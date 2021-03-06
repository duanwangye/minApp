<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\admin\logic\Miniprogram as Logic;

class Miniprogram extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //修改小程序配置
   public function modify() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

   //获取小程序配置信息
   public function getinfo() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }
}