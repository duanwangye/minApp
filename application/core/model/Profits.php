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

class Profits extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $createTime = 'addTime';

    public function getLv1Attr($value){
        return Common::price2($value/100);
    }

    public function getLv2Attr($value){
        return Common::price2($value/100);
    }

    public function getLv3Attr($value){
        return Common::price2($value/100);
    }

    public function setLv1Attr($value){
        return $value*100;
    }

    public function setLv2Attr($value){
        return $value*100;
    }

    public function setLv3Attr($value){
        return $value*100;
    }

}
