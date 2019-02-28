<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/09
 * Company:财联集惠
 */

namespace app\api\controller;
use app\api\logic\Order as Logic;

class Order extends Base
{

    public function getOrderList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getOrderDetail(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function placeOrder(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function addModul(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function payment(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function verification(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function jspay(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function test1() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function cancelTradeNo() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function refund(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public $logic;
    public function __initialize(){
        $this->logic = new Logic($this->request);
    }
}