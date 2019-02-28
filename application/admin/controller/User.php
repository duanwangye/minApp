<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\User as Logic;

class User extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //获取用户列表
   public function getlist() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    //获取用户详细信息
    public function getinfo() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //修改用户信息
    public function modify() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //删除用户信息
    public function delete() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function associatedAccount(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

}