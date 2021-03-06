<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\agent\logic;

use app\core\model\UserProgram;
use tool\Common;

class User extends Base
{
    //获取团队人数
    public function getTeamCount()
    {
        $params['agentId'] = $this->master['masterID'];
        $userProgram_model = new UserProgram();
        $user_list = $userProgram_model->where($params)->select()->toArray();
        $user_info = array_unique(array_column($user_list, 'userID'));
        return Common::rm(1, '查询成功',count($user_info));
    }
}