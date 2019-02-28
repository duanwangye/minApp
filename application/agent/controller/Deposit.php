<?php
namespace app\agent\controller;

use app\agent\logic\Deposit as Logic;
//代理商提现控制器
class Deposit extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //代理商提现
   public function withdrawDeposit() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }
}