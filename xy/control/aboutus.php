<?php
/**
 * 控制台
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class aboutusControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('dashboard');
    }

    public function indexWt() {
        $this->aboutusWt();
    }

    /**
     * 关于我们
     */
    public function aboutusWt(){
		$version = BASE_PATH."/version.txt";
		if(is_file($version)){
			$contents = file_get_contents($version);			
		}
        $s_date = substr(C('setup_date'),0,10);
        Tpl::output('v_date',$contents);
        Tpl::output('s_date',$s_date);
        Tpl::showpage('aboutus', 'null_layout');
    }

}
