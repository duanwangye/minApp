<?php

namespace app\api\logic;

use app\core\model\UserProgram;
use think\Db;
use think\Log;
use tool\Common;
use app\core\model\Deposit as Model;
class Deposit extends Base
{

    //提现接口地址
    const TRANSFER_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    //查询提现接口地址
    const CHECK_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';

    /*
     * 获取提现手续费
     * */
    public function getRate() {
        $type = $this->app['type'];
        if ($type == 1){
            return Common::rm(1,'获取成功',['rate' => config('system.user_deposit_rate')]);
        } else {
            return Common::rm(1,'获取成功',['rate' => 0]);
        }
    }

    /*
     * @name 用户提现接口
     * @param type 1-福利提现 2-余额提现
     * @param money 申请提现金额
     * */
    public function deposit() {
        $user_id = $this->app['userID'];
        $type = $this->app['type'];
        $apply_money = $this->app['money'];//元

        if (floor($apply_money) != $apply_money) {
            return Common::rm(-1,'金额必须为整数');
        }

        if ($apply_money < 2) {
            return Common::rm(-1,'提现最低金额为2元');
        }

        $user_info = \app\core\model\User::with('userProgram')->find(['userID' => $user_id]);

        if (!$user_info) {
            return Common::rm(-1,'用户不存在');
        }
        if (empty($user_info['mobile'])) {
            return Common::rm(-1,'请进行实名认证');
        }

        if ($type == 1) {
            $money = $user_info['user_program']['yesMoney'];
            $amount = $apply_money * (1 - config('system.user_deposit_rate'));//福利提现收手续费

            if ($apply_money > $money) {
                return Common::rm(-1,'金额有误');
            }

        } else if ($type == 2) {
            $money = $user_info['user_program']['money'] * 100;
            $amount = $apply_money;//余额提现免手续费

            if ($apply_money > $money) {
                return Common::rm(-1,'金额有误');
            }

        } else {
            return Common::rm(-1,'提现类型错误');
        }

        $openid = $user_info['openID'];
        $wx_amount = $amount * 100;
        $options = [
            'mch_appid' => config('wechat.mch_appid'),
            'mchid' => config('wechat.mchid'),
            'partner_trade_no' => Common::orderNo(),//商户订单号
            'nonce_str' => Common::getRandomStr(),//随机字符串
            'openid' => $openid,
            'amount' => $wx_amount,
            'check_name' => 'NO_CHECK',
            'spbill_create_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0',
            'desc' => '返佣，用户提现',
        ];

        //创建提现记录
        $create_res = Model::create([
            'partner_trade_no' => $options['partner_trade_no'],
            'count' => $amount,
            'money' => $apply_money,
            'paytouid' => $user_id,
            'type' => $type,
            'status' => 0,
        ]);

        if (!$create_res) {
            return Common::rm(-1,'提现失败1');
        }

        Db::startTrans();
        try {
            $user_model = UserProgram::get(['userID' => $user_id]);

            // 修改用户余额
            if ($type == 1) {
                $user_model->yesMoney = $user_model['yesMoney'] - $apply_money;
                $user_model->save();

            } else {
                $user_model->money = $user_model['money'] - $apply_money;
                $user_model->save();
            }


            //微信提现
            $res = Common::curl_post_ssl(self::TRANSFER_URL, $options);

            if ($res['code'] != 0) {
                throw new \Exception("提现失败3");
            } else {
                $res = $res['message'];
            }

            $res = Common::xmltoarray($res);

            if ($res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS') {
                // 执行提交操作
                Db::commit();
                // 更新提现记录表状态
                (new Model)->save([
                    'payment_no' => $res['payment_no'],
                    'payment_time' => strtotime($res['payment_time']),
                    'status' => 1,
                ], ['partner_trade_no' => $res['partner_trade_no']]);
                return Common::rm(1, '操作成功');

            } else if ($res['return_code'] == 'SUCCESS' && $res['result_code'] == 'FAIL') {

                $err_code_arr = ['SYSTEMERROR', 'SEND_FAILED'];
                //系统繁忙，请稍后再试与付款错误 需重新调用查单接口，查询状态
                if (in_array($res['err_code'], $err_code_arr)) {
                    $partner_trade_no = $options['partner_trade_no'];
                    $check_res = $this->checkOrder($partner_trade_no);//通过订单号查单

                    if (empty($check_res['status'])) {
                        //提现失败记入日志
                        Log::write(serialize($user_info['userID'].'---'.$options['partner_trade_no']) . '微信付款失败1' . serialize($check_res), 'deposit');
                        throw new \Exception("提现失败4");
                    }

                    //查询结果为转账失败
                    if ($check_res['status'] == 'FAILED') {
                        //提现失败记入日志
                        Log::write(serialize($user_info['userID'].'---'.$options['partner_trade_no']) . '微信付款失败2' . serialize($check_res), 'deposit');
                        throw new \Exception("提现失败5");

                    } else {
                        // 执行提交操作
                        Db::commit();

                        // 更新提现记录表状态
                        (new Model)->save([
                            'payment_no' => $check_res['detail_id'],
                            'payment_time' => strtotime($check_res['payment_time']),
                            'status' => 1,
                        ], ['partner_trade_no' => $check_res['partner_trade_no']]);
                        return Common::rm(1, '操作成功');
                    }

                } else {
                    //提现失败记入日志
                    Log::write(serialize($user_info['userID'].'---'.$options['partner_trade_no']) . '微信付款失败3' . serialize($res), 'deposit');
                    throw new \Exception("提现失败6");
                }
            } else {
                //提现失败记入日志
                Log::write(serialize($user_info['userID'].'---'.$options['partner_trade_no']) . '微信付款失败4' . serialize($res), 'deposit');
                throw new \Exception("提现失败7");
            }

        } catch (\Exception $e) {

            Db::rollback();

            //修改提现状态
            (new Model)->save([
                'status' => -1,
            ], ['partner_trade_no' => $options['partner_trade_no']]);

            return Common::rm(-1, $e->getMessage());
        }
    }

    /*
     * @name 查单
     * @param partner_trade_no 订单号
     * */
    public function checkOrder($partner_trade_no) {

        $options = [
            'mch_appid' => config('wechat.mch_appid'),
            'mchid' => config('wechat.mchid'),
            'partner_trade_no' => $partner_trade_no,//商户订单号
            'nonce_str' => Common::getRandomStr(),//随机字符串
        ];

        $res = Common::curl_post_ssl(self::CHECK_URL, $options);
        $res = Common::xmltoarray($res);
        return $res;
    }

    /*
     * 提现记录
     * */
    public function getlist() {
        $user_id = $this->app['userID'];

        $list = Db::view('deposit','count,payment_time,type,status')
            ->where(['paytouid' => $user_id])
            ->limit(($this->app['page']-1)*$this->app['size'],$this->app['size'])
            ->select();

        $type = ['1' => '福利提现','2'=> '余额提现'];
        $status = ['-1' => '失败','0' => '处理中','1'=> '已成功'];
        foreach ($list as $k => $value){
            $list[$k]['count'] = Common::price2($value['count']/100);
            $list[$k]['payment_time'] = $value['payment_time'] != 0 ? date('Y-m-d H:i:s',$value['payment_time']) : 0;
            $list[$k]['type'] = $type[$value['type']];
            $list[$k]['status'] = $status[$value['status']];
        }

        $count = Db::view('deposit','count,payment_time,type,status')
            ->where(['paytouid' => $user_id])
            ->count();
        return Common::rm(1, '操作成功', [
            'count'=>$count,
            'list'=>$list
        ]);
    }

}
