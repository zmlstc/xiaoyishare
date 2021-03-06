<?php
defined('ShopWT') or exit('Access Denied By ShopWT');
$lang	= array(
		'error'				=> '在处理您的请求时出现了问题:<br />',
		'homepage'			=> '首页',
		'wt_robbuy'			=> "抢购",
		'cur_location'		=> '当前位置',
		'miss_argument'		=> '缺少参数',
		'invalid_request'	=> '非法访问',
		'wrong_argument'	=> '参数错误',
		'wt_checkcode'		=> '验证码',
		'wrong_null'		=> '请填写验证码',
		'wrong_checkcode'	=> '验证码错误',
		'wrong_checkcode_change'	=> '点击更换验证码',
		'wrong_vercode'		=> '您的操作过度频繁，请输入验证码后继续。',
		'no_record'			=> '暂无符合条件的数据记录',
		'no_login'			=> '您还没有登录',
		'wt_status'			=> '状态',
		'wt_please_choose'	=> '请选择...',

		'currency'			=> '&yen;',
		'currency_zh'		=> '元',
		'ten_thousand'       => '万',
		'piece'				=> '件',
		'welcome_to_site'	=> '欢迎来到',
		'site_search_goods'	=> '商品',
		'site_search_store'	=> '店铺',
		'site_search_robbuy'	=> '抢购',
		'hot_search'			=> '热门搜索',
		'wt_colon'			=> '：',
		'wt_comma'           => '，',
		'wt_brand'			=> '品牌',
		'wt_pointprod'		=> '积分中心',
		'wt_special'		=> '专题',
		'wt_phone'			=> '服务热线：',
		'wt_wap'			=> '手机逛商城',
		'wt_mobile_client'	=> '手机扫一扫下载',
		'wt_service'		=> '客户服务',
		'wt_help'			=> '帮助中心',
		'wt_customer'		=> '售后服务',
		'wt_custom'			=> '客服中心',
		'wt_seller'			=> '商家管理',
		'wt_enter'			=> '招商入驻',
		'wt_account'	=> '商家登录',
		'wt_site_nav'		=> '网站导航',
		'wt_menber'			=> '我的商城',
		'wt_msg'			=> '站内消息',
		'wt_order'			=> '我的订单',
		'wt_consult'		=> '咨询回复',
		'wt_house'			=> '我的收藏',
		'wt_voucher'		=> '代金券',
		'wt_points'			=> '我的积分',
		'wt_browse'			=> '最近浏览的商品',
		'wt_history'		=> '全部浏览历史',
		'wt_invite'			=> '邀请返利',
		'wt_ensure_order'	=> '我的购物车',
		'wt_new_goods'		=> '最新加入的商品',
		'wt_bill_goods'		=> '去购物车结算',

		/**
		 * nav中的文字
		 */
		'wt_all_goods_class'	=> '全部商品分类',
		'wt_check_more_class' 	=> '查看更多分类',

		'wt_common_op_confirm' 	=> '您确定要删除该信息吗？',
		'wt_common_op_succ' 	=> '操作成功',
		'wt_common_op_fail' 	=> '操作失败',
		'wt_common_del_succ' 	=> '删除成功',
		'wt_common_del_fail' 	=> '删除失败',
		'wt_common_save_succ' 	=> '保存成功',
		'wt_common_save_fail' 	=> '保存失败',
		'wt_common_op_repeat' 	=> '您的操作过于频繁，请稍后再试',
		'wt_common_search'	 	=> '搜索',
		'wt_common_from'	 	=> '来自',

		'wt_common_rate_null'	 => '暂无评价',
		'wt_common_credit_null'	 => '暂无信用',
		'wt_common_goods_null'	 => '暂无商品',
		'wt_common_sell_null'	 => '暂无销量',
		'wt_common_result_null'	 => '暂无符合条件的数据记录',
		'wt_common_loading'	 	 => '加载中...',

		'wt_common_button_operate'	=> '操作',
		'wt_common_button_submit'	=> '提交',
		'wt_common_button_save'	 	=> '保存',
		'wt_common_button_confirm'	=> '确认',
		'wt_common_button_select'	=> '选择',
		'wt_common_button_upload'	=> '上传',
		'wt_common_shipping_free'	=> '（免运费）',
		'pointslogindesc'			=>	'会员登录',
		'pointsappdesc'				=>	'兑换到其它应用',
		'pointsregistdesc'			=>	'注册会员',
		'pointscommentsdesc'		=>	'评论商品',
		'pointsorderdesc'			=>	'购物消费',
		'pointsorderdesc_1' 		=>	'订单',
		'points_pointorderdesc'		=>	'消耗积分',
		'points_pointorderdesc_1' 	=>	'兑换礼品信息',
		'points_unit' 				=> '积分',
		'points_pointorderdesc_app' => 'UC应用的积分兑入',
		'credit_unit' 				=> '分',

		'wt_buying_goods'			=> '已买到的商品',
		'wt_mysns'					=> '个人主页',
		'wt_myfriends'				=> '我的好友',
		'wt_selled_goods'			=> '已售出的商品',
		'wt_selling_goods'			=> '销售中的商品',
		'wt_mystroe'					=> '我的店铺',
		'wt_favorites_goods'			=> '收藏的商品',
		'wt_favorites_stroe'			=> '收藏的店铺',
		'wt_cart_no_goods'			=> '您的购物车中暂无商品，赶快选择心爱的商品吧！',
		'wt_check_cart'				=> '查看购物车',
		'wt_accounts_goods'			=> '结算商品',
		'wt_delete'					=> '删除',
		'wt_goods_num_one'			=> '共',
		'wt_goods_num_two'			=> '种商品   金额总计',
		'wt_sign_multiply'			=> '×',
		//调试模式语言包
		'wt_debug_current_page'			=> '当前页面',
		'wt_debug_request_time'			=> '请求时间',
		'wt_debug_execution_time'		=> '页面执行时间',
		'wt_debug_memory_consumption'	=> '占用内存',
		'wt_debug_request_method'		=> '请求方法',
		'wt_debug_communication_protocol'=> '通信协议',
		'wt_debug_user_agent'			=> '用户代理',
		'wt_debug_session_id'			=> '会话ID',
		'wt_debug_logging'				=> '日志记录',
		'wt_debug_logging_1'				=> '条日志',
		'wt_debug_logging_2'				=> '无日志记录',
		'wt_debug_load_files'			=> '加载文件',
		'wt_debug_trace_title'			=> '页面Trace信息',

		'wt_notallowed_login'			=> '您的账号已经被管理员禁用，请联系平台客服进行核实',

		'wt_common_op_repeat' => '您的操作过于频繁，请稍后再试',

		'wt_snsshare' => '分享',
		'wt_month' => '月',
		'wt_day' => '日',

		//站外分享接口
		'wt_shareset_qqzone' 		=> 'QQ空间',
		'wt_shareset_qqweibo' 		=> '腾讯微博',
		'wt_shareset_sinaweibo' 	=> '新浪微博',
		'wt_shareset_renren' 		=> '人人网',

		//功能模块
		'wt_modules_what' 	=> '买什么',
		'wt_bbs'			=> '社区',
		/**
		 * 店铺地图
		 */
		'member_map_city'			=> '市',
		'member_map_store_name'		=> '店铺名称',
		'member_map_address'		=> '详细地址',
		'member_map_submit'			=> '确认保存',
		'member_map_success'		=> '保存成功',
		'member_map_loading'		=> '地图加载中...',
		'para_error' 				=> '参数错误',

		//订单
		'order_log_cancel' 		=> '取消订单',
		'order_log_new' 		=> '提交订单',
		'order_log_edit_ship' 	=> '修改运费',
		'order_log_pay' 		=> '完成付款',
		'order_log_send' 		=> '发货(编辑发货信息)',
		'order_log_success' 	=> '签收货物',
		'order_log_eval' 		=> '评价交易',
);