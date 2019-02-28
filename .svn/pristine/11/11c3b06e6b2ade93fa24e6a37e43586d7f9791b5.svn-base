<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\supplier\logic;

use app\core\model\Order as Model;
use app\core\model\Product;
use think\Db;
use tool\Common;

class Order extends Base
{
    /*
    * @name 订单列表
    * @param page  页数
    * @param size  每页个数
    * @param status  0-未支付（待支付）  1-已支付（待核销） -1支付失败 2已退款 3 已完成
    * @param productName  产品名称
    * @param beginTime  开始时间
    * @param endTime  结束时间
    */
    public function getlist()
    {
        $page = $this->app['page'];//当前页数
        $size = $this->app['size'];//每页显示数量

        //查找供应商下的全部产品
        $params_master_where['masterID'] = $this->master['masterID'];//供应商ID

        $productid_arr = Product::where('masterID',$params_master_where['masterID'])
            ->column('productID');

        $today = strtotime(date('Y-m-d', time())); //今天
        $begin_time = isset($this->app['beginTime']) ? strtotime(isset($this->app['beginTime'])) : 0;
        $end_time   = isset($this->app['endTime']) ? strtotime(isset($this->app['endTime'])) : $today+86400;
        $params_where['order.addTime'] = array(
            array('egt', $begin_time),
            array('lt', $end_time)
        );

        if( isset($this->app['productName']) ) {
            $params_where['product.productName'] = ['like','%'.$this->app['productName'].'%'];
        }

        if (isset($this->app['status'])){
            $params_where['order.status'] = $this->app['status'];
        }

        $params_where['order.productID'] = array('in',$productid_arr);

        $list = Db::view('order',['orderID','count','tradeNo','pay_type','order_type','money','userID','paytime','status'])
            ->view('product','productName,productID','order.productID = product.productID')
            ->view('user',['nickname' => 'userName'],'order.userID = user.userID','LEFT')
            ->where($params_where)
            ->limit(($page-1)*$size, $size)
            ->select();

        $count = Db::view('order')
            ->view('product','productName,productID','order.productID = product.productID')
            ->view('user',['nickname' => 'userName'],'order.userID = user.userID','LEFT')
            ->where($params_where)
            ->count();


        return Common::rm(1, '操作成功', [
            'page'=>$page,
            'count'=>$count,
            'list'=>$list
        ]);

    }

}