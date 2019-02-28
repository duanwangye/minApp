<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/11/29
 * Company:财联集惠
 */

namespace app\core\service;

class Finance
{
  private $platform;

  public function __construct(){
      vendor('nonshang.Gold');
      $this->platform = new \Gold();
  }

  //下单
  public function order(&$msg, \Trade $trade){
      vendor('payMode.Trade');
      return $this->platform->order($msg,$trade);
  }

}