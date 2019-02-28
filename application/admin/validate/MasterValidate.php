<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/10/29
 * Time: 14:41
 */

namespace app\admin\validate;

use think\Validate;

class MasterValidate extends Validate
{
    protected $rule = [
        'mobile'  =>  'require',
        'trueName' =>  'require|max:20|min:2',
        'password' =>  'require|min:6|max:20',
    ];

    protected $message = [
        'mobile'  =>  '手机号不能为空',
        'trueName.require' =>  '真实姓名不能为空',
        'trueName.max'     =>   '真实姓名最多不能超过20个字符',
        'trueName.min'     =>   '真实姓名最少不能低于2个字符',
        'password.require'     =>   '密码不能为空',
        'password.min'     =>   '密码不能少于6位',
        'password.max'     =>   '密码最多不能超过20个字符',
    ];

}
