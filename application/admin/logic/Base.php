<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */
namespace app\admin\logic;
use app\core\exception\AppException;
use think\Config;
use think\Request;

class Base
{
    protected $master;
    protected $app;
    protected $data;
    protected $platform;
    protected $request;
    public function __construct(Request $request) {
        $this->request = $request;
        $this->master = isset($request->master) ? $request->master : null;
        $this->data =  isset($request->data) ? $request->data : [];
        $this->app =  isset($request->app) ? $request->app : [];
        //$this->platform = Config::get('platform.default_check_gateway');
        $this->_initialize();
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }


}