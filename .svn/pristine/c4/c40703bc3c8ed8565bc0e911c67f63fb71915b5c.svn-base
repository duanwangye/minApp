<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\supplier\logic;

use app\core\model\Product as Model;
use think\Db;
use tool\Common;
class Product extends Base
{

    //修改产品信息
   public function modify() {
       $params_where['productID'] = $this->app['productID'];
       $product_model = new Model();
       $product_info = $product_model->where($params_where)->find();

       $params['status'] = $this->app['status'];

       //商品状态为分享，无法私自上架，需由总平台修改
       if($product_info['status'] == config('status.product_status_share') && $params['status'] == config('status.product_status_putaway')){
           return Common::rm(-1,'分享商品无法上架，请联系管理员');
       }

       $params['stock'] = $this->app['stock'];  //库存
       $save_result = $product_model->save($params,$params_where);

       if ($save_result){
           return Common::rm(1,'修改成功');
       }else{
           return Common::rm(-1,'修改失败');
       }
   }

   public function getlist() {
       $page = $this->app['page'];//当前页数
       $size = $this->app['size'];//每页显示数量

       $params_where = [];
       if( isset($this->app['productName']) ) {
           $params_where['product.productName'] = ['like','%'.$this->app['productName'].'%'];//产品名称查询
       }

       if (isset($this->app['status'])){
           $params_where['product.status'] = $this->app['status'];//产品状态查询
       }

       if (isset($this->app['classID'])){
           $params_where['class.classID'] = $this->app['classID'];//分类查询
       }

       $params_where['product.masterID'] = $this->master['masterID'];

       $list = Db::view('product',['productID','productName','costPrice','agentPrice','salePrice','commission','marketPrice','promType','salesSum','status','commentCount'])
           ->view('class','classID,className','product.classID = class.classID')
           ->where($params_where)
           ->limit(($page-1)*$size, $size)
           ->select();

       $count = Db::view('product')
           ->view('class','classID','product.classID = class.classID')
           ->view('master',['trueName' => 'masterName'],'product.masterID = master.masterID')
           ->where($params_where)
           ->count();

       return Common::rm(1, '操作成功', [
           'page'=>$page,
           'count'=>$count,
           'list'=>$list
       ]);
   }

}