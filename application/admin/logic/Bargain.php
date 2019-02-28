<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use app\core\model\ActivityBargain;
use app\core\model\ActivityBargainirg;
use app\core\model\ActivityBargainirgList;
use think\Db;
use think\Request;
use tool\Common;

class Bargain extends Base
{
    /*
     * @name 新增活动
     * @param product_id 参加活动的产品id
     * @param activity_money 活动价格
     * @param product_desc 活动描述
     * @param bargain_min_price 每次砍价最低价格
     * @param bargain_max_price 每次砍价最高价格
     * @param begin_time 活动开始时间
     * @param end_time 活动结束时间
     */
    public function setBargain() {
//         $this->app = [
////             'bargain_id' => 13,
//             'product_id' => 1,
//             'activity_money' => 9.99,
//             'product_desc' => 'baidutb',
//             'bargain_min_price' => '1',
//             'bargain_max_price' => '20',
//             'status' => '1',
//             'begin_time' => '1543640199',
//             'end_time' => '1543640229',
//         ];

        $product_info = \app\core\model\Product::get($this->app['product_id']);

        if ($product_info['promType'] != 6) {
            return Common::rm(-2,'非活动类产品');
        }

        $activity_money = $this->app['activity_money'];
        $bargain_min_price = $this->app['bargain_min_price'];
        $bargain_max_price = $this->app['bargain_max_price'];

        if ($bargain_max_price < $bargain_min_price) {
            return Common::rm(-2,'最高价格不能低于最低价格');
        }

        if (isset($this->app['bargain_id']) && $this->app['bargain_id']){
            $save_res = (new ActivityBargain)->save([
                'product_id' => $this->app['product_id'],
                'activity_money' => $activity_money,
                'product_desc' => $this->app['product_desc'],
                'bargain_min_price' => $bargain_min_price,
                'bargain_max_price' => $bargain_max_price,
                'status' => $this->app['status'],
                'begin_time' => $this->app['begin_time'],
                'end_time' => $this->app['end_time'],
            ],['bargain_id'=>$this->app['bargain_id']]);

            if ($save_res){
                return Common::rm(1,'操作成功');
            }else{
                return Common::rm(-2,'操作失败');
            }
        } else {
            $create_res = ActivityBargain::create([
                'product_id' => $this->app['product_id'],
                'activity_money' => $activity_money,
                'product_desc' => $this->app['product_desc'],
                'bargain_min_price' => $bargain_min_price,
                'bargain_max_price' => $bargain_max_price,
                'status' => $this->app['status'],
                'begin_time' => $this->app['begin_time'],
                'end_time' => $this->app['end_time'],
            ]);

            if ($create_res) {
                return Common::rm(1,'操作成功');
            } else {
                return Common::rm(-1,'操作失败');
            }

        }

    }


    /*
     * @name 砍价活动列表
     * @param page 当前页数
     * @param size 每页显示数量
     */
    public function getlist() {
        $list = Db::view('a_activity_bargain','bargain_id,product_desc,product_id,activity_money,bargain_min_price,bargain_max_price,begin_time,end_time,status')
            ->view('product','productName','product.productID = a_activity_bargain.product_id','LEFT')
            ->limit(($this->app['page']-1)*$this->app['size'],$this->app['size'])
            ->select();

        $count = Db::view('a_activity_bargain','bargain_id,product_id,activity_money,bargain_min_price,bargain_max_price,begin_time,end_time,status')
            ->view('product','productName','product.productID = a_activity_bargain.product_id','LEFT')
            ->count();

        foreach ($list as $k => $value){
            $list[$k]['activity_money'] = Common::price2($value['activity_money']/100);
            $list[$k]['bargain_min_price'] = Common::price2($value['bargain_min_price']/100);
            $list[$k]['bargain_max_price'] = Common::price2($value['bargain_max_price']/100);
        }

        return Common::rm(1, '操作成功', [
            'count'=>$count,
            'list'=>$list
        ]);
    }


    /*
     * 砍价活动参与用户列表
     * */
    public function bargainirglist() {

        $bargain_id = $this->app['bargain_id'];
//        $bargain_id = 1;

        $list = Db::view('a_activity_bargainirg','bargainirg_id,bargain_count,deal_money,is_addorder,addTime')
                ->view('product','productName','product.productID = a_activity_bargainirg.product_id','LEFT')
                ->view('user','nickname','user.userID = a_activity_bargainirg.user_id','LEFT')
                ->limit(($this->app['page']-1)*$this->app['size'],$this->app['size'])
                ->where(['bargain_id'=>$bargain_id])
                ->select();

        $count = Db::view('a_activity_bargainirg','bargainirg_id,bargain_count,deal_money,is_addorder,addTime')
            ->view('product','productName','product.productID = a_activity_bargainirg.product_id','LEFT')
            ->view('user','nickname','user.userID = a_activity_bargainirg.user_id','LEFT')
            ->where(['bargain_id'=>$bargain_id])
            ->count();


        $is_addorder = ['0' => '未下单','1'=>'已下单'];
        foreach ($list as $k => $value){
            $list[$k]['deal_money'] = Common::price2($value['deal_money']/100);
            $list[$k]['addTime'] = date('Y-m-d H:i:s',$value['addTime']);
            $list[$k]['is_addorder'] = $is_addorder[$value['is_addorder']];
        }

        return Common::rm(1, '操作成功', [
            'count'=>$count,
            'list'=>$list
        ]);

    }


    /*
     * @name 砍价活动帮助者列表
     * @param bargainirg_id 砍价表id
     */
    public function getbargainirglist() {
//        $this->app = [
//            'bargainirg_id'=>18,
//            'page'=>1,
//            'size'=>10,
//        ];
        $bargainirg_id = $this->app['bargainirg_id'];
        $list = Db::view('a_activity_bargainirg_list','bargain_money,addTime')
                ->view('user','nickname','user.userID = a_activity_bargainirg_list.assistor_id','LEFT')
                ->limit(($this->app['page']-1)*$this->app['size'],$this->app['size'])
                ->where(['bargainirg_id' => $bargainirg_id])
                ->select();

        $count = Db::view('a_activity_bargainirg_list','bargain_money,addTime')
            ->view('user','nickname','user.userID = a_activity_bargainirg_list.assistor_id','LEFT')
            ->where(['bargainirg_id' => $bargainirg_id])
            ->count();

        foreach ($list as $k => $value){
            $list[$k]['bargain_money'] = Common::price2($value['bargain_money']/100);
            $list[$k]['addTime'] = date('Y-m-d H:i:s',$value['addTime']);
        }

        return Common::rm(1, '操作成功', [
            'count'=>$count,
            'list'=>$list
        ]);
    }

    /*
     * @name 修改状态
     * @param status 0禁用 1启用
     * @param bargain_id 砍价配置ID
     */
    public function updateStatus(){
        $model = ActivityBargain::where('bargain_id',$this->app['bargain_id'])->update(['status' => $this->app['status']]);
        if ($model){
            return Common::rm(1,'操作成功');
        }
    }


}