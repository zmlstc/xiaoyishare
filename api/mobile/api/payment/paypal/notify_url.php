<?php
/**
 * paypal支付通知地址
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh op wt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
$_GET['w']	= 'payment';
$_GET['t']		= 'notify';
$_GET['payment_code'] = 'paypal';
$_POST['extra_common_param'] = 'real_order';
$_POST['out_trade_no'] = $_POST['invoice'];
$_POST['trade_no'] = $_POST['txn_id'];
require_once(dirname(__FILE__).'/../../../index.php');
?>