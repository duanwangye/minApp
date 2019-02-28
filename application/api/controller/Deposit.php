<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\api\controller;

use app\api\logic\Deposit as Logic;

class Deposit extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

   public function deposit() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }

    public function checkOrder() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}