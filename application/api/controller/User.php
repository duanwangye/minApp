<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/08
 * Company:财联集惠
 */

namespace app\api\controller;
use app\api\logic\User as Logic;

class User extends Base
{
    public function setUserInfo(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getUserInfo(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getUserMoney(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function realNameSendMobileCode(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
    public function getInvitationList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getAccessToken(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getWxaCode(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getStatement(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getServicePhone(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function createAddress(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getAddress(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function delAddress(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getAddressDel(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getPosters(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

        public $logic;
    public function __initialize(){
        $this->logic = new Logic($this->request);
    }
}