<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */


namespace app\core\model;

use think\Model;
use traits\model\SoftDelete;
use tool\Common;

class Product extends Model
{
   // protected $table = 'a_product';
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';


    public function classinfo()
    {
        return $this->hasOne('ProductClass','classID','classID');
    }

    public function Master(){
        return $this->hasOne('Master','masterID','masterID');
    }

    public function setcommissionAttr($value) {
        return $value * 100;
    }

    public function getcommissionAttr($value) {
       // return Common::price2($value / 100);
        return ($value/100);
    }

    public function setcostPriceAttr($value) {
        return $value * 100;
    }

    public function getcostPriceAttr($value) {
        return  ($value/100);
       // return Common::price2($value / 100);
    }

    public function setagentPriceAttr($value) {
        return $value * 100;
    }

    public function getagentPriceAttr($value) {
        return  ($value/100);
        //return Common::price2($value / 100);
    }

    public function setsalePriceAttr($value) {
        return $value * 100;
    }

    public function getsalePriceAttr($value) {
        return  ($value/100);
        //return Common::price2($value / 100);
    }

    public function setmarketPriceAttr($value) {
        return $value * 100;
    }

    public function getmarketPriceAttr($value) {
        return  ($value/100);
       // return Common::price2($value / 100);
    }

}
