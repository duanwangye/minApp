<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/10/10
 * Company:财联集惠
 */

namespace app\api\logic;
use app\core\exception\AppException;
use think\Config;
use think\Log;
use think\Request;
class Base
{
    protected $user;
    protected $app;
    protected $data;
    protected $platform;
    protected $request;
    protected $skin;
    public function __construct(Request $request) {

        $this->request = $request;
        $this->user = isset($request->user) ? $request->user : null;
        $this->app = isset($request->app) ? $request->app : [];
        $this->data = isset($request->data) ? $request->data : [];
      /*  $this->platform = Config::get('platform.default_check_gateway');
        $this->skin = Config::get('skin_channel'.'.app_skin_config');
        if($this->data['osType'] == 1) {
            $this->skin = Config::get('skin_channel_'.$this->data['channel'].'.app_skin_config');
        }*/
        //$this->_initialize();
    }

    public function map() {
        $map['userID'] = $this->user['userID'];
        return $map;
    }

    public function h5RootUrl() {
        return Config::get('h5RootUrl');
    }
}