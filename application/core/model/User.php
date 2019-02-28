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

class User extends Model
{
    protected $autoWriteTimestamp = true;
    protected $resultSetType = 'collection';
    protected $updateTime = 'updataTime';
    protected $createTime = 'addTime';

    public function userProgram()
    {
        return $this->hasOne('UserProgram','userID','userID')
            ->field('userProgramID,userID,agentId,parentID,VIP,earnings,money,noMoney,yesMoney');
    }
    public function getSexAttr($value){
        $sex = ['0' => '未知','1' => '男','2' => '女'];
        return $sex[$value];
    }

    public function getCardAttr($value){
        return Common::substr_cut($value);
    }
}
