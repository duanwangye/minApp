<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\supplier\controller;

use app\supplier\logic\Verification as Logic;

class Verification extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    public function verification() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

//    获取验证信息
    public function verificationInfo() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}