<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/11/21
 * Time: 14:04
 */
namespace app\admin\controller;

use app\admin\logic\Bargain as Logic;

class Bargain extends Base{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    public function setBargain() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getlist() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function bargainirglist() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getbargainirglist() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function updateStatus() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }


}