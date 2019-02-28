<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/12/03
 * Company:财联集惠
 */

namespace app\api\controller;

use app\core\model\orderOther as OrderModel;
use app\api\controller\AES;
use think\Log;
use think\Controller;
use think\Request;
use tool\Common;

class H5 extends Controller
{
    public function notify(){
        $res = file_get_contents('php://input');
        Log::write($res,'notify');
       $arr =  Common::xmltoarray($res);


        if ($arr['result_code'] == 'SUCCESS'){


               $order =  OrderModel::where('tradeNo',$arr['out_trade_no'])->find();
               $order->out_trade_no = $arr['transaction_id'];
               $order->paytime =time();
               $order->pay_type = $arr['trade_type'];
               $order->status = 1;   //更改支付状态
               $order->save();
               if ($order){
                   echo '<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>';
               }

        }

    }

    public function notifyOther(){
        $res = file_get_contents('php://input');
        Log::write($res,'notify');
        $arr =  Common::xmltoarray($res);


        if ($arr['result_code'] == 'SUCCESS'){


            $order =  OrderModel::where('tradeNo',$arr['out_trade_no'])->find();
            $order->payType =1;
            $order->save();
            if ($order){
                echo '<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>';
            }

        }

    }



    /*
     * @name 获取门店支付方式列表
     */
    public function payList(){
            $url = 'https://api.jxnxs.com/mct1/paylist';
        $openID = '814f77a8032a9d7232ffc4ab3b3875b9';
        $openKey = '47e3d3b9955980fb445bb6486d3b4a94';
        $param = [
            'open_id' => $openID,
            'timestamp' =>time(),
            'data' => [
                'pmt_type' => '4'
            ],
        ];
        $data = $this->data($param);
       // $res = Common::curlPost($url,$data);
        $aes = new AES();
        $str = 'FD805420D2E0BF7192256B52CF06E57209D5AEF1C892BFE56A8B9E9E3BA2821573C32E16F609582B1E6A14CEE7D13A73469B441C56D1FFC3EBD788C4AA43E93FF3603B0287BCCE945090B888C66B233A75C78D3035982376818F5CF3AC052FEECCAC241E1F36E1F7C89EB5B52807F956EAB163CE7B47FACC303A8029BB70405FC60EC1C477FA88D55B8A0B8ACB31791027D452C6954E8364A0737E5C055CD55C25BF3A7B3BFE233B8B433B82BF32D4850744249AA77351AB3CF3FA9B6A30B62C7ED1B29B616A9D9E3F52B3DE67F3227509F890D5C9E6044C3FEF2EE1BE0391D581FE21CC97F0ADBD945AFFB96835E726F461ED53366A8F2718493F70528104F96C81FF06934BCEBCCBBC4E4CD5FF2E9829AB4BB6105648EC00A7B7184C9682F8BE02523CECB4249B4155B4F513E0602F2199BF704526DE20007B6C611469F1913AED74E0BA2711E9124EC177FDE4DCF53D00D9EC36FC30116416FFA1721A494AD475D98E96964245EC5A0111838352E72FBCC3DFA9E3CBD8D07312556EDCFBFCFB2B04A88ECEEF569887DE16613249CCD7E5409A82BE390BA6E19284644B340A4A9FB93D5EA4BA316A4E2266C8D2F8D9C2D3D1E4427C3D1619EA8FEA871F7C9171197BFE809ACF6744AB92556AE9528A8BA941867646DD544B68D32F6C9F186D42EEC19141A903B423F531827BF73D2B0837E5961B294F34FD390FD04F6440FF3AB8B954E610109A4DF4555262863F4DE49F8B5D23BA5A5AF8FFB43105A0E87CBA6DD6CB3F4D579C23AA09F3C00FB8DAEA4A757E2F0469EFF5B2B17222A11BD595471162FC152E604AB23A6ADAE04898ED1E0F8AF921230DD0C3D4B5514672386224B5EBF7E26F215AE271CB7CA28A03F8E8EC84BF4601C9313EB95B7897497F3046238FD2CDD45018E6FC9F9AC2E18EA0541AB7DCF72AA1AF170CD67626910C3F1FC44D8BD09F9261CAEB8E0E1B3C76E7CEED80260C966A8D093911E47E18041F188FEF6541B42349C544DCFEE52A4730975CF541614DFAB28ACD64F4E8BB0E49698D254584F6BEA152581D6DA1D7AEAC97035D4EC947637439DB5C23812EBEB6A33FD2B8071A7159526545E5289909BA786C875AEA5FCC0E9D295CC569499A';
        $a = $aes->decode($str,$openKey);
        dump(json_decode($a,true));exit();

    }

    /*
     * @name 农商银行公众号支付
     */
    public function jspay(){
        $url = 'https://api.jxnxs.com/mct1/payorder';
       /* $openID = '814f77a8032a9d7232ffc4ab3b3875b9';
        $openKey = '47e3d3b9955980fb445bb6486d3b4a94';*/
        $openID = '73f50811841ed140dea8206a705c1f48';
        $openKey = '566e47370abbc9a60c5150aba74a3936';
        $data = [
            'open_id' => $openID,   //商户门店 open_id
            'timestamp' =>time(),  //Unix 时间戳
            //'sign' => '',        // 签名
            'data' => [
                'out_no' => 'md2018120513590011589',        // 开发者流水号
                'pmt_tag' => 'WeixinNAPP',        //付款方式编号   Weixin
                //'pmt_name' => '来客特惠',        // original_amount                  false
                //'ord_name' => '来客特惠',        //订单名称（商品描述）    false
                'original_amount' => '1',        //原始交易金额（分）
                'trade_amount' => '1',        //实际交易金额
               // 'auth_code' => '134621501623962662',        //实际交易金额
                'trade_type' => 'APP',        //实际交易金额
                'sub_appid' => 'wx1054d0223280f3f2',
                //'sub_mch_id' => 'ooM1N5TKNFatOjcoa37dUvi4Jx0g',
              //  'sub_openid' => 'ooM1N5TKNFatOjcoa37dUvi4Jx0g',
              //  'remark' => '',        //订单备注      false
               //'jump_url' => 'http://minapp.duolaibei.com/api/h5/notify',        //公众号/服务窗支付必填参数，支付完成后跳转到此地址，并以 get 返回支付结果信息
            ],        // 字段 json 数据

        ];


        $aes = new AES();
        $dataStr = json_encode($data['data']);   //json
        //$aesStr= $aes->encode($dataStr);   //加密
        $aesStr= $aes->encode($dataStr,$openKey);   //加密

      /*  $aesStr1= $aes->encrypt($dataStr,$openKey);   //加密
        dump($aesStr);
        dump($aesStr1);
        exit();*/
        //$b = $aes->decode($a);
        $data['data'] = $aesStr;
        $data['open_key'] = $openKey;
       // ksort($data);
        $string = $this->ASCII($data);
        //$string = http_build_query($data);   //转字符串
        $sign = strtolower(md5(strtolower(sha1($string))));

        $data['sign'] = $sign;
       // $data['data'] = $aes->decode(hex2bin($aesStr)); //解密
        unset($data['open_key']);
        //dump(json_encode($data));exit();
       // $res['data'] = 'C60AC9746BE3A346F1571848785118C5E29C46786119A78B9E073AB9D2F95DAA7CF8D32C7E5BD65CD1045FE5DF4DC1A0CAD3DBD0F0E7C3D88B6B8E85A4AAA0CF923A88F3983E437E7394DD72470877173F1FC44D8BD09F9261CAEB8E0E1B3C7636D3D2681244E808E27EB782FC10D6446B911D0E2316D706F93C45122B8F1E0D6D14AD220B39C1041F9BD3287DA6C4AA92D55D4B5E9B0F2C04D51517E48120D8C9E255AAC2F0084370BA7A66C443BE4133914E04E4E49444F44B4E4313AA844264826ED3AF94F08F62314601945196883D2AC3563D2B788BB40F0A486E0825405938F53D86B9104B5E8FF499DC9DF4CA4E41DDF20E8BF642910B9197AFE7AA3D2B23BC83F301CDA94CF965F9C9EE902C85058A3DBAC195AD95F4C0561EE29233DE314A523DEA396154A397A88136DBE0D2B15F8E2845527D21FAF967286B04B5F49B2455C0574C22261EC3940B179ADD05FC2E61D8689ECBDB87B31CFAAF98761BB7961C6D2D7313D023D904CB76F09C664FA62B8B00D15F68B7649D7D11673C13D86678E250630850CF9CAAC147B2F5AF681729F25114E1D571130CBF355D1B50AFB22313F668E10806389FA07D8126400A8D2A3C6A5C2E2AF8FC0CC906BD23B0FD13D2D2B0885F5E3671F276FDF4B0A968D5F7B4F43317ACB10D58B708AAB71A98020D966D18793A302738566D38B1A09DB72E808390227D0C254135CC7C835218AF0E09A19DF65447A9881B8CA47BFDC295FD113F784ED5A7600C77108D285FB156806454C36CE7BB02B2D4C6CE9ED5855B6A77A381C749B5A061A6299968699582FA2255D0BD480C98982B53CD9285DED865757005EF62D61DCDAB1AAC687D20007373458494ADE4CE06D590FBC608F4AC6911A5954F8B64D02B638BC58B0D9A99CACC9A52D487AF20040BFE463BC673999D44836B898025A91A7D48269A29B475A35C183D6C52C3C0713736D9F856A6C33B33F57F31D8349E3206016165E8206FCEBAA9525AA5F2A1395759864AD25A7189AA83599448B457C0CAB5CF60159158EA3269AB53E58D33D205EAEB94C219701267FBA684592ECD799160637F9EC1BDA02DB17BFF0B850F6EC39D372EB1A20ED3E9642E9F7D198472A7238256B36A96E052F7D57203D066894B86FB4242382D1BBF94BB456E76ABB616984F30';
        $res = json_decode(Common::curlPost($url,$data),true);  //post
       // $resData = json_decode($aes->decode($res['data'],$openKey),true);
        dump($res);
        if ($res['errcode'] == 0){
            $resData = json_decode($aes->decode($res['data'],$openKey),true);
           // $weChat = json_decode($resData['trade_result'],true);
            dump($resData);exit();
        }


    }

    function ASCII($params = array()){
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    $str .= $k .'=' . $val . '&';
                }
                $strs = rtrim($str, '&');
                return $strs;
            }
        }
        return '参数错误';
    }

    public function data($data){
        $openKey = '47e3d3b9955980fb445bb6486d3b4a94';
        $aes = new AES();
        $dataStr = json_encode($data['data']);   //json
        //$aesStr= $aes->encode($dataStr);   //加密
        $aesStr= $aes->encode($dataStr,$openKey);   //加密

        /*  $aesStr1= $aes->encrypt($dataStr,$openKey);   //加密
          dump($aesStr);
          dump($aesStr1);
          exit();*/
        //$b = $aes->decode($a);
        $data['data'] = $aesStr;
        $data['open_key'] = $openKey;
        // ksort($data);
        $string = $this->ASCII($data);
        //$string = http_build_query($data);   //转字符串
        $sign = strtolower(md5(strtolower(sha1($string))));

        $data['sign'] = $sign;
        // $data['data'] = $aes->decode(hex2bin($aesStr)); //解密
        unset($data['open_key']);
        return $data;
        //dump(json_encode($data));exit();

    }

    public function getOpenID(){
        $redirect_uri = urlencode('http://minapp.duolaibei.com/api/h5/weiChat');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx65be766059354680&redirect_uri=http%3A%2F%2Fminapp.duolaibei.com%2Fapi%2Fh5%2FweiChat&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
        header('location:'.$url);
    }

    public function weiChat(){
      //  $this->getOpenID();
      $code = $_GET['code'];
      Log::write($code,'weiChat');
      $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx65be766059354680&secret=b9fd900447d4b3aace9b59b859619b9b&code='.$code.'&grant_type=authorization_code';

      $token = Common::curlGet($url);

      $data = json_decode($token,true);
        Log::write($data,'weiChat');
      dump($data);exit();

    }

    public function unifiedorder(){   //lk2018th12061504dh16925636d6a6dg
        //
        $time = time();
        dump($time);
        $data = [
            'appid' => 'wx65be766059354680',
            'mch_id' => '1518616481',   //商户号
            'nonce_str' => time(),   //随机字符串在32位以
            //'sign' => '',   //签名
            'sign_type' => 'MD5',   //签名类型  默认为MD5 false
            'body' => '测试支付',   //商品描述
            'out_trade_no' => '2018120615001001',   //商户订单号
            'total_fee' => 100,   //标价金额
           // 'time_start' => $time,   //标价金额
           // 'time_expire' => ($time+7200),   //标价金额
            'spbill_create_ip' => '60.177.239.89',   //终端IP
            'notify_url' => 'http://minapp.duolaibei.com/api/h5/weiChat',   //通知地址
            'trade_type' => 'JSAPI',   //交易类
            'openid' => 'o6afj0_uuN8puU60oL8bOa25xxAg',   //交易类
        ];

        $str = $this->ASCII($data);
        $stringSignTemp = $str.'&key=lk2018th12061504dh16925636d6a6dg';
        $sing = strtoupper(md5($stringSignTemp));
        $data['sign'] = $sing;
        $xml = $this->arrayToXml($data);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res = $this->xmlToArray(Common::curlPost($url,$xml));
        $data = [
            'appId' => $res['appid'],
            'timeStamp' => $time,
            'nonceStr' => $res['nonce_str'],
            'package' => 'prepay_id='.$res['prepay_id'],
            'signType' => 'MD5',
            'paySign' => $res['sign'],
        ];
        //$str = '{"appId":"wxd8978cc4068bc70c","timeStamp":"1543977669","nonceStr":"5b6793428c1c4477b6371ab8ce80d5f0","package":"prepay_id=wx05104109070954399c1eed790750743854","signType":"RSA","paySign":"GxJb0EKPP0zHrzX8rW90gyeAChqyUHrVU5VUqneozJTbJ+uMmge+7BDopJd/a4Hvmh7RmPbt5UQiDKDIB7mOTiy/lLRnT2zZ/nHn9m7y6gDu6NYu4U5wGmCKC4tvNjxyK3QHgJ2ThhsxmgrMBXpkby2DTNO6E55XyS1Q6NEx/MW46We6KuPi6aPWBVTfLBUGqWFix0bLY8Oo4uh/JPBlNJtd9F0nc+2YwZdeHWHqEwEjtFp+XTwi1aL2b3rp8jiZcymOvxIM79CGurK/x4EFhAWUCxwaaZ6F4VhMRq84blTC70H/nb3wczzDE3XR0HRtZ/53cRw73NDuU2mN31VJ2Q=="}';
//        $str = json_encode($data);
//        /*dump($str);
//        dump($str1);exit();*/
//        $this->assign('jsApiParameters',($str));
//        return $this->fetch();
        return json(Common::rm(1,'操作成功',$data));
    }

    public function arrayToXml($arr, $level = 1) {
        $xml = "<root>";
        foreach ($arr as $key=>$val){
            if(is_array($val)){
                $xml.="<".$key.">".arrayToXml($val)."</".$key.">";
            }else{
                $xml.="<".$key.">".$val."</".$key.">";
            }
        }
        $xml.="</root>";
        return $xml;
    }

    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }


}