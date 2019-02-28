<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/11/10
 * Time: 13:35
 */
namespace app\admin\controller;

use app\admin\logic\Index as Logic;
class Index extends Base {
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //首页数据统计
    public function index() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function statistics() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function masterVerification() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}