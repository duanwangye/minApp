<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use app\core\model\Order;
use think\console\command\optimize\Config;
use think\Db;
use think\Loader;
use think\Log;
use tool\Common;
use app\core\model\Product as Model;
use app\core\model\ProductInformation;
use app\core\model\ProductReject;
use app\core\model\Master;
class Product extends Base
{

    //获取产品列表
   public function getlist() {
       $page = $this->app['page'] ;//当前页数
       $size = $this->app['size']  ;//每页显示数量
       $list = Db::view('product','productID,productName,costPrice,agentPrice,salePrice,commission,marketPrice,status,stock,addTime,promType,salesSum')
           ->view('class','className','product.classID = class.classID')
           ->view('master',['trueName' => 'agentName'],'product.agentID = master.masterID','LEFT')
           //->view(['a_master' => 'supply'],['trueName' => 'supplyName'],'product.masterID = supply.masterID','LEFT')
           ->order('addTime desc')
           ->limit(($this->app['page']-1)*$this->app['size'],$this->app['size'])
           ->select();
       $count = Db::view('product','productID,productName,costPrice,agentPrice,salePrice,commission,marketPrice,status,stock,addTime,promType,salesSum')
           ->view('class','className','product.classID = class.classID')
           ->view('master',['trueName' => 'agentName'],'product.agentID = master.masterID','LEFT')
           //->view(['a_master' => 'supply'],['trueName' => 'supplyName'],'product.masterID = supply.masterID','LEFT')
           ->limit(($this->app['page']-1)*$this->app['size'],$this->app['size'])
           ->count();
       foreach ($list as $k => $value){
           $list[$k]['costPrice'] = Common::price2($value['costPrice']/100);
           $list[$k]['agentPrice'] = Common::price2($value['agentPrice']/100);
           $list[$k]['salePrice'] = Common::price2($value['salePrice']/100);
           $list[$k]['commission'] = Common::price2($value['commission']/100);
           $list[$k]['marketPrice'] = Common::price2($value['marketPrice']/100);
           $status = ['0' => '下架','1'=>'上架','3' => '分享专用'];  //0-下架 1-上架 3-分享专用
           $promType = ['0' => '普通订单','1' => '限时抢购','2' => '团购','3'=> '促销优惠','4' => '预售','5' => '实物类'];
           $list[$k]['statusText'] = $status[$value['status']];
           $list[$k]['promType'] = $promType[$value['promType']];
           $list[$k]['addTime'] = date('Y-m-d H:i:s',$value['addTime']);
       }

       return Common::rm(1, '操作成功', [
           'count'=>$count,
           'list'=>$list
       ]);
   }

    //新增/修改产品
   public function setProduct() {
       $params['masterID'] = $this->app['masterID'];
       //$params['agentID'] = isset($this->app['agentID'])?$this->app['agentID']:config('laikeagent.default_master_id');
       $params['classID'] = $this->app['classID'];
       $params['useRules'] = $this->app['useRules'];
       $params['validity'] = $this->app['validity'];
       $params['productName'] = $this->app['productName'];
       $params['titleImg'] = $this->app['titleImg'];
       $params['media'] = json_encode($this->app['media']);
       $params['content'] = htmlspecialchars($this->app['content']);
       $params['costPrice'] = $this->app['costPrice'];
       $params['agentPrice'] = $this->app['agentPrice'];//代理价
       $params['salePrice'] = $this->app['salePrice'];
       $params['commission'] = $this->app['commission'];//佣金（返佣价）
       $params['marketPrice'] = $this->app['marketPrice'];
       $params['sort'] = $this->app['sort'];
       $params['promType'] = $this->app['promType'];
       $params['status'] = isset($this->app['status']) ? $this->app['status'] : 0;
       $params['useRules'] = $this->app['useRules'];//使用规则
       $params['validity'] = $this->app['validity'];//有效期
       $params['stock'] = $this->app['stock'];//库存


      /* //验证价格
       $price_validate = Loader::validate('PriceValidate');

       $validate_result = $price_validate->check($params);

       $master_info = Master::get([
           'masterID' => $params['masterID']
       ]);

       if($master_info['type'] != 2 ){
           return Common::rm(-1,'供应商ID错误');
       }

       if(!$validate_result){
           return Common::rm(-1,$price_validate->getError());
       }*/
       //Log::write($params,'data');
       if (isset($this->app['productID'])){
           $params_where['productID'] = $this->app['productID'];
           $model = new Model();
           $result = $model->save($params,$params_where);
       }else{
           $result = Model::create($params);
       }


       if ($result){
           return Common::rm(1,'成功');
       }else{
           return Common::rm(-1,'失败');
       }
   }

    //获取产品详细信息
    public function getinfo() {
        $model = new Model();
        $params_where['productID'] = $this->app['productID'];
        $info = $model::with(['classinfo'])
                //->field('productID,agentID,masterID,classID,content,productName,costPrice,agentPrice,salePrice,commission,marketPrice,promType,salesSum,status,commentCount')
                ->find($params_where);
       // dump($info->toArray());exit();
        if (!empty($info['content'])) {
            $info['content'] = htmlspecialchars_decode($info['content']);
        }
       //$info = $info->visible(['productID','master.trueName','masterID','titleImg','contentImg','classID','classinfo.className','productName','costPrice','agentPrice','salePrice','commission','marketPrice','promType','stock','status','commentCount']);
       $info = $info->visible(['productID','masterID','productName','titleImg','media','useRules','validity','content','costPrice','agentPrice','salePrice','commission','marketPrice','sort','promType','status','stock','classinfo.parentID','classinfo.classID']);
       $info['media'] = json_decode($info['media'],true);
        return Common::rm(1,'获取成功',$info);
    }

    //驳回产品添加的申请
    public function rejectProduct() {
        $check_reault = $this->checkStatus();

        if(!$check_reault){
            return Common::rm(-1,'操作失败');
        }

        $params['InformationId'] = $this->app['InformationId'];
        $params['reason'] = $this->app['reason'];
        $crate_result = ProductReject::create($params);

        if (!$crate_result){
            return Common::rm(-1,'驳回失败');
        }

        return $this->changeInformationStatus('product_status_reject','驳回');
    }

    //通过产品添加的申请
    public function accessProduct() {
        $check_reault = $this->checkStatus();
        if(!$check_reault){
            return Common::rm(-1,'操作失败');
        }
        return $this->changeInformationStatus('product_status_access','审核');
    }

    /*
     * @name 修改产品状态
     * @param status   产品状态
     * @param productID 产品ID
     */
    public function setStatus(){
        $model = Model::where('productID',$this->app['productID'])->update(['status' => $this->app['status']]);
        if ($model){
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }
    }

    public function checkStatus(){
        $info = ProductInformation::where('InformationId',$this->app['InformationId'])->value('status');

        if($info != config('status.product_status_wait')){
            return false;
        }else{
            return true;
        }
    }

    //改变信息状态
    public function changeInformationStatus($status,$type){
        $params_where['InformationId'] = $this->app['InformationId'];
        $params['status'] = config('status.'.$status);
        $product_infomation_model = new ProductInformation();
        $save_result = $product_infomation_model->save($params,$params_where);

        if ($save_result){
            return Common::rm(1,$type.'成功');
        }else{
            return Common::rm(-1,$type.'失败');
        }

    }

    /*
    * @name 通过/驳回产品添加的申请
    * @param InformationId  产品信息ID
    * @param status  状态 1-通过 -1-驳回
    * @param rejectReason  驳回理由
    */
    public function informationModify() {
        $key = 'jgxvrdISWpmbnlJFNMD0RUYZsEuc3KLO';
        $args = [
            'appid' => '78d69f40906679a976dc4d45cebffbe6',
            'method' => 'payment.micropay',
            'total_amount' => '0.01',
            'code' => '813161',
            'external_order_no' => '2018112000000011',
            'auth_code' => '283006522611204909',
            'notify_url' => 'www.baidu.com',
        ];

      /*  $args = [
            'appid' => '78d69f40906679a976dc4d45cebffbe6',
            'method' => 'payment.unifiedorder',
            'total_amount' => '0.01',
            'code' => '813161',
            'body' => '813161',
            'external_order_no' => '20181120-0001',

        ];*/
        ksort($args);
        $requestString = '';
        foreach($args as $k => $v) {
            if($v==null || $v==''){
                continue;
            }
            $requestString .= $k . '=' . urlencode($v).'&';
        }
        $requestString .= 'key=' . $key;
        $newSign=strtoupper(md5($requestString));
        $args['sign'] = $newSign;

        $postUrl =  'http://www.duolaibei.com/Api/Gateway.html';
        $curlPost = $args;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

       dump(json_decode($data),true);exit();
        $params['status'] = $this->app['status'];

        if ($params['status'] == config('status.product_status_reject')){
            $params['rejectReason'] = $this->app['rejectReason'];
        }

        $params_where['InformationId'] = $this->app['InformationId'];
        $information_model = new ProductInformation();

        $save_result = $information_model->save($params,$params_where);

        if ($save_result){
            return Common::rm(-1,'修改成功');
        }else{
            return Common::rm(1,'修改失败');
        }

    }

    //查看驳回理由
    public function getRejectInfo() {
        $params['InformationId'] = $this->app['InformationId'];
        $information_info = ProductInformation::where('InformationId',$params['InformationId'])->find();

        return Common::rm(1,'查看成功',$information_info['rejectReason']);

    }

    //产品添加的信息列表
    public function informationList() {
        $page = $this->app['page'];//当前页数
        $size = $this->app['size'];//每页显示数量
        $product_infomation_model = new ProductInformation();

        $list = $product_infomation_model::with('agentinfo')
            ->limit(($page-1)*$size, $size)
            ->field('InformationId,agentId,productName,productPrice,supplierName,supplierTel,status')
            ->select();
        $count = $product_infomation_model->count();   //总条数

        return Common::rm(1, '操作成功', [
            'page'=>$page,
            'count'=>$count,
            'list'=>$list
        ]);
    }

    /*
     * @name 得到代理商供应商列表
     * @param type 商户类型  1-代理商 2-供应商
     */
    public function getMasterList(){
       // $this->app['type'] =  1;
        $list = Master::where(['type'=>$this->app['type'],'status' =>1])->select();
        if ($list){
            $list->visible(['masterID','trueName']);
        }

        return Common::rm(1,'操作成功',$list);
    }

    /*
     * @name 删除产品
     * @param array productIDS  判断该产品下方是否存在订单
     */
    public function delProduct(){
        //$this->app['productIDS'] = [20];
        $order = Order::where('productID','in',$this->app['productIDS'])->select();
        if (empty($order)){
            return Common::rm(-2,'该产品存在订单中，无法删除');
        }
        $product = Model::destroy( $this->app['productIDS']);

        if ($product){
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }
    }
}