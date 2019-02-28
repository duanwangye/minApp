<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 19/01/08
 * Company:财联集惠
 */
namespace app\core\model;

use think\Model;
use tool\Common;

class orderOther extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function getPayTypeAttr($val){
        $payType = ['0' => '支付失败','1' => '支付成功'];
        return $payType[$val];
    }
}