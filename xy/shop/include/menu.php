<?php
/**
 * 菜单
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

$_menu['shop'] = array(
        'name' => '商城',
        'child' => array(
                array(
                        'name' => '设置',
                        'child' => array(
                                'upload' => '上传设置',
                                'payment' => $lang['wt_pay_method'],
                                'message' => $lang['wt_message_set'],
								'inviteset'=>'推广佣金设置'
                        )),
                array(
                        'name' => $lang['wt_operation'],
                        'child' => array(
                                'rechargecard' => '充值卡管理',
                                'mall_consult' => '客服咨询管理',
                        )),
                array(
                        'name' => $lang['wt_store'],
                        'child' => array(
                                'store' => $lang['wt_store_manage'],
                                'store_class' => $lang['wt_store_class'],
                                'facility' => '设施管理',
                                'help_store' => '店铺帮助',
                                'store_predeposit' => '提现管理'
                        )),
                
                array(
                        'name' => '交易',
                        'child' => array(
                                'order' => $lang['wt_order_manage'],
                                'evaluate' => $lang['wt_goods_evaluate']
                        )),
                array(
                        'name' => $lang['wt_stat'],
                        'child' => array(
                                'stat_general' => $lang['wt_statgeneral'],
                                'stat_trade' => $lang['wt_stattrade'],
                                'stat_store' => $lang['wt_statstore'],
                                'stat_industry' => $lang['wt_statindustry'],
                                'stat_member' => $lang['wt_statmember'],
                        )),
                array(
                        'name' => '促销',
                        'child' => array(
                                'operation' => '促销设置',
                                'activity' => $lang['wt_activity_manage'],
                                'coupon' => '平台优惠券',
                                'voucher' => $lang['wt_voucher_price_manage'],
                                'pointprod'=>$lang['wt_pointprod'],
                        )),
                array(
                        'name' => '活动',
                        'child' => array(
                                'plat_act' => '平台活动',
                                'plat_actclass' => '活动分类',
                        )),
                array(
                        'name' => $lang['wt_member'],
                        'child' => array(
                                'member' => $lang['wt_member_manage'],
                                'sns_malbum' => $lang['wt_member_album_manage'],
                                'points' => $lang['wt_member_pointsmanage'],
                                'chat_log' => 'IM消息记录',
                                'predeposit' => $lang['wt_member_predepositmanage']
                        )),
                array(
                        'name' => '代理',
                        'child' => array(
                                'agencies' => '代理管理'
                        ))
));
