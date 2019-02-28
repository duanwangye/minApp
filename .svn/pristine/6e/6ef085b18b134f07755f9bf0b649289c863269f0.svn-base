<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/05
 * Company:财联集惠
 */

namespace app\api\controller;
use app\api\logic\Product as Logic;

class Product extends Base
{

    public function getProductDetail(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function reward(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }
}