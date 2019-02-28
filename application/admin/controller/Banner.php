<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\Banner as Logic;

class Banner extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //获取轮播图列表
   public function getlist() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    //获取轮播图信息
   public function getinfo(){
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

   //新增
   public function add() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    //修改
   public function modify() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    public function setStatus(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function delBanner(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }


}