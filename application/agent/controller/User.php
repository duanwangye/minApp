<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\controller;

use app\agent\logic\User as Logic;

class User extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    public function getTeamCount() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}