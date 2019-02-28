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

class Master extends Model
{
    protected $resultSetType = 'collection';
    protected $updateTime = 'updateTime';
    protected $createTime = 'addTime';
    protected $autoWriteTimestamp = true;
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function productList(){
        return $this->hasMany('Product','masterID','masterID');
    }

    public function createPassword($password) {
        return md5(config("app.admin_password_salt").$password);
    }

    public function createToken() {
        return md5(THINK_START_TIME.Common::token_create(10));
    }

    public function createTokenOverTime() {
        return (int)THINK_START_TIME + 7 * 86400;
    }

    public function getStatusTextAttr($value,$data){
        $status = ['0' => '未启用','1' => '已启用'];
        return $status[$data['status']];
    }

    public function getTypeTextAttr($value,$data){
        $type = ['0' => '管理员','1' => '代理商','2' => '供应商'];

        return $type[$data['type']];
    }

    public function getYesMoneyAttr($val){
        return Common::price2($val/100);
    }

    public function getNoMoneyAttr($val){
        return Common::price2($val/100);
    }

    public function setYesMoneyAttr($val){
        return $val*100;
    }

    public function setNoMoneyAttr($val){
        return $val*100;
    }



}