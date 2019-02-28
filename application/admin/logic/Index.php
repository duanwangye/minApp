<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/12/5
 * Time: 18:00
 */


namespace app\admin\logic;

use app\core\model\Verification;
use tool\Common;

class Index extends Base {

    /*
     * 成交单数统计
     * */
    public function index() {
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));

        $order_model = new \app\core\model\Order();

        $params_today_where['paytime'] = array(
            array('egt', $beginToday),
            array('lt', $endToday)
        );

        $params_month_where['paytime'] = array(
            array('egt', $beginThismonth),
            array('lt', $endThismonth)
        );

        $params_all_where['paytime'] = array('egt', 1);

        $today_pay_count = $order_model->where($params_today_where)->count();   //今日支付订单数
        $today_pay_money = $order_model->where($params_today_where)->sum('money');   //今日支付总额
        $today_pay_money = Common::price2($today_pay_money/100);

        $month_pay_count = $order_model->where($params_month_where)->count();   //当月支付订单数
        $month_pay_money = $order_model->where($params_month_where)->sum('money');   //当月支付总额
        $month_pay_money = Common::price2($month_pay_money/100);

        $all_pay_count = $order_model->where($params_all_where)->count();   //总支付订单数
        $all_pay_money = $order_model->where($params_all_where)->sum('money');   //交易总额
        $all_pay_money = Common::price2($all_pay_money/100);

        return Common::rm(1, '查询成功', [
            'today_pay_count' => $today_pay_count,//今日支付订单数
            'today_pay_money' => $today_pay_money,//今日支付总额
            'month_pay_count' => $month_pay_count,//当月支付订单数
            'month_pay_money' => $month_pay_money,//当月支付总额
            'all_pay_count' => $all_pay_count,//总支付订单数
            'all_pay_money' => $all_pay_money//交易总额
        ]);
    }

    /*
     * 表单统计信息
     * beginTime 开始时间
     * endTime 结束时间
     * */
    public function statistics() {
        $today = strtotime(date('Y-m-d', time())); //今天
        $start_date = isset($this->app['beginTime']) ? strtotime($this->app['beginTime']) : $today - 14*86400;
        $end_date   = isset($this->app['endTime']) ? (strtotime($this->app['endTime'])+1) : $today + 86400;
        $count_day  = ($end_date-$start_date)/86400; //查询最近n天
        $this_object = new \app\core\model\Order();
        $user_object = new \app\core\model\User();
        for($i = 0; $i < $count_day; $i++){
            $day = $start_date + $i*86400; //第n天日期
            $day_after = $start_date + ($i+1)*86400; //第n+1天日期
            $map['paytime'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );
            $user_map['addTime'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );


            $result_date[] = date('m月d日', $day);
            $result_count[] = (int)$this_object->where($map)->count();
            $money = $this_object->where($map)->sum('money');
            $result_money[] = Common::price2($money/100);
            $result_user_count[] = $user_object->where($user_map)->count();
        }

        return Common::rm(1, '查询成功', [
            'result_date' => $result_date,
            'result_count' => $result_count,
            'result_money' => $result_money,
            'result_user_count' => $result_user_count
        ]);
    }

    //核销系统统计
    public function masterVerification() {

        //昨日时间戳
        $beginYesterday = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

        //今日时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        $varification_model = new Verification();
        $params_yestoday['masterID'] = $this->app['masterID'];

        $params_yestoday['verificationTime'] = array(
            array('egt', $beginYesterday),
            array('lt', $endYesterday)
        );

        $params_today['masterID'] = $this->app['masterID'];
        $params_today['verificationTime'] = array(
            array('egt', $beginToday),
            array('lt', $endToday)
        );

        $today_count = $varification_model->where($params_today)->count();//今日核销数
        $yestoday_count = $varification_model->where($params_yestoday)->count(); //昨日核销数


        $params_all['masterID'] = $this->app['masterID'];
        $params_all['status'] = 1;
        $all_count = $varification_model->where($params_all)->count(); //总核销数

        return Common::rm(1, '查询成功', [
            'today_count' => $today_count,//今日核销数
            'yestoday_count' => $yestoday_count,//昨日核销数
            'all_count' => $all_count//总核销数
        ]);

    }

}
