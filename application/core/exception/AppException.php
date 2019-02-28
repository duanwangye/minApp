<?php
/**
 * Created by PhpStorm.
 * User: zhangdadan
 * Date: 2017/6/6
 * Time: 23:09
 */

namespace app\core\exception;

class AppException extends \RuntimeException
{
    private $statusCode;
    private $headers;

    public function __construct($code = 0, $message = null, \Exception $previous = null, array $headers = [])
    {
        $this->statusCode = 200;
        $this->headers    = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode() {
        return $this->statusCode;
    }
}