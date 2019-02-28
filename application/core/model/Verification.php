<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */


namespace app\core\model;

use think\Model;

class Verification extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function getStatusTextAttr($value,$data){
        $status = ['0' => '待核销','1' => '已核销'];
        return $status[$data['status']];
    }
}
