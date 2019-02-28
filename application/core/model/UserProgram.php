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
use traits\model\SoftDelete;

class UserProgram extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function user(){
        return $this->hasOne('User','userID','userID');
    }

    public function getVIPAttr($value){
        $vip = ['0' => '普通用户','1' => '会员'];
        return $vip[$value];
    }

    public function getEarningsAttr($value){
        return Common::price2($value/100);
    }

    public function getMoneyAttr($value){
        return Common::price2($value/100);
    }

    public function getNoMoneyAttr($value){
        return Common::price2($value/100);
    }

    public function getYesMoneyAttr($value){
        return Common::price2($value/100);
    }

    public function setEarningsAttr($value){
        return $value*100;
    }

    public function setMoneyAttr($value){
        return $value*100;
    }

    public function setNoMoneyAttr($value){
        return $value*100;
    }

    public function setYesMoneyAttr($value){
        return $value*100;
    }
}
