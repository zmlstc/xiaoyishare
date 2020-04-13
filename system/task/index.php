<?php
/**
 * 计划任务触发 
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */
//$_SERVER['argv'][1] = $_GET['w'];
//@$_SERVER['argv'][2] = $_GET['t'];


//if (empty($_SERVER['argv'][1])) exit('Access Denied By ShopWT');

define('APP_ID','task');
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
define('TRANS_MASTER',true);
require __DIR__ . '/../../shopwt.php';

if (PHP_SAPI == 'cli') {
     $_GET['w'] = $_SERVER['argv'][1];
     $_GET['t'] = empty($_SERVER['argv'][2]) ? 'index' : $_SERVER['argv'][2];
}
if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');

Base::run();
