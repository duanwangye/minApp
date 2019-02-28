<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/05
 * Company:财联集惠
 */

namespace app\api\controller;
use app\api\logic\Index as Logic;

class Index extends Base
{
    //首页
    public function index(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getClassList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function search(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getBannerList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }
}