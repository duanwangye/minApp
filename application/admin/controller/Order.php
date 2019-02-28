<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\Order as Logic;

class Order extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    public function getlist() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //获取订单统计信息
    public function getCount() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //获取订单统计信息
    public function statistics() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function verification(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getOrderDetail(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getCompleteCode(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}