<?php
/**
 * 微信支付通知地址
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh o pwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */

$_GET['w'] = 'payment';
$_GET['t'] = 'notify';
$_GET['payment_code'] = 'wxpay_h5';

require __DIR__ . '/../../../index.php';
