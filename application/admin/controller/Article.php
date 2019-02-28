<?php
/**
 * Created by PhpStorm.
 * User: hlj
 * Date: 2018/11/10
 * Time: 13:35
 */
namespace app\admin\controller;

use app\admin\logic\Article as Logic;
class Article extends Base {
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

    //添加角色
    public function editorArticle() {
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getArticleList(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function getArticleDetail(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function delArticle(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}