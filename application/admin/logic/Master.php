<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use app\admin\validate\MasterValidate;
use app\core\model\Master as Model;
use app\core\model\MasterRole;
use app\core\model\MasterSpending;
use app\core\model\User;
use app\core\model\UserProgram;
use think\Db;
use think\Loader;
use tool\Common;

class Master extends Base
{

    /**
     * @name {post} master/loginByPassword 密码登录
     * @Version 1.0.0
     * @apiName loginByPassword
     * @apiDescription 密码登录
     * @apiGroup Master
     *
     * @apiParam {String} mobile 手机号码
     * @apiParam {String} password 密码
     * @apiParamExample {json} 发送报文:
     *
     * @apiSuccessExample {json} 返回json数据（举例）:
         * @apiUse CreateUserError
     */
    public function loginByPassword() {
        $master = Model::get([
            'mobile'=>$this->app['mobile']
        ]);
        if(!$master) {
            return Common::rm(-3, '该管理员不存在');
        }
        if($master['password'] !=  $master->createPassword($this->app['password'])) {
            return Common::rm(-4, '密码不正确');
        }

//        $group = Db::name('master_group')->where([
//            'masterID'=>$master['masterID']
//        ])->find();
//        if(!$group) {
//            return Common::rm(-5, '没有权限');
//        }

        $master['token'] = $master->createToken();
        $master['tokenOverTime'] = $master->createTokenOverTime();
        $master['loginTime'] = THINK_START_TIME;
        $master['ip'] = $this->request->ip();
        $master->save();
        $this->master = $master;

        return Common::rm(1, '操作成功', [
            'token'=>$master['token'],
            'type'=>$master['type'],
            'userID'=>$master['masterID'],
        ]);
    }


    /**
     * @name {post} master/logout 退出登录
     * @Version 1.0.0
     * @apiName logout
     * @apiDescription 退出登录
     * @apiGroup Master
     *
     * @apiSuccessExample {json} 返回json数据（举例）:
    {
    "code": 1,
    "msg": "操作成功"
    }
     * @apiUse CreateUserError
     */
    public function logout() {
        Model::update([
            'tokenOverTime'=>0
        ],[
            'token'=>$this->master['token']
        ]);
        return Common::rm(1, '操作成功');
    }

    /*
    * 新增用户 （商户）
    * @name {post}  master/addMerchants
     * @Version 1.0.0
     * @apiName addMerchants
     * @apiDescription 新增商户
     * @apiGroup Master
        */
    public function addMerchants(){
        if ($this->app['roleID'] !=5){
            return $this->addmaster();
        }
         $mobile = Model::get(['mobile' =>  $this->app['mobile']]);
         if ($mobile){
             return Common::rm(-1,'该账号已存在，请勿重复添加');
         }
         $model = Model::create([
             'trueName' => $this->app['trueName'],
             'mobile' => $this->app['mobile'],
             'password' => md5('aisdfa90asopdf0as8d0f8a0s9d8f0asdfjasdfaqw'.$this->app['password']),
             'parentID' =>  $this->app['parentID'],
             'status' =>  1,
            // 'privateKey' => $key['privKey'],
            // 'publicKey' => Config::get('system.pubkey'),  //商户公钥  和系统对接时使用
             'appID' => Common::randString()                  //商户调用接口唯一标识
         ]);
        $group = Group::create([
            'masterID' => $model->masterID,
            'masterRoleID' => $this->app['roleID'],
        ]);

        if ($this->app['parentID'] != 0){
            $pay = MasterPay::where(['masterID' => $this->app['parentID'],'channelID' => $this->app['channelID']])->find();
            #如果商户费率小于代理商费率返回错误
            if ($pay['rate'] > $this->app['rate']){
               return Common::rm(-1,'商户费率不能小于代理商费率');
            }
        }

        //商户支付配置
        $channel = MasterPay::create([
            'masterID' => $model->masterID,
            'channelID' => $this->app['channelID'],
            'payType' =>  !empty($this->app['payType']) ? json_encode($this->app['payType']) : null ,   //支付配置 装换成json字符串
            'paymentList' => implode(',',$this->app['payType']),   //支付配置 装换成json字符串
            'rate' => $this->app['rate'],        //商户渠道费率  （费率要大于父级费率）
            'status' => 1                         //渠道状态  0 关闭 1 开启  新增商户渠道默认开启
         ]);


       if ($channel){
           return Common::rm(1,'操作成功');
       }else{
           return Common::rm(-1,'添加失败');
       }

    }

    public static function checkAuth($master = [], $action = '') {
        $group = Db::name('master_group')->where([
            'masterID'=>$master['masterID']
        ])->find();
        if(!$group) {
            return false;
        }
        if($group['masterRoleID'] == 1) {
            return true;
        }
        $auth = Db::name('master_auth')->where([
            'masterRoleID'=>$group['masterRoleID']
        ])->select();
        if(!$auth) {
            return false;
        }
        $actionS = array_column($auth, 'action');
        if(!in_array($action, $actionS)) {
            return false;
        }
        return true;
    }


    /*
     * 获取后台用户列表
     * */
    public function getMasterList() {
        $page = $this->app['page'] ;//当前页数
        $size = $this->app['size'] ;//每页显示数量

        $master_model = new Model();
        $invests = $master_model
                    ->limit(($page-1)*$size, $size)
                    ->field('masterID,trueName,mobile,status,type,areaCode,address,there,yesMoney,noMoney')
                    ->select();
        if ($invests){
            $invests->append(['statusText','typeText']);
        }
        $count = $master_model->count();//总条数
        return Common::rm(1, '操作成功', [
            'page'=>$page,
            'count'=>$count,
            'list'=>$invests
        ]);

    }

    /*
     * 新增后台用户
     * */
    public function add() {
        $master_validate = Loader::validate('MasterValidate');
        $validate_result = $master_validate->check($this->app);

        if(!$validate_result){
            return Common::rm(-1,$master_validate->getError());
        }

        $mobile = Model::get(['mobile' => $this->app['mobile']]);
        if ($mobile){
            return Common::rm(-1,'该账号已存在，请勿重复添加');
        }
        $result = Model::create([
            'trueName' => $this->app['trueName'],
            'mobile' => $this->app['mobile'],
            'password' => md5(config("app.admin_password_salt").$this->app['password']),
            'status' =>  isset($this->app['status'])?$this->app['status']:config('status.master_status_using'),
            'type' =>  $this->app['type'],
            'appID' => Common::randString(),                  //商户调用接口唯一标识
            'longitude' => $this->app['longitude'],
            'latitude' => $this->app['latitude'],
            'areaCode' => $this->app['areaCode'],
            //'cityName' => $this->app['cityName'],
            //'adCode' => $this->app['adCode'],
            'address' => $this->app['address'],
        ]);

        if ($result){
            return Common::rm(1,'添加成功');
        }else{
            return Common::rm(-1,'添加失败');
        }

    }

    /*
     * 修改后台用户信息
     * */
    public function edit() {
        $master_info = Model::get(['masterID' => $this->app['masterID']]);

        $params['trueName'] = $this->app['trueName']?$this->app['trueName']:$master_info['trueName'];
        $params['mobile'] = $this->app['mobile']?$this->app['mobile']:$master_info['mobile'];
        $params['password'] = $this->app['password']?md5(config("app.admin_password_salt").$this->app['password']):$master_info['password'];;
        $params['status'] = $this->app['status']?$this->app['status']:$master_info['status'];
        $params['longitude'] = $this->app['longitude']?$this->app['longitude']:$master_info['longitude'];
        $params['latitude'] = $this->app['latitude']?$this->app['latitude']:$master_info['latitude'];
        $params['areaCode'] = $this->app['areaCode']?$this->app['areaCode']:$master_info['areaCode'];
       // $params['cityName'] = $this->app['cityName']?$this->app['cityName']:$master_info['cityName'];
       // $params['adCode'] = $this->app['adCode']?$this->app['adCode']:$master_info['adCode'];
        $params['address'] = $this->app['address']?$this->app['address']:$master_info['address'];
        $params['type'] = $this->app['type'];

        $master_params['masterID'] = $this->app['masterID'];

        $save_result = $master_info->save($params,$master_params);

        if ($save_result){
            return Common::rm(1,'修改成功');
        }else{
            return Common::rm(-1,'你没有做出任何修改');
        }
    }

    /*
     * 软删除后台用户信息
     * */
    public function delete() {
        $delete_result = Model::destroy($this->app['masterID']);
        if ($delete_result){
            return Common::rm(1,'删除成功');
        }else{
            return Common::rm(-1,'删除失败');
        }
    }

    //禁用
    public function recycle() {
       $model =Model::where('masterID',$this->app['masterID'])->update(['status' => $this->app['status']]);

       if ($model){
           return Common::rm(1,'操作成功');
       }
    }

    /*
     * @name 后台打款
     * @param masterID
     */
    public function getAgentMoney(){
        /*$this->app = [
            'masterID' => '24',
            'reminMoney' => '96',   //打款金额
            'deductionMoney' => '100',   //扣款金额
            'note' => '请注意查收',   //备注
        ];*/

        //扣除当前用户的可用金额

        $model = Model::where('masterID',$this->app['masterID'])->find();

        if ($model['yesMoney'] - $this->app['deductionMoney'] <0){
            return Common::rm(-2,'该商户可用金额不足'.$this->app['deductionMoney'].'元');
        }
        $model->yesMoney = $model['yesMoney'] - $this->app['deductionMoney'];
        $model->save();

        if ($model){
            $spend = MasterSpending::create($this->app);
            if ($spend){
                return Common::rm(1,'操作成功');
            }else{
                return Common::rm(-2,'操作失败');
            }
        }else{
            return Common::rm(-2,'操作失败');
        }
    }

    /*
     * @name 供应商得到流水
     * @param masterID 供应商ID
     */
    public function getSpendList(){
        /*$this->app['masterID'] = '24';
        $this->app['page'] = 1;
        $this->app['size'] = 10;*/
        $map = [];
       if (isset($this->app['masterID']) && $this->app['masterID']){
           $map['masterID'] =$this->app['masterID'];
       }
        $list = MasterSpending::where($map)->limit(( $this->app['page']-1)*$this->app['size'],$this->app['size'])->select();
        $count = MasterSpending::where($map)->count();
        if (!$list->isEmpty()){
            $list->visible(['reminMoney','deductionMoney','note','addTime']);
        }

        return Common::rm(1,'操作成功',['list' => $list,'count' => $count]);
    }


    /*
     * @name 代理商信息
     * @param masterID
     */
    public function getAgentInfo(){
        //$this->app['masterID'] = '18';
        $model = Model::where('masterID',$this->app['masterID'])->find();
        if ($model){
            $model->visible(['yesMoney','noMoney']);
        }

        //得到供应商下面的用户列表
        $count = UserProgram::where('agentId',$this->app['masterID'])->count();

        return Common::rm(1,'操作成功',[
            'money' => $model,
            'agentCount' => $count,
        ]);
    }

}