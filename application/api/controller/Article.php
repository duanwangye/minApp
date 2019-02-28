<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/09/21
 * Company:财联集惠
 */

namespace app\api\controller;

use app\api\logic\Article as Logic;

class Article extends Base
{
    public $logic;

    public function __initialize() {
        $this->logic = new Logic($this->request);
    }

   public function getArticleList() {
       $function = __FUNCTION__;
       return json($this->logic->$function());
   }


    public function getArticleDetail(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }

    public function payArticle(){
        $function = __FUNCTION__;
        return json($this->logic->$function());
    }
}