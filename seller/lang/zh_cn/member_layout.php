<?php
defined('ShopWT') or exit('Access Denied By ShopWT');
$lang	= array(
/**
* header中的文字
*/
		'wt_hello'                               => '您好',
		'wt_logout'                              => '退出',
		'wt_guest'                               => '游客',
		'wt_login'                               => '登录',
		'wt_register'                            => '注册',
		'wt_user_center'                         => '我的商城',
		'wt_myvoucher'                           => '我的代金券',
		'wt_store_inform'                        => '被举报禁售',
		'wt_my_flea'                             => '我的闲置',
		'wt_message'                             => '站内信',
		'wt_help_center'                         => '帮助中心',
		'wt_more_links'                          => '更多链接',
		'wt_index'                               => '首页',
		'wt_current_position'                    => '当前位置',
		'wt_search'                              => '搜索',
		'wt_member_path_goods_class'             => '商品分类',
		'wt_store_class'                         => '店铺分类',
		'wt_cart'                                => '购物车',
		'wt_kindof_goods'                        => '种商品',
		'wt_favorites'                           => '我的收藏',
/**
* 左侧导航与右侧小导航中的路径文字
*/
		'wt_member_order_manage'                 => '交易操作',
		'wt_member_myorder'                 		=> '已买到的商品',
		'wt_member_points_manage'                 => '我的积分',
		'wt_member_consult_complain'                 => '咨询与维权',
		'wt_member_path_album'                   => '图片空间',
		'wt_member_path_my_album'                => '我的相册',
		'wt_member_path_album_pic_list'          => '图片列表',
		'wt_member_path_album_pic_info'          => '图片详细',
		'wt_member_path_index'                   => '账户概览',
		'wt_member_path_profile'                 => '个人资料',
		'wt_member_path_points'                  => '积分明细',
		'wt_member_path_pointorder'              => '已兑换的商品',
		'wt_member_path_points_exchange'         => '积分兑换',
		'wt_member_path_pointorder_list'         => '兑换列表',
		'wt_member_path_pointorder_info'         => '兑换详细',
		'wt_member_path_avatar'                  => '更换头像',
		'wt_member_path_baseinfo'                => '基本信息',
		'wt_member_path_passwd'                  => '修改密码',
		'wt_member_path_email'                   => '修改电子邮件',
		'wt_member_path_message'                 => '站内信',
		'wt_member_path_receivemsg'              => '收到消息',
		'wt_member_path_privatemsg'              => '已发送消息',
		'wt_member_path_systemmsg'               => '系统消息',
		'wt_member_path_sellermsg'               => '商家消息',
		'wt_member_path_sendmsg'                 => '发送站内信',
		'wt_member_path_showmsg'                 => '查看站内信',
		'wt_member_path_close'                 	=> '私信',
		'wt_member_path_friend'                  => '好友',
		'wt_member_path_buyerindex'              => '买家首页',
		'wt_member_path_myspace'              	=> '个人主页',
		'wt_member_path_setting'              	=> '设置',
		'wt_member_path_accountsettings'         => '账户设置',
		'wt_member_path_followlist'              => '关注列表',
		'wt_member_path_myorder'                 => '我的订单',
		'wt_member_path_myvoucher'               => '我的代金券',
		'wt_member_path_myinform'                => '我的举报',
		'wt_member_path_myorder_list'            => '订单列表',
		'wt_member_path_show_order'              => '订单详情',
		'wt_member_path_robbuy'                => '我的抢购',
		'wt_member_path_consult'                 => '我的咨询',
		'wt_member_path_consult_list'            => '全部咨询',
		'wt_member_path_to_reply'                => '未回复咨询',
		'wt_member_path_replied'                 => '已回复咨询',
		'wt_member_path_favorites'               => '我的收藏',
		'wt_member_path_collect_goods'           => '收藏商品',
		'wt_member_path_collect_store'           => '收藏店铺',
		'wt_member_path_address'                 => '收货地址',
		'wt_member_path_evalmanage'              => '评价晒单',
		'wt_member_path_address_list'            => '地址列表',
		'wt_member_path_coupon'                  => '我的优惠券',
		'wt_member_path_coupon_list'             => '优惠券列表',
		'wt_member_my_store'                     => '我的店铺',
		'wt_member_path_goods_manage'            => '商品管理',
		'wt_member_path_goods_sell'              => '商品发布',
		'wt_member_path_goods_selling'           => '出售中的商品',
		'wt_member_path_goods_storage'           => '仓库中的商品',
		'wt_member_path_goods_state'             => '违规的商品',
		'wt_member_path_goods_verify'            => '等待审核的商品',
		'wt_member_path_goods_list'              => '出售中的商品',
		'wt_member_path_add_goods'               => '新增商品',
		'wt_member_path_add_goods_step1'         => '选择商品所在分类',
		'wt_member_path_add_goods_stpe2'         => '填写商品详细信息',
		'wt_member_path_add_goods_step3'         => '商品发布成功',
		'wt_member_path_edit_goods'              => '编辑商品',
		'wt_member_path_brand_list'              => '品牌申请',
		'wt_member_path_store_info'              => '店铺信息',
		'wt_member_path_bind_class'              => '经营类目',
		'wt_member_path_store_reopen'            => '申请续签',
		'wt_member_path_batch_edit'              => '批量编辑',
		'wt_member_path_robbuy_manage'         => '抢购管理',
		'wt_member_path_consult_manage'          => '咨询管理',
		'wt_member_path_store_goods_class'       => '分类管理',
		'wt_member_path_store_spec'              => '商品规格',
		'wt_member_path_store_spec_add'          => '添加规格',
		'wt_member_path_goods_class'             => '商品分类',
		'wt_member_path_store_order'             => '订单管理',
		'wt_member_path_bill_manage'             => '结算管理',
		'wt_member_path_real_order'              => '实收订单',
		'wt_member_path_tkth_order'              => '退款订单',
		'wt_member_path_deliver'             	=> '发货',
		'wt_member_path_daddress'             	=> '发货设置',
		'wt_member_path_store_order'             => '订单管理',
		'wt_member_path_store_refund'            => '退款记录',
		'wt_member_path_store_return'            => '退货记录',
		'wt_member_path_all_order'               => '所有订单',
		'wt_member_path_order_pay'               => '待付款',
		'wt_member_path_order_no_shipping'       => '待发货',
		'wt_member_path_order_shipping'          => '待收货',
		'wt_member_path_order_finish'            => '已完成',
		'wt_member_path_order_cancel'            => '已取消',
		'wt_member_path_store_setting'           => '店铺设置',
		'wt_member_path_store_slide'				=> '幻灯片设置',
		'wt_member_path_store_printsetup'		=> '打印设置',
		'wt_member_path_store_theme'             => '主题设置',
		'wt_member_path_valid_theme'             => '可用主题',
		'wt_member_path_payment'                 => '支付方式',
		'wt_member_path_payment_list'            => '支付方式设置',
		'wt_member_path_transport'               => '物流工具',
		'wt_member_path_postage'               	=> '售卖区域',
		'wt_member_path_store_navigation'        => '导航管理',
		'wt_member_path_navigation_list'         => '导航列表',
		'wt_member_path_navigation_add'          => '新增导航',
		'wt_member_path_navigation_edit'         => '编辑导航',
		'wt_member_path_store_partner'           => '合作伙伴管理',
		'wt_member_path_partner_list'            => '合作伙伴列表',
		'wt_member_path_store_coupon'            => '优惠券管理',
		'wt_member_path_store_voucher'           => '代金券管理',
		'wt_member_path_store_inform'            => '被举报禁售',
		'wt_member_path_xianshi'                 => '限时折扣',
		'wt_member_path_mansong'                 => '满即送',
		'wt_member_path_coupon_list'             => '优惠券列表',
		'wt_member_path_store_activity'          => '活动管理',
		'wt_member_path_activity_list'           => '活动列表',
		'wt_member_path_activity_apply'          => '参与活动',
		'wt_member_path_group_list'              => '抢购列表',
		'wt_member_path_collect_list'            => '收藏商品',
		'wt_member_path_collect_store'           => '收藏店铺',
		'wt_member_path_order_list'              => '订单列表',
		'wt_member_path_daddress_list'           => '收货地址',
		'wt_member_path_default_express'         => '默认物流公司',
		'wt_member_path_all_order'               => '所有订单',
		'wt_member_path_wait_pay'                => '待付款',
		'wt_member_path_wait_send'               => '待发货',
		'wt_member_path_sent'                    => '已发货',
		'wt_member_path_finished'                => '已完成',
		'wt_member_path_canceled'                => '已取消',
		'wt_member_path_payment_list'            => '支付方式设置',
		'wt_member_path_nav_list'                => '导航列表',
		'wt_member_path_partner_list'            => '合作伙伴列表',
		'wt_member_path_store_config'            => '店铺设置',
		'wt_member_path_store_callcenter'        => '客服中心',
		'wt_member_path_store_cert'              => '认证设置',
		'wt_member_path_store_map'               => '店铺地图',
		'wt_member_path_valid_theme'             => '可用主题',
		'wt_member_path_join_activity'           => '参与活动',
		'wt_member_path_all_consult'             => '全部咨询',
		'wt_member_path_unreplied_consult'       => '未回复咨询',
		'wt_member_path_replied_consult'         => '已回复咨询',
		'wt_member_path_new_group'               => '新增抢购',
		'wt_member_path_edit_group'              => '编辑抢购',
		'wt_member_path_cancel_group'            => '取消抢购',
		'wt_member_path_group_intro'             => '抢购说明',
		'wt_member_path_watermark'               => '水印管理',
		'wt_member_path_qq_bind'                 => 'QQ绑定',
		'wt_member_path_sina_bind'               => '新浪微博绑定',
		'wt_member_path_complain'                => '投诉管理',

		'wt_member_path_seller_refund'           => '退款审核',
		'wt_member_path_buyer_refund'            => '退款申请',
		'wt_member_path_buyer_return'            => '退货申请',
		'wt_member_path_evaluatemanage'          => '评价晒单',

		'wt_member_path_deliver'           		=> '发货',
		'wt_member_path_deliverno'           	=> '待发货',
		'wt_member_path_delivering'          	=> '发货中',
		'wt_member_path_delivered'           	=> '已收货',
		'wt_member_path_deliver_info'           	=> '物流详情',

		'wt_member_path_store_sns'				=> '店铺动态',
		'wt_member_path_store_sns_browse'		=> '浏览店铺动态',
		'wt_member_path_store_sns_add'			=> '发布动态',
		'wt_member_path_store_sns_setting'		=> '动态设置',
/**
 * 闲置
 */
		'wt_member_path_flea'					=> '我的闲置',
		'wt_member_path_flea_list'               => '闲置列表',
		'wt_member_path_add_flea_goods'          => '发布闲置',
		'wt_member_path_edit_flea'               => '编辑闲置',
		'wt_member_path_collect_flea'            => '收藏闲置',
		'wt_member_path_edit_flea'               => '编辑闲置',
		'wt_member_path_flea_favorites'			=> '闲置收藏',
		'wt_member_path_flea_favorites_list'		=> '收藏列表',

/**
* footer中的文字
*/
		'wt_index'                               => '首页',
		'wt_page_execute'                        => '页面执行',
		'wt_second'                              => '秒',
		'wt_query'                               => '查询',
		'wt_times'                               => '次',
		'wt_online'                              => '在线',
		'wt_person'                              => '人',
		'wt_enabled'                             => '已启用',
		'wt_disabled'                            => '已禁用',
		'wt_memory_cost'                         => '占用内存',
/**
* 页面中的常用文字
*/
		'wt_select_all'                          => '全选',
		'wt_ensure_del'                          => '您确定要删除吗?',
		'wt_ensure_cancel'                       => '您确定要取消吗?',
		'wt_del'                                 => '删除',
		'wt_del_&nbsp'                           => '删&nbsp;除',
		'wt_message'                             => '站内消息',
		'wt_no_goods'                            => '没有符合条件的商品',
		'wt_no_store'                            => '没有符合条件的店铺',
		'wt_handle'                              => '操作',
		'wt_edit'                                => '编辑',
		'wt_ok'                                  => '确定',
		'wt_cancel'                              => '取消',
		'wt_display'                             => '显示',
		'wt_new'                                 => '新增',
		'wt_search'                              => '搜索',
		'wt_submit'                              => '提交',
		'wt_please_choose'                       => '-请选择-',
		'wt_yes'                                 => '是',
		'wt_no'                                  => '否',
		'wt_all'                                 => '全部',
		'wt_sort'                                => '排序',
		'wt_view'                                => '查看',
		'wt_detail'                              => '详细',
		'wt_close'                               => '关闭',
		'wt_link'                                => '链接地址',
		'wt_explain'                             => '说明',
		'wt_sale'                           => '促销活动',
		'wt_robbuy_flag'                       => '团',
		'wt_robbuy'                            => '抢购活动',
		'wt_robbuy_view'                       => '查看',
		'wt_mansong_flag'                        => '满',
		'wt_mansong'                             => '满即送',
		'wt_xianshi_flag'                        => '折',
		'wt_yuan'                                => '元',
		'wt_xianshi'                             => '限时折扣',
		'wt_bundling_flag'						=> '组',
		'wt_bundling'							=> '优惠套装',
		'wt_state' => '状态',
		'wt_manage' => '管理',
		'wt_end' => '结束',
		'wt_normal' => '正常',
		'wt_invalidation' => '失效',
		'wt_back' => '返回',

		'sns_from'		=> '来自',
		'sns_shop'		=> '商城',
/**
* 直通车菜单
*/
		'wt_member_path_ztc_add'                 => '添加申请',
		'wt_member_path_ztc_glist'               => '商品列表',
		'wt_member_path_ztc_glog'                => '金币日志',
/**
 * 店铺审核提示文字
 */
		'wt_store_state_fail_reason'				=> '店铺被管理员关闭或开店申请未通过审核，原因：',
		'wt_store_administrator_not_fill_in'		=> '管理员未填写',
		'wt_store_apply_open'					=> '点击申请开通店铺',
		'wt_period'								=> '。',
		'wt_store_apply_open_confirm'			=> '确定按管理员要求改正了吗？',
		'wt_store_administrator_is_audit'		=> '开店申请审核中。',
/**
* 预存款菜单
*/
		'wt_member_path_predeposit_title'        => '预存款',
		'wt_member_path_predepositrecharge'      => '预存款充值',
		'wt_member_path_predeposit_rechargeadd'  => '充值添加',
		'wt_member_path_predeposit_rechargelist' => '充值明细',
		'wt_member_path_predeposit_rechargeinfo' => '充值详细',
		'wt_member_path_predeposit_loglist'      => '账户余额',
		'wt_member_path_predepositlog'           => '预存款明细',
		'wt_member_path_predepositcash'          => '预存款提现',
		'wt_member_path_predeposit_cashadd'      => '提现申请',
		'wt_member_path_predeposit_cashlist'     => '余额提现',
		'wt_member_path_predeposit_cashinfo'     => '提现详细',
/**
* 订单评价
*/
		'wt_member_path_evaluateadd'            	=> '订单商品评价',
/**
 * 优惠套装
 */
		'wt_member_path_bundling'					=> '优惠套装',
		'wt_member_path_bundling_list'				=> '活动列表',
		'wt_member_path_bundling_quota_add'			=> '购买套餐',
		'wt_member_path_bundling_add'				=> '添加活动',
		'wt_member_path_bundling_edit'				=> '编辑套餐',
		'wt_member_path_bundling_purchase_history'	=> '购买套餐记录',

/**
 * 左侧菜单
 */
		'wt_updateavatar'                    => '上传新头像',
		'wt_edituserinfo'                    => '编辑用户信息',
		'wt_buyercredit'                     => '买家信用',
		'wt_pointsnum'                    	=> '可用积分',
		'wt_predepositnum'                   => '账户余额',
		'wt_tradeinfo'                   	=> '交易信息',
		'wt_order_waitpay'                   => '待付款',
		'wt_order_receiving'                 => '待收货',
		'wt_order_waitevaluate'              => '待评价',
		'wt_member_path_sharemanage'     	=> '分享绑定',

/**
 * 店铺自动发布默认语言
 * 必须5个
 */
		'wt_store_auto_share_new1'			=> '亲，我家又上新宝贝了。',
		'wt_store_auto_share_new2'			=> '亲，为您推荐一款本店新上宝贝。',
		'wt_store_auto_share_new3'			=> '亲，我家又上新宝贝了！快来逛逛看更多吧。',
		'wt_store_auto_share_new4'			=> '亲，我家又上新宝贝了。',
		'wt_store_auto_share_new5'			=> '亲，为您推荐一款本店新上宝贝。',

		'wt_store_auto_share_xianshi1'		=> '限时折扣，玩得就是心跳。',
		'wt_store_auto_share_xianshi2'		=> '省心又省钱，活动促销中。',
		'wt_store_auto_share_xianshi3'		=> '只买对的，不买贵的，宝贝限时折扣中。',
		'wt_store_auto_share_xianshi4'		=> '宝贝限时折扣中，性价比超高哟。',
		'wt_store_auto_share_xianshi5'		=> '折扣限时，不买不死心哇~',

		'wt_store_auto_share_mansong1'		=> '满即送，快行动！',
		'wt_store_auto_share_mansong2'		=> '不满不送，不送不买~',
		'wt_store_auto_share_mansong3'		=> '满啦就送啦，快快行动吧~',
		'wt_store_auto_share_mansong4'		=> '我家有满就送活动啦，亲快去查看下详情吧',
		'wt_store_auto_share_mansong5'		=> '我家有满就送活动啦，亲快去查看下详情吧',

		'wt_store_auto_share_bundling1'		=> '搭配购买更省钱',
		'wt_store_auto_share_bundling2'		=> '搭配的是品质，捡到的是实惠！',
		'wt_store_auto_share_bundling3'		=> '搭配不错，省心省钱更不会错',
		'wt_store_auto_share_bundling4'		=> '省心又省钱，找套餐就对了！',
		'wt_store_auto_share_bundling5'		=> '省心又省钱，套餐促销中～',

		'wt_store_auto_share_robbuy1'		=> '打折的是价格，不打折的是品格！',
		'wt_store_auto_share_robbuy2'		=> '抢购进万家，实惠你我他。',
		'wt_store_auto_share_robbuy3'		=> '今天您团了吗？还没有？快来参加吧！',
		'wt_store_auto_share_robbuy4'		=> '让您足不出户，便宜到家，快来抢购吧！',
		'wt_store_auto_share_robbuy5'		=> '品质与价格的双优选择，快来看看吧。',
);