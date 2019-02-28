<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\supplier\controller;

use app\supplier\logic\Order as Logic;

class Order extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    public function getlist() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}