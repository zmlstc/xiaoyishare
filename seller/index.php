<?php
/**
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */
define('APP_ID','seller');
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));

require __DIR__ . '/../shopwt.php';

define('APP_SITE_URL',SELLER_SITE_URL);
define('TPL_NAME','shopwt');
define('SELLER_TEMPLATES_URL',SELLER_SITE_URL.'/templets/'.TPL_NAME);
define('SELLER_TPL_PATH',BASE_PATH.'/templets/'.TPL_NAME);
if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');
Base::run();
