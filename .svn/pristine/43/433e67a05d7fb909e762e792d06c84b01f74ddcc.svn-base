<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/07
 * Company:财联集惠
 */
namespace app\api\logic;
use app\core\model\Product as Model;
use app\core\model\Master;
use app\core\model\Profits;
use think\Db;
use tool\Common;

class Product extends Base
{
    /*
     * @name 得到产品详情
     * @param productID
     * @return 产品详情
     */
    public function getProductDetail(){
       // $this->app['productID'] = 21;
        $model = Model::with('Master')->where('productID',$this->app['productID'])->find();
        //dump($model->toArray());exit();
        if ($model){
            $model->visible(['productID','salesSum','productName','media','promType','content','stock','salePrice','marketPrice','useRules','validity','master'=>['trueName','longitude','latitude','address']]);
            $model['media'] = json_decode($model['media'],true);
        }
        //$model['content']  =  htmlspecialchars_decode($model['content']);;
        return Common::rm(1,'操作成功',$model);
    }

    /*
     * @name 查看分享奖励
     * @param productID
     * @return 奖励金额
     */
    public function reward(){
        //$this->app['productID'] = 20;
        $product = Model::where('productID',$this->app['productID'])->find();
        $profits = Profits::get(1);
        //一级分润计算
        $money = Common::price2($product['commission']*($profits->lv1));

        return Common::rm(1,'您分享用户购买，可得佣金'.$money.'元');
    }


}