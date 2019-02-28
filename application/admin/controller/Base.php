<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\controller;

use app\core\exception\AppException;
use app\core\model\Master;
use think\Log;


class Base
{

    public $master;
    public $data = [];
    public $request;
    public $app;


    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        $this->request = request();
        $this->data = $this->request->post();
        //$this->data = $this->request->getInput();  //未设置请求头或者请求头 不是application/json的时候 使用
        Log::write($this->data,'data');
       // $this->data['token'] = '16f357a14166afa7b44410e1d36d7c92';

        if($this->request->action() != strtolower('loginByPassword')) {
           if ($this->request->action() != strtolower('upload')){
               if(!isset($this->data['token'])) {
                   // todo: 改为跳转登录界面
                   throw new AppException(-1003, 'token是必传字段');
               }

               $master = Master::get([
                   'token'=>$this->data['token']
               ]);
               if(!$master) {
                   throw new AppException(-1001, '不存在token');
               }
               if($master['tokenOverTime'] < THINK_START_TIME) {
                   throw new AppException(-1002, '登录超时，请重新登录');
               }

               $this->request->bind('master', $master);
           }

        }
        //$this->request->bind('master', $master);
        $this->request->bind('app', isset($this->data['app']) ? $this->data['app'] : []);
        $this->__initialize();
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }

}