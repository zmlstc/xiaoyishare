<?php
/* *
 * 功能：支付宝服务器异步通知页面
 */


$_GET['w'] = 'payment';
$_GET['t']	= 'notify';
$_GET['payment_code']	= 'alipay';
$_SERVER['PATH_INFO'] = 'payment/notify';
require_once(dirname(__FILE__).'/../../../index.php');
?>
