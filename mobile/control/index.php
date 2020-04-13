<?php
/**
 * 手机端首页控制
 * 
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh op wt.c om
 * @link       
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 *
 */

defined('ShopWT') or exit('Access Denied By ShopWT');
class indexControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
    } 

    /**
     * 首页
     */
    public function indexWt() {
		//echo time();exit;
			//https://www.fhlego.com/m/#/pages/store/pay?store_id=9
		if(!empty($_GET['pay_sid'])&& $_GET['pay_sid']!=''&&intval($_GET['pay_sid'])>0){
			@header('Location:https://www.fhlego.com/m/#/pages/store/pay?store_id='.intval($_GET['pay_sid']));
		}else if(!empty($_GET['store_id'])&& $_GET['store_id']!=''&&intval($_GET['store_id'])>0){
			@header('Location:https://www.fhlego.com/m/#/pages/store/index?store_id='.intval($_GET['store_id']));
		}else if(!empty($_GET['ubm'])&& $_GET['ubm']!=''){
			@header('Location:https://www.fhlego.com/m/#/pages/member/register/userreg?bm='.trim($_GET['ubm']));
		}else if(!empty($_GET['sbm'])&& $_GET['sbm']!=''){
			@header('Location:https://www.fhlego.com/m/#/pages/member/register/shopreg?bm='.trim($_GET['sbm']));
		}
    }
	

	
}
