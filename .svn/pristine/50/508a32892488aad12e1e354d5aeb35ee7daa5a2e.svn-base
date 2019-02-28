<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use tool\Common;
use app\core\model\Profits as Model;
class Profits extends Base
{
    //添加分润
    public function addProfits() {
        $params['lv1'] = $this->app['lv1'];
        $params['lv2'] = $this->app['lv2'];
        $params['lv3'] = $this->app['lv3'];

        if (config('system.share_class') == 2){
            $params['lv3'] = 0;//二级分销，3级需归零
        }elseif (config('system.share_class') == 1){
            $params['lv2'] = 0;//一级分销，2、3级需归零
        }

        $all_percent = $params['lv1'] + $params['lv2'] + $params['lv3'];
        if ($all_percent > 100){
            return Common::rm(-1,'总分润比不能大于100');
        }

        $create_result = Model::create($params);

        if ($create_result){
            return Common::rm(1,'新增成功');
        }else{
            return Common::rm(-1,'新增失败');
        }
    }

    public function getinfo() {
        $profits_model = new Model();
        $profits_info = $profits_model->order('addTime desc')->find();
        return Common::rm(1,'操作成功',$profits_info);
    }

}