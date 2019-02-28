<?php

namespace app\admin\controller;
use app\admin\logic\Upload as Logic;
class Upload extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    public function upload() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

}
