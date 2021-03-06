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

define('BASE_PATH',str_replace('\\','/',dirname(dirname(__FILE__))));
define('MODULES_BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
require __DIR__ . '/../../shopwt.php';

define('APP_SITE_URL', ADMIN_SITE_URL.'/mobile');
define('TPL_NAME',TPL_ADMIN_NAME);
define('ADMIN_TEMPLATES_URL',ADMIN_SITE_URL.'/templets/'.TPL_NAME);
define('ADMIN_STATIC_URL',ADMIN_SITE_URL.'/static');
define('SHOP_TEMPLATES_URL',BASE_SITE_URL.'/templets/'.TPL_NAME);
define('BASE_TPL_PATH',MODULES_BASE_PATH.'/templets/'.TPL_NAME);
define('MODULE_NAME', 'mobile');
if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');
$system='mobile';

Base::runadmin($system);
