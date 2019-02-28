<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\logic;

use app\core\model\Master as Model;
use tool\Common;

class Master extends Base
{

    /**
     * @name {post} master/loginByPassword 密码登录
     * @Version 1.0.0
     * @apiName loginByPassword
     * @apiDescription 密码登录
     * @apiGroup Master
     *
     * @apiParam {String} mobile 手机号码
     * @apiParam {String} password 密码
     * @apiParamExample {json} 发送报文:
     *
     * @apiSuccessExample {json} 返回json数据（举例）:
         * @apiUse CreateUserError
     */
    public function loginByPassword() {
        $master = Model::get([
            'mobile'=>$this->app['mobile']
        ]);
        if(!$master) {
            return Common::rm(-3, '该管理员不存在');
        }
        if($master['password'] !=  $master->createPassword($this->app['password'])) {
            return Common::rm(-4, '密码不正确');
        }
        $master['token'] = $master->createToken();
        $master['tokenOverTime'] = $master->createTokenOverTime();
        $master['loginTime'] = THINK_START_TIME;
        $master['ip'] = $this->request->ip();
        $master->save();
        $this->master = $master;

        return Common::rm(1, '操作成功', [
            'token'=>$master['token'],
            'info'=>[
                'mobile'=>$this->app['mobile'],
            ]
        ]);
    }


    /**
     * @name {post} master/logout 退出登录
     * @Version 1.0.0
     * @apiName logout
     * @apiDescription 退出登录
     * @apiGroup Master
     *
     * @apiSuccessExample {json} 返回json数据（举例）:
    {
    "code": 1,
    "msg": "操作成功"
    }
     * @apiUse CreateUserError
     */
    public function logout() {
        Model::update([
            'tokenOverTime'=>0
        ],[
            'token'=>$this->master['token']
        ]);
        return Common::rm(1, '操作成功');
    }


}