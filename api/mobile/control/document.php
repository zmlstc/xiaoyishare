<?php
/**
 * 前台品牌分类
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class documentControl extends mobileHomeControl {
    public function __construct() {
        parent::__construct();
    }

    //获取一个文档
    public function getOneDocWt() {
        $id = intval($_POST['id']);
        $doc = Model('document')->getOneById($id);

        output_data($doc);
    }

    public function agreementWt() {
        $doc = Model('document')->getOneByCode('agreement');
        output_data($doc);
    }
}
