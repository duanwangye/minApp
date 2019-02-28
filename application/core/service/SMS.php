<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/24
 * Company:财联集惠
 */

namespace app\core\service;

use think\Cache;
use think\Validate;
use tool\Common;
use Tencent\SMS as Service;

class SMS
{
    /*
     * @name 发送验证码
     * @param mobile  手机号
     */
    public function sendVerificationCode($mobile = ''){

        $validate = new Validate([
            'mobile' => 'require|length:11'
        ],[
            'name.require' => '手机号码必须填写',
            'name.length' => '手机号码格式不正确'
        ]);

        $result  = $validate->check([
            'mobile'=>$mobile
        ]);

        if (!$result){
            return Common::rm(-101, $validate->getError());
        }

        $package = Cache::get('verificationCode'.$mobile);
        if (!empty($package)){
            if ($package['outTime'] > THINK_START_TIME) {
                return Common::rm(-102, '您发送验证码过快，请等待' . (int)($package['outTime'] - THINK_START_TIME) . '后重新发送');
            }
            if ($package['times'] > 100) {
                return Common::rm(-103, '您发送的短信太频繁了，请稍后再次验证');
            }
        }else{
            $package = [
                'times'=>0
            ];
        }

        $code = mt_rand(1000, 9999);  //验证码
        $id = '234114';
        $param = [
            $code
        ];

        $res = Service::send_sma($mobile,$param,$id);

        if ($res['errmsg'] == 'OK'){
            $package['times']++;
            $package['outTime'] = THINK_START_TIME + 60;
            $package['code'] = $code;
            Cache::set('verificationCode'.$mobile, $package, 600);
            return Common::rm(1, '操作成功');
        }

        return Common::rm(-102,$res);

    }

    //获取验证码
    public function checkVerificationCode($mobile = '', $code = '') {
        $result = Cache::get('verificationCode'.$mobile);
        if(!$result) {
            return Common::rm(-101, '验证码已经失效了');
        }
        if($result['code'] != $code) {
            return Common::rm(-102, '验证码失败');
        }
        return Common::rm(1, '验证码通过');
    }
}