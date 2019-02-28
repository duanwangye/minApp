<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/05
 * Company:财联集惠
 */

namespace app\api\logic;
use app\core\model\Banner;
use app\core\model\Product;
use app\core\model\Master;
use app\core\model\ProductClass;


use think\Db;
use tool\Common;

class Index extends Base
{
    /*
     * @name Banner 列表
     */
    public function getBannerList(){
        //首页banner图
        $banner = Banner::where('status',1)->order('sort asc')->select();
        $adver = [];
        $bannerList = [];
        foreach ($banner as $k => $val){
            if ($val['type'] == 2){   //广告
                $adver[$k]['title'] = $val['title'];
                $adver[$k]['thumb'] = $val['thumb'];
                $adver[$k]['link'] = $val['link'];
            }
            if ($val['type'] == 1){ //banner
                $bannerList[$k]['title'] = $val['title'];
                $bannerList[$k]['thumb'] = $val['thumb'];
                $bannerList[$k]['link'] = $val['link'];
            }
        }
        $adver = array_merge($adver);
        $bannerList = array_merge($bannerList);

        return Common::rm(1,'操作成功',$bannerList);
    }

    /*
     * @name 关键字搜索及分类搜索
     * @param keyWord 关键字
     * @param classID
     * @return  产品列表
     */
    public function search(){
        $this->app['pageIndex'] ;
        $this->app['pageItemCount'];
        $map['areaCode'] = $this->app['areaCode'] ;
        $mapProject['status'] = 1;
        if (isset($this->app['keyWord']) && $this->app['keyWord']){  //关键字
            $mapProject['productName'] = ['like','%'.$this->app['keyWord'].'%'];
        }
        if(isset($this->app['classID']) && $this->app['classID']){
            $mapProject['classID'] = $this->app['classID'];
        }
        $data = Master::with(['productList' => function($query) use($mapProject){
            $query->where($mapProject)->order('sort asc')->limit(($this->app['pageIndex'] -1)*$this->app['pageItemCount'],$this->app['pageItemCount']);
        }])->where($map)->select();
        $productList = [];
        if ($data){
            foreach ($data as $k => $val){
                $data = [];
                foreach ($val['product_list'] as $key => $value){
                    $data['productID'] = $value['productID'];
                    $data['productName'] = $value['productName'];
                    $data['titleImg'] = $value['titleImg'];
                    $data['salePrice'] = $value['salePrice'];
                    $data['marketPrice'] = $value['marketPrice'];
                    $data['promType'] = $value['promType'];
                    $data['sort'] = $value['sort'];
                    $productList[] = $data;
                }

            }
        }
        //升序排列的数组  array_multisort
        //返回输入数组中某个单一列的值 array_column
        array_multisort(array_column($productList,'sort'),SORT_ASC,$productList);

        return Common::rm(1,'操作成功',$productList);
    }

    /*
     * @name 得到更多分类
     * @return 分类列表
     */
    public function getClassList(){
        $list = Db::name('class')->where('status',1)->select();
        $new_list = [];
        foreach ($list as $k => $val){
            unset($val['updataTime']);
            unset($val['status']);
            unset($val['addTime']);
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
        return Common::rm(1, '操作成功',$list);
    }



}