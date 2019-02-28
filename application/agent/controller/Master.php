<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\controller;

use app\agent\logic\Master as Logic;

class Master extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //账号密码登录
    public function loginByPassword() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //登出
    public function logout() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

}