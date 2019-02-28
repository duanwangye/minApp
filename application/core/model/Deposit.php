<?php
namespace app\core\model;

use think\Model;
use tool\Common;

class Deposit extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function getStatusTextAttr($value,$data){
        //0-申请中。1-提现成功，-1 - 提现失败
        $status = ['0' => '申请中','1' => '体现成功','-1' => '提现失败'];
        return $status[$data['status']];
    }

    public function getTypeAttr($value){
        //1-福利提现 2-余额提现 3-充值
        $type = ['1'=> '可用余额','2'=> '余额','3' => '充值'];
        return $type[$value];
    }

    public function getcountAttr($value) {
        return Common::price2($value / 100);
    }

    public function setcountAttr($value) {
        return $value*100;
    }

    public function getMoneyAttr($value){
        return Common::price2($value / 100);
    }

    public function setMoneyAttr($value){
        return $value*100;
    }


}
