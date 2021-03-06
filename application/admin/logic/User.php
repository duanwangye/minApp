<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use think\Db;
use tool\Common;
use app\core\model\User as Model;
use app\core\model\UserProgram;
use app\core\model\Master;
class User extends Base
{

    /*
     * @name 用户列表
     * @param page  页数
     * @param size  每页个数
     * @param mobile  手机号
     */
   public function getlist() {
       $page = $this->app['page'] ;//当前页数
       $size = $this->app['size'] ;//每页显示数量

       $params_where = [];
       if( isset($this->app['mobile']) ) {
           $params_where['mobile'] = $this->app['mobile'];
       }

       $user_model = new Model();
       $list = $user_model::with('userProgram')
           ->limit(($page-1)*$size, $size)
           ->where($params_where)
           ->select();
       $count = $user_model->count();   //总条数

       if ($list){
           $list = $list->visible(['userID','mobile','nickname','sex','addTime','user_program.VIP','user_program.earnings','user_program.money','user_program.noMoney','user_program.yesMoney']);
       }

       return Common::rm(1, '操作成功', [
           'page'=>$page,
           'count'=>$count,
           'list'=>$list
       ]);
   }

    //获取用户详细信息(得到用户实名注册后的信息)
    public function getinfo() {

       $info = Db::view('user','openID,photo,trueName,card,address')
           ->view('user_program','agentId,parentID','user_program.userID = user.userID')
           ->view('master',['trueName' => 'agentName'],'user_program.agentId = master.masterID')
           ->view(['a_user'=>'parentUser'],['nickname' => 'parentName'],'user_program.parentID = parentUser.userID','LEFT')
           ->where('user.userID',$this->app['userID'])
           ->find();

         if ($info){
             unset($info['agentId']);
             unset($info['parentID']);
         }
        return Common::rm(1,'获取成功',$info);
    }

    /*
     * @name 代理商关联账号
     */
    public function associatedAccount(){
       /* $this->app['masterID'] = '17';    //代理商ID  agentId
        $this->app['userID'] = '18';    //代理商ID*/
        $user =UserProgram::where('userID',$this->app['userID'])->update(['agentId' => $this->app['masterID']]);
        if ($user){
            Master::where('masterID',$this->app['masterID'])->update(['there' => 1]);
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }
    }



}