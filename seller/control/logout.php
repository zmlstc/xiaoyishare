<?php
/**
 * 店铺卖家注销
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class logoutControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();
    }

    public function indexWt() {
        $this->logoutWt();
    }

    public function logoutWt() {
        $this->recordSellerLog('注销成功');
        // 清除店铺消息数量缓存
        setWtCookie('storemsgnewnum'.$_SESSION['seller_id'],0,-3600);
        session_destroy();
        redirect(urlSeller('login'));
    }

}
