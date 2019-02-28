<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/10/10
 * Company:财联集惠
 */

namespace app\api\controller;
use app\core\exception\AppException;
use app\core\model\Master;
use think\Config;
use think\Log;
use tool\Common;

class Base
{
    public $user;
    public $data = [];
    public $request;
    public $app;


    public function __construct()
    {

        $this->request = request();
        $this->data = json_decode($this->request->getInput(), true);
        //dump( $this->data );exit();
        /*if ($this->request->action() != strtolower('setUserInfo')){
            $this->check();
        }*/

        $this->app =$this->data['app'];
        Log::write($this->data,'data');
        Log::write($this->app,'data');
        $this->request->bind('data', $this->data);
        $this->request->bind('app', $this->app);
        $user = null;
      /*  $user = Master::get(['appID' => $this->data['appID']]);
        if(!$user) {
            throw new AppException(-1000, '不存在用户，请确认是否传输正确的APPID');
        }*/
        $this->request->bind('user', $user);
        $this->__initialize();
    }

    public function check() {
        Log::info($this->data);
            if(!isset($this->data['apiV'])) {
            throw new AppException(-101, 'api版本号不能为空');
        }

        if(!isset($this->data['sign'])) {
            throw new AppException(-106, 'sign不能为空');
        }

        if(!isset($this->data['time'])) {
            throw new AppException(-107, 'time不能为空');
        }

        if(!isset($this->data['app'])) {
            throw new AppException(-109, 'app应用数据不能为空');
        }

        $signPre = '';
        if($this->data['app'] === '') {
            $signPre = $this->data['apiV'].$this->data['time'];
        }
        else if($this->data['app'] === []) {
            $signPre = $this->data['apiV'].$this->data['time'].'{}';
        }
        else {  //JSON_UNESCAPED_SLASHES
            $signPre = $this->data['apiV'].$this->data['time'].json_encode($this->data['app'], JSON_UNESCAPED_UNICODE);
        }

        $sign = md5($signPre);
        Log::info($signPre);;
        Log::info($sign);;
        if($sign != $this->data['sign']) {
            throw new AppException(-108, 'sign签名不正确');
        }
    }



    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }
}