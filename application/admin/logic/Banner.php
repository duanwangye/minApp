<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use tool\Common;
use app\core\model\Banner as Model;
class Banner extends Base
{
    //获取列表
    public function getlist() {
        $page = $this->app['page'];//当前页数
        $size = $this->app['size'];//每页显示数量

        $banner_model = new Model();
        $list = $banner_model
            ->limit(($page-1)*$size, $size)
            ->field('bannerID,title,thumb,sort,link,status,type')
            ->select();
        if ($list){
            $list->append(['statusText']);
        }
        $count = $banner_model->count();   //总条数

        return Common::rm(1, '操作成功', [
            'count'=>$count,
            'list'=>$list
        ]);
    }

    /*
     * @name 更改产品状态
     * @param status
     * @param bannerID
     */
    public function setStatus(){
        $model = Model::where('bannerID',$this->app['bannerID'])->update(['status' => $this->app['status']]);
        if ($model){
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }
    }

    //获取banner信息
    public function getinfo() {
        $model = new Model();
        $params_where['bannerID'] = $this->app['bannerID'];
        $info = $model
            ->field('bannerID,title,thumb,sort,link,status,type')
            ->find($params_where);
        return Common::rm(1,'获取成功',$info);
    }

    //新增banner图片
    public function add() {
        $params['title'] = $this->app['title'];
        $params['thumb'] = $this->app['thumb'];
        $params['sort'] = $this->app['sort'];
        //$params['productID'] = $this->app['productID'];
        $params['link'] = $this->app['link'];
        $params['status'] = $this->app['status'];
        $params['type'] = $this->app['type'];

      /*  $count = (new Model())->where(['type' => 1])->count();

        if($count >= config('count.banner_count_max')){
            return Common::rm(1, 'banner个数不能大于'.config('count.banner_count_max'));
        }*/

        $crate_result = Model::create($params);

        if ($crate_result){
            return Common::rm(1, '新增成功');
        }else{
            return Common::rm(-1,'新增失败');
        }
    }

    //修改广告图
    public function modify(){
        $params['title'] = $this->app['title'];
        $params['thumb'] = $this->app['thumb'];
        $params['sort'] = $this->app['sort'];
        //$params['productID'] = $this->app['productID'];
        $params['link'] = $this->app['link'];
        $params['status'] = $this->app['status'];
        $params['type'] = $this->app['type'];

        $params_where['bannerID'] = $this->app['bannerID'];

        $model = new Model();
        $save_result = $model->save($params,$params_where);

        if ($save_result){
            return Common::rm(1,'更新成功');
        }else{
            return Common::rm(-1,'更新失败');
        }
    }

    /*
     * @name 删除banner图
     * @param bannerIDS
     */
    public function delBanner(){
        //$this->app['bannerIDS'] = [20];
        $res = Model::destroy($this->app['bannerIDS']);
        if($res){
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }
    }

}