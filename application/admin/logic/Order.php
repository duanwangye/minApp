<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use app\core\model\Master;
use app\core\model\Order as Model;
use app\core\model\OrderShare;
use app\core\model\UserProgram;
use app\core\model\Verification;
use think\Db;
use tool\Common;

class Order extends Base
{

    public function getlist()
    {
     /* $this->app['page'] = 1;//当前页数
      $this->app['size'] = 22;//每页显示数量*/
      $map = [];
      //$this->app['masterID'] = 17;
      if (isset($this->app['masterID']) && $this->app['masterID']){
            $map['product.masterID'] = $this->app['masterID'];
        }
        /* $list = Model::with(['productinfo' => function($query) use ($map){$query->where($map);},'user'=>function($query){$query->field('nickname,userID');}])
           ->limit(( $this->app['page']-1)*$this->app['size'],$this->app['size'])->select();
       if ($list){
           $list->visible(['orderID','count','tradeNo','order_type','money','paytime','status','addTime','productinfo' => [
               'productName'],'user' => ['nickname']]);
       }

       $count = Model::with(['productinfo','user'=>function($query){$query->field('nickname,userID');}])->count();*/
        $list = Db::view('order','orderID,tradeNo,count,order_type,money,paytime,status,addTime')
           ->view('product','productName','order.productID = product.productID')
           ->view('user','nickname','order.userID = user.userID')
           ->view('master','trueName','product.masterID = master.masterID')
           ->view('user_address','consignee,address,mobile','user_address.addressID = order.addressID','LEFT')
           ->limit(( $this->app['page']-1)*$this->app['size'],$this->app['size'])
           ->where($map)
           ->select();
        $count = Db::view('order','orderID,count,order_type,money,paytime,status,addTime')
            ->view('product','productName','order.productID = product.productID')
            ->view('user','nickname','order.userID = user.userID')
            ->view('master','trueName','product.masterID = master.masterID')
            ->view('user_address','consignee,address,mobile','user_address.addressID = order.addressID','LEFT')
            ->limit(( $this->app['page']-1)*$this->app['size'],$this->app['size'])
            ->where($map)
            ->count();
        foreach ($list as $k => $value){
            $list[$k]['productinfo']['productName'] = $value['productName'];
            $list[$k]['user']['nickname'] = $value['nickname'];
            $list[$k]['addTime'] = date('Y-m-d H:i',$value['addTime']);
            $list[$k]['money'] = Common::price2($value['money']/100);
            $list[$k]['paytime'] = null;
            if ($value['paytime']){
                $list[$k]['paytime'] = date('Y-m-d H:i',$value['paytime']);
            }
            unset($list[$k]['productName']);
            unset($list[$k]['nickname']);
        }
        return Common::rm(1, '操作成功', [
            'count'=>$count,
            'list'=>$list
        ]);

    }

    /*
     * @name  得到订单详情
     * @param orderID 订单ID
     */
    public function getOrderDetail(){
        //$this->app['orderID'] = 313;
        $verification = Verification::where('orderID',$this->app['orderID'])->select();
        if ($verification){
            $verification->append(['statusText'])->visible(['verificationCode','status']);
        }
        foreach ($verification as $k => $val){
            $verification[$k]['verificationCode'] =substr_replace($val['verificationCode'],'****',3,6);
        }
        return Common::rm(1,'操作成功',$verification);
    }

    //获取首页统计信息
    public function getCount() {
        $year = date("Y",time());
        $month = date("m",time());
        $t1 = mktime(0,0,0,$month,1,$year); // 创建本月开始时间

        $today = strtotime(date('Y-m-d'),time()); //今天
        $month = strtotime(date("Y-m-d H:i:s",$t1)); //本月月初时间

        $order_model = new Model();
        $params_today_where['addTime'] = array('egt', $today);

        $params_month_where['addTime'] = array('egt', $month);

        $today_count = $order_model->where($params_today_where)->count();   //今日总订单数
        $month_count = $order_model->where($params_month_where)->count();   //当月总订单数

        $params_today_where['status'] = config('status.order_status_pay');
        $today_pay_count = $order_model->where($params_today_where)->count();   //今日支付订单数

        $params_month_where['status'] = config('status.order_status_pay');
        $month_pay_count = $order_model->where($params_month_where)->count();   //当月支付订单数

        return Common::rm(1, '查询成功', [
            'today_count' => $today_count,
            'month_count' => $month_count,
            'today_pay_count' => $today_pay_count,
            'month_pay_count' => $month_pay_count
        ]);

    }

    //表单统计信息
    public function statistics() {
        $today = strtotime(date('Y-m-d', time())); //今天
        $start_date = isset($this->app['beginTime']) ? strtotime($this->app['beginTime']) : $today - 14*86400;
        $end_date   = isset($this->app['endTime']) ? (strtotime($this->app['endTime'])+1) : $today + 86400;
        $count_day  = ($end_date-$start_date)/86400; //查询最近n天
        $this_object = new Model();
        for($i = 0; $i < $count_day; $i++){
            $day = $start_date + $i*86400; //第n天日期
            $day_after = $start_date + ($i+1)*86400; //第n+1天日期
            $map['addtime'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );
            $map['status'] = 1;

            $result_date[] = date('m月d日', $day);
            $result_count[] = (int)$this_object->where($map)->count();
            $result_sum[] = $this_object->where($map)->sum('money');
        }

        return Common::rm(1, '查询成功', [
            'result_date' => $result_date,
            'result_count' => $result_count,
            'result_sum' => $result_sum
        ]);
    }


    /*
 * @name 供应商核销
 * @param 核销码
 * @return  返回核销结果
 */
    public function verification(){
      //  $this->app['verificationCode'] = 'GB305700155706260003'; // 核销码

        //根据核销码查询
        $vcode =  Verification::where('verificationCode',$this->app['verificationCode'])->find();

        if (!$vcode){
            return Common::rm(-1009,'不存在该核销码');
        }
        if ($vcode['status'] == 1){
            return Common::rm(-1010,'该核销码已核销');
        }

        //根据订单号查询当前订单是否支付
       $order =  Model::get($vcode['orderID']);
        if ($order['status'] != 1){
            return Common::rm(-1011,'该码无效，请核定订单状态');
        }

        //更改当前核销码状态
        $vcode->status = 1;
        $vcode->updataTime = time();
        $vcode->save();
        //得到该订单状态，如果还有待核销的则不进行反润及订单状态修改
        $vList = Verification::where(['orderID'=>$vcode['orderID'],'status' => 0])->find();
        if (!$vList){  //不存在，该订单已经全部核销 进行订单结算
            $share = OrderShare::where('orderID',$vcode['orderID'])->find();  //分润列表
            $user = Model::where('orderID',$vcode['orderID'])->find();      //用户信息列表
            //更改订单状态
            $order = Model::where('orderID',$vcode['orderID'])->update(['status' => '3']);
            //查询父级进行分润
            $parentFirst = 1;  //一级
            $parentSecond = 1;  //二级
            $parentThird = 1;   //三级
            $agent = UserProgram::where('userID',$user['userID'])->find();
            $agentID = $agent['agentId'];  //代理商ID
            if ($agent['parentID'] !=0){
                $parentFirst = $agent['parentID'];
                $second = UserProgram::where('userID',$parentFirst)->find();
                if ($second['parentID'] !=0){
                    $parentSecond = $second['parentID'];
                    $third = UserProgram::where('userID',$parentSecond)->find();
                    if ($third['parentID'] !=0){
                        $parentThird = $third['parentID'];
                    }
                }
            }
            self::updateMoney($parentFirst,$share['shareOne']);
            self::updateMoney($parentSecond,$share['shareTwo']);
            self::updateMoney($parentThird,$share['sharethree']);
            //代理商
            //代理商分润
            $masterAgent =Master::get($agentID);
            $masterAgent->noMoney = $masterAgent['yesMoney']+$share['agentPrice'];
            $masterAgent->save();
           // self::updateMoney($agentID,$share['agentPrice']);
        }
        return Common::rm(1,'核销成功');
    }

    /*
     * @name 核销完成 修改用户金额
     * @param userID
     * @money 金额
     * @param earnings(收益累加) money余额(充值 退款（无税收）)  noMoney 冻结余额 yesMoney 可用金额（可提现金额（税收））
     */
    public static function updateMoney($usrID,$money){
        $user= UserProgram::get(['userID' => $usrID]);
        $user->noMoney = $user['noMoney'] - $money;
        $user->yesMoney = $user['yesMoney'] + $money;
        $user->updataTime = time();
        $user->save();

    }

    /*
     * @name 供应商得到已核销
     * @param masterID
     */
    public function getCompleteCode(){
       // $this->app['masterID'] = '26' ;

        $list = Db::view('verification')
            ->view('order','count','verification.orderID = order.orderID')
            ->view('product','productName','order.productID = product.productID')
            ->where(['verification.masterID'=>$this->app['masterID'],'verification.status' => 1])
            ->limit(( $this->app['page']-1)*$this->app['size'],$this->app['size'])
            ->select();

        $count = Db::view('verification')
            ->view('order','count','verification.orderID = order.orderID')
            ->view('product','productName','order.productID = product.productID')
            ->where(['verification.masterID'=>$this->app['masterID'],'verification.status' => 1])
            ->limit(( $this->app['page']-1)*$this->app['size'],$this->app['size'])
            ->count();

        foreach ($list as $k => $val){
            $list[$k]['addTime'] = date('Y-m-d H:i',$val['addTime']);
            unset($list[$k]['updataTime']);
            unset($list[$k]['count']);
            unset($list[$k]['masterID']);
            unset($list[$k]['orderID']);
            unset($list[$k]['verificationID']);
        }

        return Common::rm(1,'操作成功',['list' => $list,'count'=>$count]);
    }


}