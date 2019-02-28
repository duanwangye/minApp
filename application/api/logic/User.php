<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/08
 * Company:财联集惠
 */

namespace app\api\logic;
use app\core\model\Banner;
use app\core\model\Deposit;
use app\core\model\Profits;
use app\core\model\User as Model;
use app\core\model\UserAddress;
use app\core\model\UserProgram;
use app\core\service\SMS;
use think\Cache;
use think\Log;
use tool\Common;

class User extends Base
{
    /*
     * @name 保存用户信息（小程序授权获取用户信息） 实名认证
     *  * @param  mobile  电话号码
     * @param trueName  真实姓名
     * @param card     身份证号
     * @param address  地址
     */
    public function setUserInfo(){

        $array = [0, 1, 2];
        foreach ($array as &$val) {
            var_dump(current($array));
        }
        exit();
       // return Common::rm(1,'',$this->app);
       /* $this->app = [
            'code' => '023BdvdG1Kdxh00GOVbG1oHvdG1Bdvd9',
            'photo' => 'https://www.baidu.com/img/baidu_jgylogo3.gif',
            'nickname' => 'baidutb',
            'sex' => '1',
            'mobile' => '15968808705',
            'trueName' => 'duanhui',
            'card' => '420528199302281413',
            'address' => '杭州跨贸小镇506',
        ];*/

       //获取openID
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx1054d0223280f3f2&secret=272a7ffd2d156c4566fc1ccd2c49e307&js_code='.$this->app['code'].'&grant_type=authorization_code';
        $data = json_decode(Common::curlGet($url),true);
        if (isset($data['openid'])){
            unset($this->app['code']);
            $this->app['openID'] = $data['openid'];
        }else{
            return Common::rm(-2,'系统出错');
        }

        $user = Model::where('openID',$this->app['openID'])->find();
        Log::write($this->app,'addcan');
        if ($user && isset($this->app['parentID']) && $this->app['parentID']){
            return Common::rm(101,'邀请失败，您已是平台用户',['userID' =>  $user['userID']]);
        }
        if (!$user){
            $model = Model::create([
                'openID' => $this->app['openID'],
                'photo' => $this->app['photo'],
                'nickname' => $this->app['nickname'],
                'sex' => $this->app['sex'],
            ]);
            $parentID = 0;
            $agentId = 1;
            if (isset($this->app['parentID']) && $this->app['parentID']){
                $parentID = $this->app['parentID'];
                $agent =  UserProgram::where('userID',$this->app['parentID'])->find();
                $agentId = $agent['agentId'];
            }


            //根据父级ID查询其父级的代理ID
            if (isset($this->app['agentId']) && $this->app['agentId']){
                $agentId = $this->app['agentId'];
            }
            UserProgram::create([
                'userID' => $model->userID,
                'parentID' => $parentID,
                'agentId' => $agentId,
                'VIP' => 0,
            ]);
            return Common::rm(1,'操作成功',['userID' =>  $model->userID]);
        }else{
            if (isset($this->app['mobile']) && $this->app['mobile']){
                if($this->app['mobileCode'] != '000000') {
                    $package = (new SMS())->checkVerificationCode($this->app['mobile'], $this->app['mobileCode']);
                    if($package['code'] != 1) {
                        return $package;
                    }
                }
                $user->save([
                    'mobile' => $this->app['mobile'],
                    'trueName' => $this->app['trueName'],
                   /* 'card' => $this->app['card'],
                    'address' => $this->app['address'],*/
                ],['userID' => $this->app['userID']]);

                UserProgram::where('userID', $user['userID'])->update(['VIP' => 1]);

            }
            return Common::rm(1,'操作成功',['userID' =>  $user['userID']]);
        }
    }

    /*
     * @name 用户实名认证发送验证码
     * @param mobile 电话号码
     */
    public function realNameSendMobileCode(){
       // $this->app['mobile'] = '15968808705';
        $model = Model::get([
            'mobile' =>   $this->app['mobile']
        ]);

        if ($model){
            return Common::rm(-3,'该手机号码已存在');
        }

        return (new SMS())->sendVerificationCode($this->app['mobile']);
    }

    /*
     * @name 得到用户详细信息
     * @param OpenID  用户唯一标识
     */
    public function getUserInfo(){
        $user = Model::where('userID',$this->app['userID'])->find();
        if ($user){
            $user->visible(['mobile','trueName','VIP','sex']);
        }

        return Common::rm(1,'操作成功',$user);
    }

    /*
     * @name 得到用户余额
     */
    public function getUserMoney(){
       /* $this->app = [
            'userID' => '9'
        ];*/
        $user = Model::with('userProgram')->where('userID',$this->app['userID'])->find();
        $data = [];
        if ($user){
            $data = [
                'userID' => $user['userID'],
                'earnings' => $user['userProgram']['earnings'],
                'money' => $user['userProgram']['money'],
                'noMoney' => $user['userProgram']['noMoney'],
                'yesMoney' => $user['userProgram']['yesMoney'],
            ];
        }

        return Common::rm(1,'操作成功',$data);

    }

    /*
     * @name 得到我的邀请列表
     * @param userID 用户ID
     * @return
     */
    public function getInvitationList(){
        $data = UserProgram::with(['user' => function($query){
            $query->field('nickname,userID');
        }])->where('parentID',$this->app['userID'])->select();

        if ($data){
            $data->visible(['user' => ['nickname'],'VIP','userID']);
        }

        return Common::rm(1,'操作成功',$data);
    }

    /*
     * @name 获取accessToken
     */
    public function getAccessToken(){
        $accessToken = Cache::get('access_token');
        $expires =  Cache::get('expires_in');
        if (!$accessToken){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx1054d0223280f3f2&secret=272a7ffd2d156c4566fc1ccd2c49e307';
            $res = json_decode(Common::curlGet($url),true);
            //设置缓存
            $time = time()+7100;
            Cache::set('access_token',$res['access_token'],$time);
            Cache::set('expires_in',($res['expires_in']+time()),$time);
            return Common::rm(1,'操作成功',[
                'access_token' => $res['access_token']
            ]);
        }else{
            if ($expires < time()){
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx1054d0223280f3f2&secret=272a7ffd2d156c4566fc1ccd2c49e307';
                $res = json_decode(Common::curlGet($url),true);
                //设置缓存
                $time = time()+7100;
                Cache::set('access_token',$res['access_token'],$time);
                Cache::set('expires_in',($res['expires_in']+time()),$time);
                return Common::rm(1,'操作成功',[
                    'access_token' => $res['access_token']
                ]);
            }
            return Common::rm(1,'操作成功',[
                'access_token' => $accessToken
            ]);
        }

    }


    /*
     * @name 生成带参数二维码
     * @param  产品ID或者userID
     * @return
     */
    public function getWxaCode(){
        $accseeToken = $this->getAccessToken();
        //$accseeToken['content']['access_token'] = '16_lz9T419achOYSFo--Ecao1sauGo8tjYMtcgxcs_Pj6oofb7T8OlC28mDH-Q7hRHputusTrO4mAS9d6a8tENIZAuPW5sklua_tPltOWt4jXsCt8ruLTDiqZM2DuXxxkWNHH02Yd7_9m2jKXajOCIaAIASGM';
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$accseeToken['content']['access_token'];
        $data = [
            'scene' => $this->app['scene'],
            'page' =>  $this->app['page'],   //不填写默认转调主页
            'width' => $this->app['width'],   //不填写默认转调主页
          /*  'auto_color' => false,   //自动配置线条颜色
            'line_color' =>['r' =>0,'g' => 0,'b' => 0],   //不填写默认转调主页
            'is_hyaline' =>false,   //不填写默认转调主页*/
        ];

        $file = Common::curlPost($url,json_encode($data));

        if ($file){
            $json = json_decode($file,true);
            if (isset($json['errcode'])){
                Cache::rm('access_token');
                Cache::rm('expires_in');
                return Common::rm(-1008,$json['errmsg']);
            }
        }
        return Common::rm(1,'操作成功',['file' => base64_encode($file)]);
       /* //$imgDir = 'uploadImg/';
        $filename=time().".jpg";///要生成的图片名字
        $imgDir = '/uploads';
        $file = fopen("./".$imgDir.$filename,"w");//打开文件准备写入
        fwrite($file,$img);//写入
        fclose($file);//关闭
        dump($imgDir.$filename);exit();*/


    }

    /*
     * @name 得到消费明细
     * @userID  用户ID
     * @status  0-申请中。1-提现成功，-1 - 提现失败
     */
    public function getStatement(){
        //$this->app['userID'] = '9';
        $list = Deposit::where('paytouid',$this->app['userID'])->limit(($this->app['pageIndex'] -1)*$this->app['pageItemCount'],$this->app['pageItemCount'])->select();

        if ($list){
            $list->append(['statusText'])->visible(['money','count','type','statusText','payment_time']);
        }

        foreach ($list as $k => $val){
            if ($val['payment_time']){
                $list[$k]['payment_time'] = date('Y-m-d',$val['payment_time']);
            }
        }



        return Common::rm(1,'操作成功',$list);
    }

    /*
     * @name  用户新增收货地址
     * @param userID 用户ID
     * @param consignee 收货人
     * @param address 详细地址
     * @param mobile 联系人手机
     * @param iSdefault 默认收货地址  0非默认 1 默认
     */
    public function createAddress(){

//        $this->app = [
//          /*  'userID' => '10',
//            'consignee' => '段辉',
//            'address' => '浙江省杭州市西湖区',
//            'mobile' => '15968808705',*/
//            'addressID' => '3',
//            'isDefault' => '1',
//            'userID' => '46',
//        ];
        if (isset($this->app['addressID']) && $this->app['addressID']){
            $address = UserAddress::get($this->app['addressID']);
            if (isset($this->app['isDefault']) && $this->app['isDefault']){
                UserAddress::where('userID',$address['userID'])->Update(['isDefault' => 0]);
            }
            $address->consignee = isset($this->app['consignee'])?$this->app['consignee']:$address['consignee'];
            $address->address = isset($this->app['address'])?$this->app['address']:$address['address'];
            $address->mobile = isset($this->app['mobile'])?$this->app['mobile']:$address['mobile'];
            $address->isDefault = isset($this->app['isDefault']) ? $this->app['isDefault']:$address['isDefault'];
            $address->save();
            if ($address){
                return Common::rm(1,'操作成功');
            }else{
                return Common::rm(-2,'操作失败');
            }
        }else{
            $address = UserAddress::where('userID', $this->app['userID'])->find();
            if ($address){ //存在 直接添加
                UserAddress::create($this->app);
            }else{
                $this->app['isDefault'] = 1; //设置默认
                UserAddress::create($this->app);
            }
            return Common::rm(1,'操作成功');
        }

    }

    /*
     *@name 得到个人收货地址列表
     * @param userID 用户ID
     */
    public function getAddress(){
        //$this->app['userID'] = 10;
        $address = UserAddress::where('userID',$this->app['userID'])->select();
        if (!$address->isEmpty()){
            $address->hidden(['addTime','updataTime']);
        }

        return Common::rm(1,'操作成功',$address);
    }

    /*
     * @name  得到地址详情
     * @param addressID
     */
    public function getAddressDel(){

        //$this->app['addressID'] = 10;
        $res = UserAddress::get( $this->app['addressID']);
        if ($res){
            $res->hidden(['addTime','updataTime','addressID','userID','isDefault']);
        }
        return Common::rm(1,'操作成功',$res);
    }

    /*
     * @name 删除收货地址
     * @param addressID 地址iD
     */
    public function delAddress(){
       //$this->app['addressID']     = '1';
       $res = UserAddress::destroy( $this->app['addressID']);
       if ($res){
           return Common::rm(1,'操作成功');
       }else{
           return Common::rm(-2,'操作失败');
       }
    }

    /*
     * @name 得到海报列表
     */

    public function getPosters(){
        $posters = Banner::where(['status' => 1,'type' => 3])->find();
        if ($posters){
            $posters->visible(['thumb']);
        }
        return Common::rm(1,'操作成功',$posters);
    }



    /*
     * @name 得到客服电话
     */
    public function getServicePhone(){
        return Common::rm(1,'操作成功','400-524156165165');
    }



}