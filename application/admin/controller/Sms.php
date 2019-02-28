<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\Sms as Logic;

class Sms extends Base
{
    public function changeModul(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function smsModel(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function sendSms(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function delModel(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

}