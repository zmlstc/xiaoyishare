<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>推广佣金设置</h3>
        <h5>佣金相关比例设置</h5>
      </div>
    </div>
  </div>
  <form method="post" name="settingForm" id="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      
	  <dl class="row">
        <dt class="tit">平台分佣比例</dt>
        <dd class="opt">
          <input id="commis_rate_system" name="commis_rate_system" value="<?php echo $output['list_setting']['commis_rate_system'];?>" class="txt" type="text" style="width:60px;">%
          <p class="notic"></p>
        </dd>
      </dl>
	        <dl class="row">
        <dt class="tit">邀请商家分佣比例</dt>
        <dd class="opt">
          <input id="commis_rate_invstore" name="commis_rate_invstore" value="<?php echo $output['list_setting']['commis_rate_invstore'];?>" class="txt" type="text" style="width:60px;">%
          <p class="notic"></p>
        </dd>
      </dl>
	        <dl class="row">
        <dt class="tit">邀请会员分佣比例</dt>
        <dd class="opt">
          <input id="commis_rate_invmember" name="commis_rate_invmember" value="<?php echo $output['list_setting']['commis_rate_invmember'];?>" class="txt" type="text" style="width:60px;">%
          <p class="notic"></p>
        </dd>
      </dl>
	    <dl class="row">
        <dt class="tit">消费会员分佣比例</dt>
        <dd class="opt">
          <input id="commis_rate_invpoints" name="commis_rate_invpoints" value="<?php echo $output['list_setting']['commis_rate_invpoints'];?>" class="txt" type="text" style="width:60px;">%
          <p class="notic">消费会员获得的分佣将以积分形式发放</p>
        </dd>
      </dl>
	 
	  
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script>

$(function(){
    $("#submitBtn").click(function(){
        if($("#settingForm").valid()){
            $("#settingForm").submit();
        }
    });
});
</script> 
