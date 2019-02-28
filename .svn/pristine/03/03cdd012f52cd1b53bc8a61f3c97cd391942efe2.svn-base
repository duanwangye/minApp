<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/10/29
 * Time: 14:41
 */

namespace app\admin\validate;

use think\Validate;

class PriceValidate extends Validate
{
    protected $rule = [
        'costPrice'  =>  'require|egt:0',
        'agentPrice' =>  'require|gt:costPrice',
        'salePrice' =>  'require|gt:agentPrice',
        'marketPrice' =>  'require|gt:salePrice',
    ];

    protected $message = [
        'costPrice.require'  =>  '成本价不能为空',
        'costPrice.egt' =>  '成本价不能小于0',
        'agentPrice.require'     =>   '代理价不能为空',
        'salePrice.require'     =>   '销售价不能为空',
        'marketPrice.require'     =>   '销售价不能为空',
        'agentPrice.gt'     =>   '代理价不能低于成本价',
        'salePrice.gt'     =>   '销售价不能低于代理价',
        'marketPrice.gt'     =>   '销售价不能低于代理价',
    ];

}
