<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\supplier\controller;

use app\supplier\logic\Product as Logic;

class Product extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //修改产品信息
   public function modify() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

   //产品列表
   public function getlist() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }
}