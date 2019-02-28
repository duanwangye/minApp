<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */


namespace app\core\model;

use think\Model;

class ProductClass extends Model
{
    protected $table = 'a_class';
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';


    public function getStatusTextAttr($value,$data){
        $status = ['0' => '未启用','1' => '已启用'];
        return $status[$data['status']];
    }
}