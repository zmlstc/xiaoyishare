<?php defined('ShopWT') or exit('Access Denied By ShopWT');
return array(
    '' =>
        array(
            'mmt_code' => 'inviter_store_sale',
            'mmt_name' => '邀请的商家销售',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您邀请的商家“某某店铺”在平台成功销售，您的返佣已转入余额，请查看。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'arrival_notice' =>
        array(
            'mmt_code' => 'arrival_notice',
            'mmt_name' => '到货通知提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您关注的商品 “{$goods_name}” 已经到货。<a href="{$goods_url}" target="_blank">点击查看商品</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您关注的商品 “{$goods_name}” 已经到货。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您关注的商品  “{$goods_name}” 已经到货。',
            'mmt_mail_content' => '<p>
   {$site_name}提醒：
</p>
<p>
  您关注的商品 “{$goods_name}” 已经到货。
</p>
<p>
 <a href="{$goods_url}" target="_blank">点击查看商品</a> 
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'consult_goods_reply' =>
        array(
            'mmt_code' => 'consult_goods_reply',
            'mmt_name' => '商品咨询回复提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您关于商品 “{$goods_name}”的咨询，商家已经回复。<a href="{$consult_url}" target="_blank">点击查看回复</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您关于商品 “{$goods_name}” 的咨询，商家已经回复。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您关于商品 “{$goods_name}”的咨询，商家已经回复。',
            'mmt_mail_content' => '<p>
  {$site_name}提醒：
</p>
<p>
  您关注的商品“{$goods_name}” 已经到货。
</p>
<p>
  <a href="{$consult_url}" target="_blank">点击查看回复</a> 
</p>
<p>
 <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>
<br />
<div class="firebugResetStyles firebugBlockBackgroundColor">
 <div style="background-color:transparent ! important;" class="firebugResetStyles">
  </div>
</div>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'consult_mall_reply' =>
        array(
            'mmt_code' => 'consult_mall_reply',
            'mmt_name' => '平台客服回复提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的平台客服咨询已经回复。<a href="{$consult_url}" target="_blank">点击查看回复</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您的平台客服咨询已经回复。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您的平台客服咨询已经回复。',
            'mmt_mail_content' => '<p>
 {$site_name}提醒：
</p>
<p>
  您的平台客服咨询已经回复。
</p>
<p>
    <a href="{$consult_url}" target="_blank">点击查看回复</a> 
</p>
<p>
 <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'inviter_member_consume' =>
        array(
            'mmt_code' => 'inviter_member_consume',
            'mmt_name' => '邀请的会员消费',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您邀请的会员“{$nickname}”在平台消费并支付成功，您的返佣已转入余额，请查看。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'inviter_member_reg' =>
        array(
            'mmt_code' => 'inviter_member_reg',
            'mmt_name' => '邀请注册会员',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您已成功邀请一位注册会员，可通过“我的邀请”进行查看。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'inviter_store_reg' =>
        array(
            'mmt_code' => 'inviter_store_reg',
            'mmt_name' => '邀请店铺入驻',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您已成功邀请一家店铺入驻，可通过“我的邀请”进行查看。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'member_reg_succ' =>
        array(
            'mmt_code' => 'member_reg_succ',
            'mmt_name' => '注册小易共享',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '恭喜你成功注册小易共享，您可以通过平台向入驻商家进行在线支付。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'order_book_end_pay' =>
        array(
            'mmt_code' => 'order_book_end_pay',
            'mmt_name' => '预定订单尾款支付提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的订单已经进入支付尾款时间。<a href="{$order_url}" target="_blank">点击支付尾款</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您的订单已经进入支付尾款时间。订单编号 {$order_sn}。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您的订单已经进入支付尾款时间。订单编号 {$order_sn}。',
            'mmt_mail_content' => '<p>
    {$site_name}提醒：
</p>
<p>
  您的订单已经进入支付尾款时间。订单编号 {$order_sn}。<br />
<a href="{$order_url}" target="_blank">点击支付尾款</a>
</p>
<p>
    <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>
<br />',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'order_deliver_success' =>
        array(
            'mmt_code' => 'order_deliver_success',
            'mmt_name' => '商品出库提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的订单已经出库。<a href="{$order_url}" target="_blank">点击查看订单</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您的订单已经出库。订单编号 {$order_sn}。',
            'mmt_mail_switch' => '1',
            'mmt_mail_subject' => '{$site_name}提醒：您的订单已经出库。订单编号 {$order_sn}。',
            'mmt_mail_content' => '<p>
	{$site_name}提醒：
</p>
<p>
	您的订单已经出库。订单编号 {$order_sn}。<br />
<a href="{$order_url}" target="_blank">点击查看订单</a> 
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p style="text-align:right;">
	{$site_name}
</p>
<p style="text-align:right;">
	{$mail_send_time}
</p>
<br />',
            'apicodeid' => '',
            'mp_msg_id' => 'VX-JlhB03pIEfAVHTJ-U2Kab2MZuOyhLUZB35F-hTVo',
            'mmt_wx_switch' => '1',
        ),
    'order_payment_success' =>
        array(
            'mmt_code' => 'order_payment_success',
            'mmt_name' => '付款成功提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您到{$store_name}店铺消费{$money}元已成功付款，感谢您使用小易共享。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '订单{$order_sn}已完成支付，感谢您使用小易共享。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：{$order_sn}的款项已经收到，请留意出库通知。',
            'mmt_mail_content' => '<p>
 {$site_name}提醒：
</p>
<p>
  {$order_sn}的款项已经收到，请留意出库通知。
</p>
<p>
  <a href="{$order_url}" target="_blank">点击查看订单详情</a>
</p>
<p>
  <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>
<br />',
            'apicodeid' => '',
            'mp_msg_id' => 'J4jvkVZPINOlaAeOosqr3q_fHXcamf7G4YCTEYUs2Oc',
            'mmt_wx_switch' => '1',
        ),
    'predeposit_change' =>
        array(
            'mmt_code' => 'predeposit_change',
            'mmt_name' => '余额变动提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '{$time}您的账户资金有变化，描述：{$desc}，金额变化{$av_amount}元，冻结金额变化：{$freeze_amount}元。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '你的账户于 {$time} 账户资金有变化，描述：{$desc}，可用金额变化： {$av_amount}元，冻结金额变化：{$freeze_amount}元。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：你的账户于 {$time} 账户资金有变化，描述：{$desc}，可用金额变化： {$av_amount}元，冻结金额变化：{$freeze_amount}元。',
            'mmt_mail_content' => '<p>
    {$site_name}提醒：
</p>
<p>
  你的账户于 {$time} 账户资金有变化，描述：{$desc}，可用金额变化：{$av_amount}元，冻结金额变化：{$freeze_amount}元。
</p>
<p>
  <a href="{$pd_url}" target="_blank">点击查看余额</a> 
</p>
<p>
  <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'realname_add_info' =>
        array(
            'mmt_code' => 'realname_add_info',
            'mmt_name' => '提交实名认证',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '你已成功提交实名认证申请，请耐心等待系统审核。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'realname_chekc_error' =>
        array(
            'mmt_code' => 'realname_chekc_error',
            'mmt_name' => '实名认证失败',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的实名认证未通过审核，请重新进行申请。',
            'mmt_short_switch' => '1',
            'mmt_short_content' => '很遗憾！您的实名认证未通过审核，请重新进行申请。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'realname_chekc_success' =>
        array(
            'mmt_code' => 'realname_chekc_success',
            'mmt_name' => ' 实名认证通过',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的实名认证已审核通过，现在可以使用平台的推广分享功能。',
            'mmt_short_switch' => '1',
            'mmt_short_content' => '恭喜您！您提交的实名认证已审核通过，现在可以使用平台的推广分享功能。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '',
            'mmt_mail_content' => '',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'recharge_card_balance_change' =>
        array(
            'mmt_code' => 'recharge_card_balance_change',
            'mmt_name' => '充值卡余额变动提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '你的账户余额于 {$time}有变化【{$description}】，可用余额变化 ：{$available_amount}元，冻结余额变化：{$freeze_amount}元。请到“我的钱包”进行查看。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '你的账户于 {$time} 充值卡余额有变化，描述：{$description}，可用充值卡余额变化： {$available_amount}元，冻结充值卡余额变化：{$freeze_amount}元。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：你的账户于 {$time} 充值卡余额有变化，描述：{$description}，可用充值卡余额变化： {$available_amount}元，冻结充值卡余额变化：{$freeze_amount}元。',
            'mmt_mail_content' => '<p>
    {$site_name}提醒：
</p>
<p>
  你的账户于 {$time} 充值卡余额有变化，描述：{$description}，可用充值卡余额变化：{$available_amount}元，冻结充值卡余额变化：{$freeze_amount}元。
</p>
<p>
  <a href="{$url}" target="_blank">点击查看余额</a> 
</p>
<p>
  <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'refund_return_notice' =>
        array(
            'mmt_code' => 'refund_return_notice',
            'mmt_name' => '退款退货提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的退款退货单有了变化。<a href="{$refund_url}" target="_blank">点击查看</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您的退款退货单有了变化。退款退货单编号：{$refund_sn}。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您的退款退货单有了变化。',
            'mmt_mail_content' => '<p>
  {$site_name}提醒：
</p>
<p>
  您的退款退货单有了变化。退款退货单编号：{$refund_sn}。
</p>
<p>
    &lt;a href="{$refund_url}" target="_blank"&gt;点击查看&lt;/a&gt;
</p>
<p>
 <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>
<br />',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'rpt_use' =>
        array(
            'mmt_code' => 'rpt_use',
            'mmt_name' => '优惠券使用提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您的优惠券已经使用，编号：{$coupon_code}。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您有优惠券已经使用，编号：{$coupon_code}。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您有优惠券已经使用，编号：{$coupon_code}。',
            'mmt_mail_content' => '<p>
  {$site_name}提醒：
</p>
<p>
  您有优惠券已经使用，编号：{$coupon_code}。
</p>
<p>
 &lt;a href="{$coupon_url}" target="_blank"&gt;点击查看&lt;/a&gt;
</p>
<p>
    <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>
<p>
    <br />
</p>
<p>
   <br />
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'trad_change' =>
        array(
            'mmt_code' => 'trad_change',
            'mmt_name' => '佣金变动提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '你的账户于 {$time} 账户资金有变化，描述：{$desc}，可用金额变化 ：{$av_amount}元，冻结金额变化：{$freeze_amount}元。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '你的账户于 {$time} 账户资金有变化，描述：{$desc}，可用金额变化： {$av_amount}元，冻结金额变化：{$freeze_amount}元。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：你的账户于 {$time} 账户佣金有变化，描述：{$desc}，可提现佣金金额变化 ：{$av_amount}元，冻结佣金金额变化：{$freeze_amount}元。',
            'mmt_mail_content' => '<p>
	{$site_name}提醒：
</p>
<p>
	你的账户于 {$time} 账户佣金有变化，描述：{$desc}，可提现佣金金额变化 ：{$av_amount}元，冻结佣金金额变化：{$freeze_amount}元。
</p>
<p>
	<a href="{$trad_url}" target="_blank">点击查看详情</a> 
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p style="text-align:right;">
	{$site_name}
</p>
<p style="text-align:right;">
	{$mail_send_time}
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'voucher_use' =>
        array(
            'mmt_code' => 'voucher_use',
            'mmt_name' => '代金券使用提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您有代金券已经使用，代金券编号：{$voucher_code}。<a href="{$voucher_url}" target="_blank">点击查看</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您有代金券已经使用，代金券编号：{$voucher_code}。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您有代金券已经使用，代金券编号：{$voucher_code}。',
            'mmt_mail_content' => '<p>
  {$site_name}提醒：
</p>
<p>
  您有代金券已经使用，代金券编号：{$voucher_code}。
</p>
<p>
 &lt;a href="{$voucher_url}" target="_blank"&gt;点击查看&lt;/a&gt;
</p>
<p>
    <br />
</p>
<p>
   <br />
</p>
<p>
   <br />
</p>
<p style="text-align:right;">
 {$site_name}
</p>
<p style="text-align:right;">
   {$mail_send_time}
</p>
<p>
    <br />
</p>
<p>
   <br />
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'voucher_will_expire' =>
        array(
            'mmt_code' => 'voucher_will_expire',
            'mmt_name' => '代金券即将到期提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您有一个代金券即将在{$indate}过期，请记得使用。<a href="{$voucher_url}" target="_blank">点击查看</a>',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您有一个代金券即将在{$indate}过期，请记得使用。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您有一个代金券即将在{$indate}过期，请记得使用。',
            'mmt_mail_content' => '<p>
	{$site_name}提醒：
</p>
<p>
	您有一个代金券即将在{$indate}过期，请记得使用。
</p>
<p>
	<a href="{$voucher_url}" target="_blank">点击查看</a> 
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p style="text-align:right;">
	{$site_name}
</p>
<p style="text-align:right;">
	{$mail_send_time}
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
    'vr_code_will_expire' =>
        array(
            'mmt_code' => 'vr_code_will_expire',
            'mmt_name' => '兑换码即将到期提醒',
            'mmt_message_switch' => '1',
            'mmt_message_content' => '您有一组兑换码即将在{$indate}过期，请记得使用。',
            'mmt_short_switch' => '0',
            'mmt_short_content' => '您有一组兑换码即将在{$indate}过期，请记得使用。',
            'mmt_mail_switch' => '0',
            'mmt_mail_subject' => '{$site_name}提醒：您有一组兑换码即将在{$indate}过期，请记得使用。',
            'mmt_mail_content' => '<p>
	{$site_name}提醒：
</p>
<p>
	您有一组兑换码即将在{$indate}过期，请记得使用。
</p>
<p>
	&lt;a href="{$order_vr_url}" target="_blank"&gt;点击查看&lt;/a&gt;
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>
<p style="text-align:right;">
	{$site_name}
</p>
<p style="text-align:right;">
	{$mail_send_time}
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>',
            'apicodeid' => '',
            'mp_msg_id' => NULL,
            'mmt_wx_switch' => '1',
        ),
);