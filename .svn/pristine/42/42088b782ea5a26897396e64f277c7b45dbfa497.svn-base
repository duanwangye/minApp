<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\admin\logic;

use app\core\model\MasterGroup;
use app\core\model\MasterRole;
use tool\Common;
use app\core\model\MasterAuth;
class Auth extends Base
{

    //新增角色
    public function addRole() {
        $params['name'] = $this->app['name'];
        $create_result = MasterRole::create($params);
        if($create_result) {
            return Common::rm(1,'新增成功');
        }else{
            return Common::rm(-1,'新增失败');
        }
    }

    //新增角色名
    public function modifyRole() {
        $params['name'] = $this->app['name'];
        $params_where['masterRoleID'] = $this->app['masterRoleID'];
        $role_model= new MasterRole();

        $save_result = $role_model->save($params,$params_where);
        if($save_result) {
            return Common::rm(1,'新增成功');
        }else{
            return Common::rm(-1,'新增失败');
        }
    }

    //后台用户与角色绑定
    public function addAdministrator() {
        $params['masterID'] = $this->app['masterID'];
        $params['masterRoleID'] = $this->app['masterRoleID'];

        $create_result = MasterGroup::create($params);
        if($create_result) {
            return Common::rm(1,'新增成功');
        }else{
            return Common::rm(-1,'新增失败');
        }
    }

    //修改后台用户与角色绑定
    public function modifyAdministrator() {
        $params['masterID'] = $this->app['masterID'];
        $params['masterRoleID'] = $this->app['masterRoleID'];

        $params_where['groupID'] = $this->app['groupID'];

        $group_model= new MasterGroup();
        $save_result = $group_model->save($params,$params_where);
        if($save_result) {
            return Common::rm(1,'修改成功');
        }else{
            return Common::rm(-1,'修改失败');
        }
    }

}