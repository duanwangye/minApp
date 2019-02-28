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

class MasterSpending extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $createTime = 'addTime';
    protected $updateTime = 'updataTime';

    public function setReminMoneyAttr($val){
        return $val*100;
    }

    public function setDeductionMoneyAttr($val){
        return $val*100;
    }

    public function getReminMoneyAttr($val){
        return Common::price2($val/100);
    }

    public function getDeductionMoneyAttr($val){
        return Common::price2($val/100);
    }

}
