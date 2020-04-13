<?php
/**
 * 接收微信支付异步通知回调地址
 *
 * 
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
error_reporting(7);
$_GET['w']	= 'payment';
$_GET['t']		= 'wxpay_notify';
require_once(dirname(__FILE__).'/../../../index.php');
