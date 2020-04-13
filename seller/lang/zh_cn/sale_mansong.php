<?php
defined('ShopWT') or exit('Access Denied By ShopWT');
$lang	= array(

		'sale_unavailable' => '商品促销功能尚未开启',
		'sale_mansong' => '满即送',

		'sale_active_list' 	=> '活动列表',
		'sale_quota_list' 	=> '套餐列表',
		'sale_join_active' 	=> '添加活动',
		'sale_buy_product' 	=> '购买套餐',
		'sale_goods_manage' => '商品管理',
		'sale_add_goods' 	=> '添加商品',

		'state_new' => '新申请',
		'state_verify' => '已审核',
		'state_cancel' => '已取消',
		'state_verify_fail' => '审核失败',
		'mansong_quota_state_activity' => '正常',
		'mansong_quota_state_cancel' => '取消',
		'mansong_quota_state_expire' => '失效',
		'mansong_state_unpublished' => '未发布',
		'mansong_state_published' => '已发布',
		'mansong_state_cancel' => '已取消',
		'all_state' => '全部状态',

		'mansong_quota_start_time' => '开始时间',
		'mansong_quota_end_time' => '结束时间',
		'mansong_quota_times_limit' => '活动次数限制',
		'mansong_quota_times_published' => '已发布活动次数',
		'mansong_quota_times_publish' => '剩余活动次数',
		'mansong_quota_goods_limit' => '商品限制',
		'mansong_name' => '活动名称',
		'mansong_status' => '活动状态',
		'mansong_active_content' => '活动内容',
		'mansong_apply_date' => '套餐申请时间',
		'mansong_apply_quantity' => '申请数量(月)',
		'apply_date' => '申请时间',
		'apply_quantity' => '申请数量',
		'apply_quantity_unit' => '（包月）',
		'mansong_discount' => '折扣',
		'mansong_buy_limit' => '购买限制',

		'start_time' => '开始时间',
		'end_time' => '结束时间',
		'xianshi_list' => '限时折扣',
		'mansong_list' => '满即送',
		'mansong_add' => '添加活动',
		'mansong_quota' => '套餐列表',
		'mansong_apply' => '申请列表',
		'mansong_detail' => '活动详情',
		'mansong_quota_add' => '购买套餐',
		'mansong_quota_add_quantity' => '套餐购买数量',
		'mansong_quota_add_confirm' => '确认购买?您总共需要支付',
		'goods_add' => '添加商品',
		'choose_goods' => '选择商品',
		'goods_name' => '商品名称',
		'goods_store_price' => '商品价格',
		'mansong_goods_selected' => '已选商品',
		'mansong_publish' => '发布活动',
		'ensure_publish' => '确认发布该活动?',
		'level_price' => '单笔订单满',
		'level_discount' => '立减现金',
		'gift_name' => '送礼品',
		'mansong_price' => '购买满即送所需金额',
		'mansong_price_explain1' => '购买单位为月(30天)，一次最多购买12个月，购买后您可以发布满即送活动，但同时只能有一个活动进行',
		'mansong_price_explain2' => '每月您需要支付',
		'mansong_price_explain3' => '套餐时间从审批后的第二天零点开始计算',
		'mansong_add_explain1' => '满即送活动包括店铺所有商品，活动时间不能和已有活动重叠',
		'mansong_add_explain2' => '每个满即送活动最多可以设置3个价格级别，点击新增级别按钮可以增加新的级别，价格级别应该由低到高',
		'mansong_add_explain3' => '每个级别可以有减现金、送礼品2种促销方式，至少需要选择一种',
		'mansong_add_start_time_explain' => '开始时间不能为空且不能早于%s',
		'mansong_add_end_time_explain' => '结束时间不能为空且不能晚于%s',
		'mansong_discount_explain' => '折扣必须为0.1-9.9之间的数字',
		'mansong_buy_limit_explain' => '购买限制必须为正整数',
		'time_error' => '时间格式错误',
		'param_error' => '参数错误',
		'greater_than_start_time' => '结束时间必须大于开始时间',
		'mansong_price_error' => '不能为空且必须为正整数',
		'mansong_name_explain' => '活动名称最多为25个字符',
		'mansong_name_error' => '活动名称不能为空',
		'mansong_remark_explain' => '活动备注最多为100个字符',
		'mansong_quota_quantity_error' => '数量不能为空，且必须为1-12之间的整数',
		'mansong_quota_add_success' => '满即送套餐购买成功',
		'mansong_quota_add_fail' => '满即送套餐购买申请失败',
		'mansong_quota_current_error' => '您还没有购买"满即送"促销套餐，或该促销活动已经关闭。<br/>请先购买套餐活动，再查看活动列表。',
		'mansong_quota_current_error1' => '您当前的满即送套餐已用完，请等待下期套餐或购买新的套餐',
		'mansong_quota_current_error2' => '您已经购买了满即送套餐',
		'mansong_add_success' => '满即送活动添加成功',
		'mansong_add_fail' => '满即送活动添加失败',
		'mansong_goods_none' => '您还没有添加活动商品',
		'mansong_goods_add_success' => '满即送活动商品添加成功',
		'mansong_goods_add_fail' => '满即送活动商品添加失败',
		'mansong_goods_delete_success' => '满即送活动商品删除成功',
		'mansong_goods_delete_fail' => '满即送活动商品删除失败',
		'mansong_goods_cancel_success' => '满即送活动商品取消成功',
		'mansong_goods_cancel_fail' => '满即送活动商品取消失败',
		'mansong_goods_limit_error' => '已经超过了活动商品数限制',
		'mansong_goods_is_exist' => '该商品已经参加了本期满即送，请选择其它商品',
		'mansong_publish_success' => '满即送活动发布成功',
		'mansong_publish_fail' => '满即送活动发布失败',
		'mansong_cancel_success' => '满即送活动取消成功',
		'mansong_cancel_fail' => '满即送活动取消失败',
		'mansong_level_price_error' => '消费额必须为正整数',
		'mansong_level_discount_null' => '优惠金额不能为空',
		'mansong_level_discount_error' => '优惠金额必须为正整数',
		'mansong_level_gift_error' => '赠品名称不能为空',
		'mansong_level_rule_select_error' => '请至少选择一种促销方式',
		'mansong_level_error' => '高级别的销售额必须大于低级别',

		'setting_save_success' => '设置保存成功',
		'setting_save_fail' => '设置保存失败',
		'mansong_explain1' => '已参加限时折扣、抢购的商品，可同时参加满即送活动',

		'text_month' => '月',
		'text_gold' => '金币',
		'text_jian' => '件',
		'text_ci' => '次',
		'text_goods' => '商品',
		'text_normal' => '正常',
		'text_invalidation' => '失效',
		'text_default' => '默认',
		'text_add' => '添加',
		'text_back' => '返回',
		'text_level' => '级别',
		'text_level_condition' => '消费满',
		'text_reduce' => '减',
		'text_yuan' => '元',
		'text_cash' => '现金',
		'text_give' => '赠送',
		'text_gift' => '礼品',
		'text_link' => '链接',
		'link_explain' => '礼品链接必须为包含http://的完整url',
		'text_new_level' => '新增级别',
		'text_remark' => '备注',
		'text_not_join' => '未参加',

		'mansong_apply_verify_success_glog_desc' => '购买满即送活动%s个月，单价%s元，总共花费%s元',
);