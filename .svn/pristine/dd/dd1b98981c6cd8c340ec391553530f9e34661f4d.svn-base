<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\logic;

use tool\Common;

use app\core\model\ProductInformation;
use app\core\model\ProductReject;
class Product extends Base
{

    //添加供应商提供的产品信息
    public function addProductInformation() {
        $params['agentId'] = $this->master['masterID'];
        $params['productName'] = $this->app['productName'];
        $params['productPrice'] = $this->app['productPrice'];// 供应商价格
        $params['supplierName'] = $this->app['supplierName'];
        $params['supplierTel'] = $this->app['supplierTel'];
        $params['status'] = config("status.product_status_wait");

        $crate_result = ProductInformation::create($params);
        if ($crate_result){
            return Common::rm(1,'提交成功');
        }else{
            return Common::rm(-1,'提交失败');
        }

    }


    //查看驳回理由
    public function getRejectInfo() {
        $params['InformationId'] = $this->app['InformationId'];
        $information_info = ProductInformation::where('InformationId',$params['InformationId'])->find();

        if($information_info['agentId'] != $this->master['masterID']){
            return Common::rm(-1,'查看失败');
        }else{
            return Common::rm(1,'查看成功',$information_info['rejectReason']);
        }

    }

    //产品添加的信息列表
    public function informationList() {
        $page = $this->app['page'];//当前页数
        $size = $this->app['size'];//每页显示数量

        $params_where['agentId'] = $this->master['masterID'];

        if( isset($this->app['status']) ) {
            $params_where['status'] = $this->app['status'];
        }

        $product_infomation_model = new ProductInformation();

        $list = $product_infomation_model
            ->limit(($page-1)*$size, $size)
            ->field('InformationId,agentId,productName,productPrice,supplierName,supplierTel,status')
            ->where($params_where)
            ->select();
        $count = $product_infomation_model->where($params_where)->count();   //总条数

        return Common::rm(1, '操作成功', [
            'page'=>$page,
            'count'=>$count,
            'list'=>$list
        ]);
    }

}