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

class ProductReject extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}