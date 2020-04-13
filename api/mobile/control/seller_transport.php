<?php
/**
 * 商家运费模板
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class seller_transportControl extends mobileSellerControl{

    public function __construct() {
        parent::__construct();
    }

    public function indexWt() {
        $this->transport_listWt();
    }

    /**
     * 返回商家店铺商品分类列表
     */
    public function transport_listWt() {
        $model_transport = Model('transport');
        $transport_list = $model_transport->getTransportList(array('store_id'=>$this->store_info['store_id']));
        output_data(array('transport_list' => $transport_list));
    }
}
