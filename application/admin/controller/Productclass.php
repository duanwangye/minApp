<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\Productclass as Logic;

class Productclass extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //获取分类信息
   public function getlist() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    //新增分类信息
    public function add() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //修改分类信息
    public function modify() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    //删除分类信息
    public function delete() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getinfo(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function updateStatus(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getSecondList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function addClass(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function delClass(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}