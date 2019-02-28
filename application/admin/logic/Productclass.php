<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use app\core\model\Product;
use think\Db;
use tool\Common;
use app\core\model\ProductClass as Model;
class Productclass extends Base
{
    //获取分类列表
    public function getlist() {
        $list = Db::name('class')->select();
        $new_list = [];
        foreach ($list as $k => $val){
            $status = ['0' => '未启用','1' => '已启用'];
            $val['statusText'] = $status[$val['status']];
            $val['addTime'] = date('Y-m-d H:i:s', $val['addTime']);
            unset($val['updataTime']);
            if ($val['parentID'] == 0){
                $new_list[$k] = $val;
                $new_list[$k]['child'] = [];
            }
           foreach ($new_list as $key => $value){
                if ($value['classID'] == $val['parentID']){
                    $new_list[$key]['child'][] = $val;
                }
           }
        }
        $list = array_merge($new_list);
        //$arr = array_reverse($arr);
        return Common::rm(1, '操作成功', [
            'list'=>$list
        ]);
    }

    /*
     * @name 修改分类状态
     * @param status 0禁用 1启用
     * @param classID 分类ID
     */
    public function updateStatus(){
        $model = Model::where('classID',$this->app['classID'])->update(['status' => $this->app['status']]);
        if ($model){
            return Common::rm(1,'操作成功');
        }
    }

    /*
     * @name 得到二级分类
     * @param classID 分类ID
     * @return 二级分类列表
     */
    public function getSecondList(){
        $list = Model::where('parentID',$this->app['classID'])->select();
        if ($list){
            $list->append(['statusText'])->visible(['classID','className','parentID','addTime','status']);
        }

        return Common::rm(1,'操作成功',$list);
    }

    /*
   * @name 分类删除
   * @param ClassIDS
   */
    public function delClass(){
        $product = Product::where('classID','in',$this->app['classIDS'])->select();
         if (!$product->isEmpty()){
             return Common::rm(-2,'分类下面存在产品，无法删除');
         }
        $class = Model::where('parentID','in',$this->app['classIDS'])->select();
        if (!$class->isEmpty()){
            return Common::rm(-2,'分类下面存在子分类，无法直接删除');
        }
        $res = Model::destroy($this->app['classIDS']);
        if ($res){
            return Common::rm(1,'操作成功');
        }else{
            return Common::rm(-2,'操作失败');
        }

    }

    /*
     * @name 新增/修改分类
     * @param classID  分类ID
     * @param className  分类名称
     * @param parentID   父级ID
     */
    public function addClass(){
      /*  $this->app['className'] = 99;
        $this->app['parentID'] = 0;
        $this->app['status'] = 1;*/
        //不存在新增 存在更新
        if (isset($this->app['classID']) && $this->app['classID']){
            $model = Model::where('classID',$this->app['classID'])->update([
                'className' => $this->app['className'],
                'status' => $this->app['status'],
                'parentID' => $this->app['parentID'],
                'icon' => $this->app['icon'],
                'color' => $this->app['color'],
            ]);
            if ($model){
                return Common::rm(1,'操作成功');
            }else{
                return Common::rm(-2,'操作失败');
            }
        }else{
            $model = Model::create([
                'className' => $this->app['className'],
                'icon' => $this->app['icon'],
                'color' => $this->app['color'],
                'status' => $this->app['status'],
                'parentID' => $this->app['parentID']?$this->app['parentID']:0,
            ]);
            if ($model){
                return Common::rm(1,'操作成功');
            }

        }

    }



}