<?php
/**
 * 商城板块初始化文件
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */

define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
if (!@include(dirname(dirname(__FILE__)).'/shopwt.php')) exit('shopwt.php isn\'t exists!');

define('APP_SITE_URL', AGENTS_SITE_URL);
define('TPL_NAME',TPL_ADMIN_NAME);
define('AGENTS_TEMPLATES_URL',AGENTS_SITE_URL.'/templets/'.TPL_NAME);
define('AGENTS_STATIC_URL',AGENTS_SITE_URL.'/static');
define('SHOP_TEMPLATES_URL',BASE_SITE_URL.'/templets/'.TPL_NAME);
define('BASE_TPL_PATH',BASE_PATH.'/templets/'.TPL_NAME);
if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');
Base::run();
