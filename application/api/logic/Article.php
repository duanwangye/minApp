<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 19/01/08
 * Company:财联集惠
 */

namespace app\api\logic;

use app\core\model\Article as Model;
use app\core\model\ArticleBuy;
use app\core\model\orderOther;
use app\core\model\User;
use tool\Common;

class Article extends Base
{

    /*
     * @name 得到文章列表
     * @param type 分类根据
     */

    public function getArticleList(){
       /* $this->app['pageIndex'] = 1;
        $this->app['pageItemCount'] = 10;
        $this->app['type'] = 1;*/
        //$this->app['userID'] = 1;
        $map = [];
        if (isset($this->app['type'])){
            $map['type'] = $this->app['type'];
        }

        //得到个人支付文章列表
        if (isset($this->app['userID']) && $this->app['userID']){
         $model = [];
         $payList = ArticleBuy::with('articleBuy')->where('userID',$this->app['userID'])->select();
         if (!$payList->isEmpty()){
            foreach ($payList as $k => $val){
                if ($val['article_buy']['type'] == 1){
                    $val['article_buy']['typeText'] = '收费文章';
                }elseif ($val['article_buy']['type'] == 0){
                    $val['article_buy']['typeText'] = '普通文章';
                }
                $model[] = $val['article_buy'];
                $model[$k]['media'] = json_decode( $model[$k]['media'],true);
                unset($model[$k]['updataTime']);
                unset($model[$k]['content']);
                unset($model[$k]['sort']);
            }
         }
            return Common::rm(1,'操作成功',$model);
        }
        $model = Model::where($map)->order('sort asc,addTime')->limit(($this->app['pageIndex'] -1)*$this->app['pageItemCount'],$this->app['pageItemCount'])->select();
        if (!$model->isEmpty()){
            $model->append(['typeText'])->hidden(['updataTime','content','sort']);
            foreach ($model as $key => $val){
                $model[$key]['media'] = json_decode($val['media'],true);
            }
        }
        return Common::rm(1,'操作成功',$model);
    }

    /*
     * @name  得到文章详情
     * @param articleID   文章ID
     * @param userID   用户ID
     *
     */

    public function getArticleDetail(){
       // $this->app['userID'] = 1;
        $model = Model::get($this->app['articleID'] = 1);
        if ($model){
            if ($model['type'] == 1){
                $user = ArticleBuy::where('userID',$this->app['userID'])->find();
                if (!$user){
                    return Common::rm(-201,'该文章为付费文章，您未付费，暂无权限查看');
                }
            }
            $model->hidden(['type','media','sort','updataTime']);
            $model['content'] = json_decode($model['content'],true);
        }

        return Common::rm(1,'操作成功',$model);

    }

    /*
     * @name 文章付费
     * @param userID 用户ID
     * @articleID 文章ID
     */
    public function payArticle(){
      /*  $this->app['articleID'] = 1;
        $this->app['userID'] = 1;*/
        $tradeNo = Common::orderNo();
        $order = orderOther::create([
            'tradeNo' => $tradeNo,
            'userID' => $this->app['userID'],
            'articleID' => $this->app['articleID'],
            'price' => $this->app['price'],
            'payType' => 0
        ]);

        if ($order){ //调起支付
            $user = User::where('userID', $this->app['userID'])->find();
            $options = [
                'appid' => 'wx1054d0223280f3f2',
                'mch_id' => '1518616481',
                'body' => '来客商城',
                'out_trade_no' => $this->app['tradeNo'],//商户订单号
                'nonce_str' => Common::getRandomStr(),//随机字符串
                'total_fee' => $this->app['price'] * 100,
                'notify_url' => 'https://api.lktehui.com/api/h5/notifyOther',
                'trade_type' => 'JSAPI',
                'spbill_create_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0',
                'openid' => $user['openID'],
            ];
            $res = Common::curl_post_ssl1('https://api.mch.weixin.qq.com/pay/unifiedorder', $options);
            $res = Common::xmltoarray($res);

            $time = time();
            $paySign = MD5('appId=wx1054d0223280f3f2&nonceStr='.$res['nonce_str'].'&package=prepay_id='.$res['prepay_id'].'&signType=MD5&timeStamp='.$time.'&key=lk2018th12061504dh16925636d6a6dg');

            $res =  [
                'timeStamp'=>$time,
                'nonceStr'=>$res['nonce_str'],
                'package'=>'prepay_id='.$res['prepay_id'],
                'signType'=>'MD5',
                'paySign'=>$paySign
            ];

            return Common::rm(1,'操作成功',$res);
        }else{
            return Common::rm(-2,'支付环境异常');
        }
    }
}