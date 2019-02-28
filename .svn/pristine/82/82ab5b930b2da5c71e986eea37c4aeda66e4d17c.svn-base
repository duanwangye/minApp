<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\logic;

use app\core\model\Order as Model;
use think\Db;
use tool\Common;

class Order extends Base
{
    /*
     * @name 订单列表
     * @param page  页数
     * @param size  每页数量
     * @param status  0-未支付（待支付）  1-已支付（待核销） -1支付失败 2已退款 3 已完成
     * @param productName  产品名称
     * @param beginTime  开始时间
     * @param endTime  结束时间
     */
    public function getlist()
    {
        $page = $this->app['page'];//当前页数
        $size = $this->app['size'];//每页显示数量

        $today = strtotime(date('Y-m-d', time())); //今天
        $begin_time = isset($this->app['beginTime']) ? strtotime(isset($this->app['beginTime'])) : 0;
        $end_time   = isset($this->app['endTime']) ? strtotime(isset($this->app['endTime'])) : $today+86400;

        $params_where['order.addTime'] = array(
            array('egt', $begin_time),
            array('lt', $end_time)
        );
        $params_where['order.agentId'] = $this->master['masterID'];//代理商ID

        if( isset($this->app['productName']) ) {
            $params_where['product.productName'] = ['like','%'.$this->app['productName'].'%'];
        }

        if (isset($this->app['status'])){
            $params_where['order.status'] = $this->app['status'];
        }

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

    //获取首页统计信息
    public function getCount() {
        $year = date("Y",time());
        $month = date("m",time());
        $t1 = mktime(0,0,0,$month,1,$year); // 创建本月开始时间

        $today = strtotime(date('Y-m-d'),time()); //今天
        $month = strtotime(date("Y-m-d H:i:s",$t1)); //本月月初时间

        $order_model = new Model();
        $params_today_where['addTime'] = array('egt', $today);
        $params_today_where['agentId'] = $this->master['masterID'];//代理商ID

        $params_month_where['addTime'] = array('egt', $month);
        $params_month_where['agentId'] = $this->master['masterID'];//代理商ID

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
            'month_pay_count' => $month_pay_count,
        ]);

    }
}