<?php
/**
 * 微信支付通知地址
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh o pwt.c om
 * @link       交流群号：58219240
 */

$_GET['w'] = 'payment';
$_GET['t'] = 'notify';
$_GET['payment_code'] = 'wxminipay';

//$data = json_encode($_POST).'---------------'.json_encode($_GET).'============================'.__DIR__ . '/../../../index.php';
//$data .= json_encode(file_get_contents('php://input'));
 //file_put_contents(__DIR__ . '/not_log.txt', var_export($data, true), FILE_APPEND | LOCK_EX);
$_SERVER['PATH_INFO'] = 'payment/notify';
require __DIR__ . '/../../../index.php';
