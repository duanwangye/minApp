<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/11/10
 * Time: 13:35
 */
namespace app\admin\controller;

use app\admin\logic\Productclass as Logic;
class Auth extends Base {
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //添加角色
    public function addRole() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}