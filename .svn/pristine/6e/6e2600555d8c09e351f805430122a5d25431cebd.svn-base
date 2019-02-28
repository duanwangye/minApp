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

class Article extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function getTypeTextAttr($val,$data){
        $type = ['0' => '普通文章','1' => '收费文章'];
        return $type[$data['type']];
    }

    public function setPriceAttr($val){
        return $val*100;
    }

    public function getPriceAttr($val){
        return Common::price2($val/100);
    }
}