<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */


namespace app\core\model;

use think\Model;
use tool\Common;

class Order extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function productinfo()
    {
        return $this->hasOne('Product','productID','productID');
    }

    public function address(){
        return $this->hasOne('UserAddress','addressID','addressID');
    }

    public function user(){
        return $this->hasOne('User','userID','userID');
    }


    public function vouchers()
    {
        return $this->hasMany('verification', 'orderID', 'orderID');
    }

    public function agentinfo()
    {
        return $this->hasOne('Master','masterID','agentID');
    }

    public function getMoneyAttr($value){
        return Common::price2($value/100);
    }

    public function getStatusTextAttr($value,$data){
        $status = ['-1' => '待支付','1' => '待核销','2'=> '已退款','3' => '已完成','-2' => '支付失败'];

        return $status[$data['status']];
    }


    public function setMoneyAttr($value) {
        return $value * 100;
    }

  /*  public function getcountAttr($value) {
        return Common::price2($value / 100);
    }*/

    public function getpaytimeAttr($time) {
        return (isset($time) && $time != 0) ? date('Y-m-d H:i:s',$time):0;
    }
}
