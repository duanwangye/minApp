<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\controller;

use app\agent\logic\Product as Logic;

class Product extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //提供代理商产品信息
    public function addProductInformation() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //驳回代理商产品信息
    public function getRejectInfo() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //代理商提交的产品信息列表
    public function informationList() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}