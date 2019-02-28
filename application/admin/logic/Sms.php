<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/23
 * Company:财联集惠
 */

namespace app\admin\logic;

use tool\Common;

class Sms extends Base
{
    /*
     * @name 添加短息模板
     * @param text 模板内容
     */
    public function changeModul(){
        $appkey = 'ccc0de14a3be82e46bdbb3d6e064c3ca';
        $random = str_shuffle(time());
        $time= time();
        $data = 'appkey='.$appkey.'&random='.$random.'&time='.$time;
        $sign =$sign = hash('sha256',$data,false);
        $url = '';
        $param = [];
        if (!isset($this->app['tpl_id']) && !$this->app['tpl_id']){ //新增
            $url = 'https://yun.tim.qq.com/v5/tlssmssvr/add_template?sdkappid=1400164231&random='.$random;
            $param = [
                'sig' => $sign,
                'text' => $this->app['text'],  //模板内容
                'time' => $time,  //请求发起时间
                //'title' => '来客验证码',  //模板名称
                'type' => 0,  //短信类型
            ];
        }else{
            $url = 'https://yun.tim.qq.com/v5/tlssmssvr/mod_template?sdkappid=1400164231&random='.$random;
            $param = [
                'sig' => $sign,
                'text' => $this->app['text'],  //模板内容
                'time' => $time,  //请求发起时间
                'tpl_id	' =>$this->app['tpl_id'],  //模板名称
                'type' => 0,  //短信类型
            ];
        }
        $rs = json_decode(Common::curlPost($url,json_encode($param)),true);
        if ($rs['result'] == 0){  //成功
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }
    }

    /*
     * @name 查询短信模板
     */
    public function smsModel(){
        $strAppkey = 'ccc0de14a3be82e46bdbb3d6e064c3ca';
        $random = str_shuffle(time());
        $time = time();
        $data = 'appkey='.$strAppkey.'&random='.$random.'&time='.$time;
        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/get_template?sdkappid=1400164231&random='.$random;
        $sign =$sign = hash('sha256',$data,false);
        $data = [
            'sig' =>$sign,
            'time' => time(),
            'tpl_page' =>[
                "max"=> 10,
                "offset"=> 0
            ],
        ];

        $res = json_decode(Common::curlPost($url,json_encode($data)),true);

        return Common::rm(1,'操作成功',$res['data']);
    }

    /*
     * @name 删除模板
     * @param tpl_id 模板ID
     */
    public function delModel(){
        $appkey = 'ccc0de14a3be82e46bdbb3d6e064c3ca';  ////sdkappid 对应的 appkey，需要业务方高度保密
        $random = str_shuffle(time());                                         //random 请填成随机数
        $time = time();
        $data = 'appkey='.$appkey.'&random='.$random.'&time='.$time;
        $sign = hash('sha256',$data,false);
        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/del_template?sdkappid=1400164231&random='.$random;

        $param = [
            'sig' => $sign,
            'time' => $time,
            'tpl_id' =>[
                '234301'
            ]
        ];

        $res = json_decode(Common::curlPost($url,json_encode($param)),true);

        dump($res);exit;


    }

    /*
     * @name 发送短信
     *
     */
    public function sendSms(){
        $appkey = 'ccc0de14a3be82e46bdbb3d6e064c3ca';  ////sdkappid 对应的 appkey，需要业务方高度保密
        $random = str_shuffle(time());                                         //random 请填成随机数
        $time = time();                                             //UNIX 时间戳
        $mobile = '18358190832';
        $data = 'appkey='.$appkey.'&random='.$random.'&time='.$time.'&mobile='.$mobile;
        $sign = hash('sha256',$data,false);

        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=1400164231&random='.$random;

        $data = [
            'params' => [
                '1234',
            ], // 模板参数，若模板没有参数，请提供为空数组
            'sig' => $sign,  //App 凭证   sha256（appkey=$appkey&random=$random&time=$time&mobile=$mobile）
            'sign' => '来客特惠',  //短信签名，如果使用默认签名，该字段可缺省
            'tel' => [
                'mobile' => $mobile,   // 手机号
            ],
            'time' => $time,  // 	请求发起时间，UNIX 时间戳（单位：秒）
            'tpl_id' => '234114',  // 模板 ID
        ];

        $res = Common::curlPost($url,json_encode($data));

        dump(json_decode($res,true));exit();
    }
}