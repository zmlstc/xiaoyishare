<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
  <div class="wtap-form-default">
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_sn'];?></label>
      </dt>
      <dd class="opt"><?php echo $output['info']['pdc_sn']; ?>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_membername'];?></label>
      </dt>
      <dd class="opt"><?php echo $output['info']['pdc_member_name']; ?>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_cash_price'];?></label>
      </dt>
      <dd class="opt"><?php echo $output['info']['pdc_amount']; ?>&nbsp;<?php echo $lang['currency_zh'];?>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_cash_shoukuanbank']; ?></label>
      </dt>
      <dd class="opt"><?php echo $output['info']['pdc_bank_name']; ?>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_cash_shoukuanaccount'];?></label>
      </dt>
      <dd class="opt"><?php echo $output['info']['pdc_bank_no']; ?>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_cash_shoukuanname']?></label>
      </dt>
      <dd class="opt"><?php echo $output['info']['pdc_bank_user']; ?>
        <p class="notic"></p>
      </dd>
    </dl>
   
    <?php if (intval($output['info']['pdc_payment_time'])) {?>
    <dl class="row">
      <dt class="tit">
        <label><?php echo $lang['admin_predeposit_paytime']; ?></label>
      </dt>
      <dd class="opt"><?php echo @date('Y-m-d',$output['info']['pdc_payment_time']); ?> ( <?php echo $lang['admin_predeposit_adminname'];?>: <?php echo $output['info']['pdc_payment_admin'];?> )
        <p class="notic"></p>
      </dd>
    </dl>
    <?php } ?>
    <?php if (!intval($output['info']['pdc_payment_state'])) {?>
    <div class="bot" id="submit-holder"> <a class="wtap-btn-big wtap-btn-green" href="javascript:if (confirm('您确认通过会员提现申请吗？')){window.location.href='<?php echo urlAdminShop('predeposit','pd_cash_pay',array('id'=>$output['info']['pdc_id']));?>';}else{}">通过提现申请</a></div>
  <?php } ?>
  </div>