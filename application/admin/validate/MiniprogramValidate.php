<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/10/29
 * Time: 14:41
 */

namespace app\admin\validate;

use think\Validate;

class MiniprogramValidate extends Validate
{
    protected $rule = [
        'appName'  =>  'require',
        'appID' =>  'require',
        'appSecret' =>  'require',
        'domainName' =>  'require',
    ];

    protected $message = [
        'appName.require' =>  'appName不能为空',
        'appID.require'     =>   'appID不能为空',
        'appSecret.require'     =>   'appSecret不能为空',
        'domainName.require'     =>   'domainName不能为空'
    ];

}
