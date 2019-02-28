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

class OrderShare extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function setShareOneAttr($value){
        return $value*100;
    }

    public function setShareTwoAttr($value){
        return $value*100;
    }

    public function setSharethreeAttr($value){
        return $value*100;
    }

    public function getShareOneAttr($value){
        return Common::price2($value/100);
    }

    public function getShareTwoAttr($value){
        return Common::price2($value/100);
    }

    public function getSharethreeAttr($value){
        return Common::price2($value/100);
    }

}
