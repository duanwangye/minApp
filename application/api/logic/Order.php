<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/09
 * Company:财联集惠
 */

namespace app\api\logic;
use app\api\controller\H5;
use app\core\model\Master;
use app\core\model\Order as Model;
use app\core\model\OrderShare;
use app\core\model\Product;
use app\core\model\Profits;
use app\core\model\User;
use app\core\model\UserProgram;
use app\core\model\Verification;
use app\core\service\Finance;
use tool\Common;

class Order extends Base
{

    /*
     * @name 下单
     * @param userID 用户ID
     * @param productID 产品ID
     * @param count 购买数量
     * @param order_type 订单类型   1 普通订单 2 促销订单 （不可退款）
     */
    public function placeOrder(){
      /* $this->app = [
            'productID' => '14',  //产品ID
            'count' => '5',  //购买数量
            'money' => '500',  //订单金额
            'userID' => '1',  //付款用户ID
            'status' => '-1',  //订单状态
            'addressID' => '1',  //收货地址
        ];*/

       //判断y用户是否实名认证
        $userInfo = User::where('userID',$this->app['userID'])->find();

        if (!$userInfo['trueName']){
            return Common::rm(-1008,'请完善个人信息');
        }


        if ($this->app['count'] <= 0){
            return Common::rm(-101,'订单数量有误，请重新核定订单');
        }
        $this->app['status'] = '-1';
        //根据产品状态判断订单类型
        $product = Product::get(['productID',$this->app['productID']]);

        if ($product['stock'] < $this->app['count']) {
            return Common::rm(-102,'库存不足');
        }

        if ($product['status'] == 0) {
            return Common::rm(-103,'商品已下架');
        }
        //库存减下单数量
        $product->stock = $product['stock'] - $this->app['count'];
        $product->save();

        $order_type = 1;
        if ($product['promType'] != 0){
            $order_type = 2;
        }
        //将订单保存到数据库
        $this->app['tradeNo'] =  Common::orderNo();  //交易订单号
        $this->app['order_type'] = $order_type;  //订单类型
        //验证订单金额
        if (($product['salePrice']*$this->app['count']) != $this->app['money']){
            $this->app['money'] = $product['salePrice']*$this->app['count'];
        }
        $order = Model::create($this->app);
        //根据订单商品数量生成核销码
        $verificationCode = [];
        for ($i=0;$i < $this->app['count'];$i++){
            $verificationCode[$i]['verificationCode'] = Common::orderNoSecond();
            $verificationCode[$i]['orderID'] = $order->orderID;
            $verificationCode[$i]['masterID'] = $product['masterID'];
            $verificationCode[$i]['status'] = 0;
        }

        $model = new Verification();

        $res = $model->saveAll($verificationCode);

      //查询当前分润比例
        $profits = Profits::get(1);
        //计算分润金额
        $shareMoney = ($product['commission']*$this->app['count']);   //佣金
        $costPrice = $product['costPrice']*$this->app['count'];   //成本价
        $salePrice = $product['salePrice']*$this->app['count'];   //销售价
        $agentPrice = $product['agentPrice']*$this->app['count'];   //代理价
        $firstShare = $shareMoney*$profits['lv1'];     //分润金额
        $secondShare = $shareMoney*$profits['lv2'];
        $thirdShare = $shareMoney*$profits['lv3'];
        $profits = ($salePrice-$shareMoney-$costPrice-$agentPrice);
        if($profits <= 0){
            $profits = 0;
        }
        //根据用户查询父级用户，填写分润金额
        //得到用户代理商ID及一级父级ID
        $agent = UserProgram::where('userID',$this->app['userID'])->find();
        $agentID = $agent['agentId'];    //该用户代理商
        $parentFirst = 1;  //一级
        $parentSecond = 1;  //二级
        $parentThird = 1;   //三级
        if ($agent['parentID'] != 0){
            $parentFirst = $agent['parentID'];  //一级
            $second = UserProgram::where('userID',$parentFirst)->find();
            if ($second){
                if ($second['parentID'] !=0){ //查找三级分类
                    $parentSecond = $second['parentID'];
                    $third = UserProgram::where('userID',$parentSecond)->find();
                    if ($third['parentID'] !=0){
                        $parentThird = $third['parentID'];
                    }
                }
            }
        }
        //更新用户金额
        //earnings(收益  累计) money（余额 冻结+可用） noMoney（冻结金额 未核销金额） yesMoney（可用金额）
        self::userInfo($parentFirst,$firstShare);
        self::userInfo($parentSecond,$secondShare);
        self::userInfo($parentThird,$thirdShare);
        //代理商分润
        $masterAgent =Master::get($agentID);
        $masterAgent->noMoney = $masterAgent['noMoney']+$agentPrice;
        $masterAgent->save();
        //self::userInfo($agentID,$agentPrice);

        //根据购买信息进行分润 并将分润保存到分润表
        $share = [
            'orderID' => $order->orderID,
            'shareOne' => $firstShare,
            'shareTwo' => $secondShare,
            'sharethree' => $thirdShare,
            'profits' => $profits,           //平台利润
            'agentPrice' => $agentPrice,         //代理商利润
            'commission' => $shareMoney,         //代理商利润
        ];
        $orderShare = OrderShare::create($share);

        if ($orderShare){
            return Common::rm(1,'操作成功',['orderID' => $order->orderID]);
        }

    }



    /*
     * @name 得到订单列表
     * @param status  -1-未支付（待支付）  1-已支付（待核销） -2支付失败 2已退款 3 已完成 -3已取消
     */
    public function getOrderList(){
        $this->app['pageIndex'];
        $this->app['pageItemCount'];
        $map = [];
        if (isset($this->app['status']) && $this->app['status']){
            $map['status'] = $this->app['status'];
        }
        $map['userID'] = $this->app['userID'];
        $orderList = Model::with('productinfo')->where($map)->order('addTime desc')->limit(($this->app['pageIndex'] -1)*$this->app['pageItemCount'],$this->app['pageItemCount'])->select();

        if ($orderList){
            $orderList->visible(['orderID','status','count','money','productinfo' => ['productName','titleImg']]);
        }

        return  Common::rm(1,'操作成功',$orderList);
    }

    /*
     * @name 得到订单详情
     * @param orderID 订单ID
     * @return 订单详情
     */
    public function getOrderDetail(){
     //   $this->app['orderID'] = 360;
        $detail = Model::with(['productinfo','vouchers','address'])->where('orderID',$this->app['orderID'])->find();
        $vouchers = [];
        $data = [];
        if ($detail){
            foreach ($detail['vouchers'] as $k => $val){
                preg_match('/([\D]*)([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $val['verificationCode'],$match);
                unset($match[0]);
                $verificationCode =  implode(' ', $match);
                $vouchers[$k]['verificationCode'] = $verificationCode;
                $vouchers[$k]['status'] = $val['status'];
            }
            if(!$detail['paytime']){
                $detail['paytime'] =  '待付款';
            }
            $data = [
                'order' => [
                    'orderID' => $detail['orderID'],
                    'tradeNo' => $detail['tradeNo'],
                    'money' => $detail['money'],
                    'status' => $detail['status'],
                    'count' => $detail['count'],
                    'paytime' => $detail['paytime'],
                ],
                'product' => [
                    'contentImg' => json_decode($detail['productinfo']['contentImg'],true),
                    'useRules' => $detail['productinfo']['useRules'],
                    'validity' => $detail['productinfo']['validity'],
                    'productName' => $detail['productinfo']['productName'],
                    'promType' => $detail['productinfo']['promType'],
                    'productID' => $detail['productinfo']['productID'],
                ],
                'vouchers' => $vouchers,
                'address' => [
                    'consignee' => $detail['address']['consignee'],
                    'address' => $detail['address']['address'],
                    'mobile' => $detail['address']['mobile'],
                ]
            ];
        }
       return Common::rm(1,'操作成功',$data);
    }

    /*
     * @name 调起支付
     * @param  tradeNo 订单号
     * @return  支付结果
     */
    public function payment(){
        //$this->app['tradeNo'] = 'DHMAC154409866286026404636309P';
        //第一步 根据订单号得到订单信息
        $order = Model::where('tradeNo',$this->app['tradeNo'])->find();
        $user = User::where('userID',$order['userID'])->find();
        if ($order['status'] != -1){
            return Common::rm(-1005,'该订单有误，请查询支付状态');
        }
        $order_time = time() - strtotime($order['addTime']);

        if (config('system.order_time') <= $order_time  ) {
            return Common::rm(-1006,'支付超时，支付失败');
        }
        $options = [
            'appid' => 'wx1054d0223280f3f2',
            'mch_id' => '1518616481',
            'body' => '来客商城',
            'out_trade_no' => $this->app['tradeNo'],//商户订单号
            'nonce_str' => Common::getRandomStr(),//随机字符串
            'total_fee' => $order['money'] * 100,
            'notify_url' => 'https://api.lktehui.com/api/h5/notify',
            'trade_type' => 'JSAPI',
            'spbill_create_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0',
            'openid' => $user['openID'],
        ];
        $res = Common::curl_post_ssl1('https://api.mch.weixin.qq.com/pay/unifiedorder', $options);
        $res = Common::xmltoarray($res);

        $time = time();
        $paySign = MD5('appId=wx1054d0223280f3f2&nonceStr='.$res['nonce_str'].'&package=prepay_id='.$res['prepay_id'].'&signType=MD5&timeStamp='.$time.'&key=lk2018th12061504dh16925636d6a6dg');

        $res =  [
            'timeStamp'=>$time,
            'nonceStr'=>$res['nonce_str'],
            'package'=>'prepay_id='.$res['prepay_id'],
            'signType'=>'MD5',
            'paySign'=>$paySign
        ];

        return Common::rm(1,'操作成功',$res);

//        vendor('payModel.Trade');
//        $finance = new Finance();
//        $trade = new \Trade();
//        $trade->setMoney($order['money']);
//        $trade->setTradeNo( $this->app['tradeNo']);
//        $result = $finance->order($msg,$trade);
//
//        if ($result['status'] == 1){
//            //$str = strstr($result['data']['rqcode_url'],'.com');
//           // $str = strstr($url,':');
//            return Common::rm(1,'操作成功',['payUrl' =>$result['data']['rqcode_url']]);
//          /*  $h5 = new H5();
//            $h5->jspay($result['data']['rqcode_url']);*/
//
//        }
       /* if ($result['status'] == 0){  //支付失败 改变订单状态
            $order->status = '-2';
            $order->save();
            return Common::rm(-1009,$result['info']);
        }*/
    }

    /*
  * @name 更新用户余额
  * @param userID 用户ID
  * @param firstShare 新增金额
  */
    public static function userInfo($userID,$firstShare){
        $user = UserProgram::get(['userID' => $userID]);
        $user->earnings = $user['earnings'] + $firstShare;
        //$user->money = $user['money'] + $firstShare;
        $user->noMoney = $user['noMoney'] + $firstShare;
        // $user->yesMoney = $user['yesMoney'] + $firstShare;
        $user->save();
    }

    /*
     * @name 取消订单（删除订单）
     * @param orderID 订单号
     */
    public function cancelTradeNo(){
        if (!$this->app['orderID']){
            return Common::rm(-2,'删除失败');
        }

        $order_info = \app\core\model\Order::get($this->app['orderID']);

        //未支付订单，取消订单，商品回库
        if ($order_info['status'] == -1) {
            $product_info = \app\core\model\Product::get($order_info['productID']);
            $product_info->stock = $product_info['stock'] + $order_info['count'];
            $product_info->save();
        }
        //删除订单
        Model::destroy($this->app['orderID']);
        //删除分润数据
        OrderShare::where('orderID','in',$this->app['orderID'])->delete();
        //删除核销码
        Verification::where('orderID','in',$this->app['orderID'])->delete();
        return Common::rm(1,'操作成功');

    }

    /*
     * @name 退款
     * @tradeNo  订单号  根据订单号查询产品 判断是否可以退款
     * @promType  0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠,4预售    （普通订单才可退款）
     * @status 订单状态  1  可退款
     */
    public function refund(){
        //$this->app['tradeNo'] = 'DHMAC154414588386438185811441P';
        //根据订单号查询订单
        $order = Model::where('tradeNo',$this->app['tradeNo'])->find();

        if ($order['status'] !=1){
            return Common::rm(-2011,'该订单无法退款！请检查订单状态');
        }
        //查询产品信息
        $product = Product::where('productID',$order['productID'])->find();
        if ($product['promType'] !=0){
            $promType = ['1'=>'限时抢购','2'=>'团购','3'=>'促销优惠','4'=>'预售'];
            return Common::rm(-2022,'该产品属于'.$promType[$product['promType']].'产品！暂时不支持退款');
        }

        //存在核销码已经核销过的订单无法退款
        $vList = Verification::where(['orderID'=>$order['orderID'],'status' => 1])->find();

        if ($vList) {
            return Common::rm(-2024,'存在核销码已经核销，无法退款');
        }


        //商品回库
        $product_info = \app\core\model\Product::get($order['productID']);
        $product_info->stock = $product_info['stock'] + $order['count'];
        $product_info->save();

        //查询订单用户
        $UserProgram = UserProgram::where('userID',$order['userID'])->find();
        //删除 该订单产生的分润
        OrderShare::destroy(['orderID'=> $order['orderID']]);
        //删除 该订单产生的核销码
        Verification::where('orderID','in',$order['orderID'])->delete();
        //修改订单状态 成功以后尽心用户余额修改
        $order->status = '2';
        $order->save();
        if ($order){
            $UserProgram->money = $UserProgram['money'] + $order['money'];
            $UserProgram->save();
            if ($UserProgram){
                return Common::rm(1,'退款成功');
            }
        }

        return Common::rm(-2023,'退款失败');
    }


}