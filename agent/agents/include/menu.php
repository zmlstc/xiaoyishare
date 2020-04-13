<?php
/**
 * 菜单
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

$_menu['agents'] = array(
        'name' => '商城',
        'child' => array(
                
                array(
                        'name' => '店铺',
                        'child' => array(
								'store' => '店铺管理',
								'store_class' => '分类管理',
                        )),
                array(
                        'name' => '交易',
                        'child' => array(
                                'order' => '交易管理',
                                'partner' => '评价管理',
                        )),
/*                 array(
                        'name' => '运营',
                        'child' => array(
                                'partner' => '优惠券',
                                'partner' => '积分兑换',
                        )),
                array(
                        'name' => '广告',
                        'child' => array(
                                'partner' => '广告管理',
                        )),
                array(
                        'name' => '权限',
                        'child' => array(
                                'partner' => '权限设置',
                                'partner' => '操作日志',
                        )),
                array(
                        'name' => '统计',
                        'child' => array(
                                'stat_general' => '概述统计',
                                'stat_trade' => '销量统计',
                                'stat_store' => '店铺统计',
                                'stat_goods' => '商品统计',
                                'stat_aftersale' => '售后统计',
                                'stat_marketing' => '营销统计',
                        )) */
		)
);

