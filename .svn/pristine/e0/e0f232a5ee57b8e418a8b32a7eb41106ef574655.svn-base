<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/11/10
 * Time: 13:35
 */
namespace app\admin\controller;

use app\admin\logic\Profits as Logic;
class Profits extends Base {
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //添加分润
    public function addProfits() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //添加分润
    public function getinfo() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}