<?php
/**
 * Created by PhpStorm.
 * User: zhangdadan
 * Date: 2017/6/6
 * Time: 23:09
 */


namespace app\core\exception;
use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\Response;
use tool\Common;

class Http extends Handle
{
    public function render(Exception $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            Response::create(Common::rj($statusCode, $e->getMessage()), 'json')->code($statusCode)->send();
        }
        else if ($e instanceof AppException) {
            $statusCode = $e->getStatusCode();
            Response::create(Common::rj($e->getCode(), $e->getMessage()), 'json')->code($statusCode)->send();
        }
        //TODO::开发者对异常的操作
        //可以在此交由系统处理
        return parent::render($e);
    }
}