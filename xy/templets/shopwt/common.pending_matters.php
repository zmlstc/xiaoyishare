<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<ul class="cp-toast-list">
  <?php if ($output['statistics']['cashlist'] > 0) {?>
  <li>
    <span>[商城-会员]</span>
    <a target="workspace" href="<?php echo urlAdminshop('predeposit','pd_cash_list');?>" onclick="openItem('shop|predeposit')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['cashlist'];?></strong>条预存款提现申请需要处理。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['store_joinin'] > 0) {?>
  <li>
    <span>[商城-店铺]</span>
    <a target="workspace" href="<?php echo urlAdminshop('store', 'store_joinin');?>" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_joinin'];?></strong>条开店申请需要处理。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['store_reopen_applay'] > 0) {?>
  <li>
    <span>[商城-店铺]</span>
    <a target="workspace" href="<?php echo urlAdminshop('store', 'reopen_list');?>" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_reopen_applay'];?></strong>条开店续签申请需要处理。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['store_expire']) {?>
  <li>
    <span>[商城-店铺]</span>
    <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_expire'];?></strong>家店铺即将到期。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['store_expired']) {?>
  <li>
    <span>[商城-店铺]</span>
    <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_expired'];?></strong>家店铺已经到期。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['inform']) {?>
  <li>
    <span>[商城-交易]</span>
    <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|inform')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['inform'];?></strong>条举报需要处理。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['complain_new']) {?>
  <li>
    <span>[商城-交易]</span>
    <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|complain')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['complain_new'];?></strong>条投诉需要处理。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['complain_handle']) {?>
  <li>
    <span>[商城-交易]</span>
    <a target="workspace" href="<?php echo urlAdminshop('complain', 'complain_handle_list')?>" onclick="openItem('shop|complain')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['complain_handle'];?></strong>条投诉等待仲裁。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['points_order']) {?>
  <li>
    <span>[商城-交易]</span>
    <a target="workspace" href="<?php echo urlAdminshop('pointprod', 'pointorder_list');?>" onclick="openItem('shop|pointprod')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['points_order'];?></strong>个积分订单需要发货。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['check_billno']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminshop('bill', 'show_statis');?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['check_billno'];?></strong>个实物账单等待审核。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['check_vr_billno']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminshop('bill', 'show_statis')?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['check_vr_billno'];?></strong>个虚拟订单等待审核。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['pay_billno']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminshop('bill', 'show_statis')?>" onclick="openItem('shop|bill')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['pay_billno'];?></strong>个实物账单等待支付。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['mall_consult']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="javascript:void(0);" onclick="openItem('shop|mall_consult')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['mall_consult'];?></strong>个客户提问需要回复。</a>
  </li>
  <?php }?>
  
  
  <?php if ($output['statistics']['realverifyNum']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminshop('member', 'realverify');?>" onclick="openItem('shop|member')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['realverifyNum'];?></strong>个实名认证申请需要处理。</a>
  </li>
  <?php }?>
  <?php if ($output['statistics']['store_modifyNum']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminshop('store', 'sname_modify')?>" onclick="openItem('shop|store')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['store_modifyNum'];?></strong>个店名更改申请需要处理。</a>
  </li>
  <?php }?>
 <?php if ($output['statistics']['agent_joinNum']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminshop('agencies', 'agent_joinin')?>" onclick="openItem('shop|agencies')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['agent_joinNum'];?></strong>个代理申请需要处理。</a>
  </li>
  <?php }?>
  
 <?php if ($output['statistics']['MbFeedback_Num']) {?>
  <li>
    <span>[商城-运营]</span>
    <a target="workspace" href="<?php echo urlAdminMobile('mb_feedback', 'flist')?>" onclick="openItem('mobile|mb_feedback')"><i class="fa fa-bell-o"></i>有<strong><?php echo $output['statistics']['MbFeedback_Num'];?></strong>个个反馈信息需要处理。</a>
  </li>
  <?php }?>

</ul>
