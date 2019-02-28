<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\Product as Logic;

class Product extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //获取产品列表
   public function getlist() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    //新增/修改产品
    public function setProduct() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //获取产品详细信息
    public function getinfo() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }


    //通过/驳回代理商产品信息
    public function informationModify() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }


    //代理商提交的产品信息列表
    public function informationList() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function setStatus(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getMasterList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function delProduct(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}