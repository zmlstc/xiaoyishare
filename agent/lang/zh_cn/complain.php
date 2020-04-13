<?php
defined('ShopWT') or exit('Access Denied By ShopWT');
$lang	= array(

/**
 * 导航菜单
 */
'complain_new_list' => '新投诉',
'complain_handle_list' => '待仲裁',
'complain_appeal_list' => '待申诉',
'complain_talk_list' => '对话中',
'complain_finish_list' => '已关闭',
'complain_subject_list' => '投诉主题',

/**
 * 导航菜单
 */
'complain_manage_title' => '投诉管理',
'complain_manage_subhead' => '商城对商品交易投诉管理及仲裁',
'complain_submit' => '投诉处理',
'complain_setting' => '投诉设置',

'complain_state_new' => '新投诉',
'complain_state_handle' => '待仲裁',
'complain_state_appeal' => '待申诉',
'complain_state_talk' => '对话中',
'complain_state_finish' => '已关闭',
'complain_subject_list' => '投诉主题',

'complain_pic' => '图片',
'complain_pic_view' => '查看图片',
'complain_pic_none' => '暂无图片',
'complain_detail' => '投诉详情',
'complain_message' => '投诉信息',
'complain_evidence' => '投诉证据',
'complain_evidence_upload' => '上传投诉证据',
'complain_content' => '投诉内容',
'complain_accuser' => '投诉人',
'complain_accused' => '被投诉店铺',
'complain_admin' => '管理员',
'complain_unknow' => '未知',
'complain_datetime' => '投诉时间',
'complain_goods' => '投诉的商品',
'complain_goods_name' => '商品名称',
'complain_state' => '投诉状态',
'complain_progress' => '投诉进程',
'complain_handle' => '投诉处理',
'complain_subject_content' => '投诉主题',
'complain_subject_select' => '选择投诉主题',
'complain_subject_desc' => '投诉主题描述',
'complain_subject_add' => '添加主题',
'complain_appeal_detail' => '申诉详情',
'complain_appeal_message' => '申诉信息',
'complain_appeal_content' => '申诉内容',
'complain_appeal_datetime' => '申诉时间',
'complain_appeal_evidence' => '申诉证据',
'complain_appeal_evidence_upload' => '上传申诉证据',
'complain_state_inprogress' => '进行中',
'complain_state_finish' => '已完成',
'final_handle_detail' => '处理详情',
'final_handle_message' => '处理结果',
'final_handle_datetime' => '处理时间',
'order_detail' => '订单详情',
'order_message' => '订单信息',
'order_state' => '订单状态',
'order_sn' => '订单号',
'order_datetime' => '下单时间',
'order_price' => '订单总额',
'order_discount' => '优惠打折',
'order_voucher_price' => '使用的代金券面额',
'order_voucher_sn' => '代金券编码',
'order_buyer_message' => '买家信息',
'order_seller_message' => '店铺信息',
'order_shop_name' => '店铺名称',
'order_buyer_name' => '买家名称',
'order_state_cancel' => '已取消',
'order_state_unpay' => '未付款',
'order_state_payed' => '已付款',
'order_state_send' => '已发货',
'order_state_receive' => '已收货',
'order_state_commit' => '已提交',
'order_state_verify' => '已确认',
'complain_time_limit' => '投诉时效',
'complain_time_limit_desc' => '单位为天，订单完成后开始计算，多少天内可以发起投诉',

'refund_message'	=> '退款信息',
'refund_order_refund'	=> '已确认退款金额',

/**
 * 提示信息
 */
'confirm_delete' => '您确定要删除吗?',
'complain_content_error' => '投诉内容不能为空且必须小于100个字符',
'appeal_message_error' => '投诉内容不能为空且必须小于100个字符',
'complain_pic_error' => '图片必须是jpg格式',
'complain_time_limit_error' => '投诉时效不能为空且必须为数字',
'complain_subject_content_error' => '投诉主题不能为空且必须小于50个字符',
'complain_subject_desc_error' => '投诉主题描述不能为空且必须小于100个字符',
'complain_subject_type_error' => '未知投诉主题类型',
'complain_subject_add_success' => '投诉主题添加成功',
'complain_subject_add_fail' => '投诉主题添加失败',
'complain_subject_delete_success' => '投诉主题删除成功',
'complain_subject_delete_fail' => '投诉主题删除失败',
'complain_setting_save_success' => '投诉设置保存成功',
'complain_setting_save_fail' => '投诉设置保存失败',
'complain_goods_select' => '选择要投诉的商品',
'complain_submit_success' => '投诉提交成功',
'complain_close_confirm' => '确认关闭此投诉?',
'appeal_submit_success' => '申诉提交成功',
'talk_detail' => '对话详情',
'talk_null' => '对话不能为空',
'talk_none' => '目前没有对话',
'talk_list' => '对话记录',
'talk_send' => '发布对话',
'talk_refresh' => '刷新对话',
'talk_send_success' => '对话发送成功',
'talk_send_fail' => '对话发送失败',
'talk_forbit_success' => '对话屏蔽成功',
'talk_forbit_fail' => '对话屏蔽失败',
'complain_verify_success' => '投诉审核成功',
'complain_verify_fail' => '投诉审核失败',
'complain_close_success' => '投诉关闭成功',
'complain_close_fail' => '投诉关闭失败',
'talk_forbit_message' => '<该对话被管理员屏蔽>',
'final_handle_message_error' => '处理意见不能为空且必须小于255个字符',
'final_handle_message' => '处理意见',
'handle_submit' => '提交仲裁',
'complain_repeat' => '您已经投诉了该订单请等待处理',
'verify_submit_message' => '确认审核此投诉',


/**
 * 文本
 */
'complain_text_select' => '请选择...',
'complain_text_handle' => '操作',
'complain_text_detail' => '详细',
'complain_text_submit' => '提交',
'complain_text_pic' => '图片',
'complain_text_num' => '数量',
'complain_text_price' => '价格',
'complain_text_problem' => '问题描述',
'complain_text_say' => '说',
'complain_text_verify' => '审核',
'complain_text_close' => '关闭投诉',
'complain_text_forbit' => '屏蔽',
'complain_help1' => '在投诉时效内，买家可对订单进行投诉，投诉主题由管理员在后台统一设置',
'complain_help2' => '投诉时效可在系统设置处进行设置',
'complain_help3' => '点击详细，可进行投诉审核。审核完成后，被投诉店铺可进行申诉。申诉成功后，投诉双方进行对话，最后由后台管理员进行仲裁操作',
);