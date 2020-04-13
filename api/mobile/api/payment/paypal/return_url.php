<?php
/**
 * paypal支付返回地址
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh op wt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
$_GET['w']	= 'payment';
$_GET['t']		= 'return';
$_GET['payment_code'] = 'paypal';
$_GET['extra_common_param'] = 'real_order';
$_GET['out_trade_no'] = $_POST['invoice'];
$_GET['trade_no'] = $_POST['txn_id'];
require_once(dirname(__FILE__).'/../../../index.php');
?>