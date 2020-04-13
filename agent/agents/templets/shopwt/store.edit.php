<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<style type="text/css">
.d_inline {
	display: inline;
}
</style>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAgentAgents('store','store');?>" title="返回<?php echo $lang['manage'];?>列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['wt_store_manage'];?> - 编辑店铺信息</h3>
        <h5><?php echo $lang['wt_store_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <div class="homepage-focus" wttype="editStoreContent">
  <div class="title">
  <h3>编辑店铺信息</h3>
    <ul class="tab-base wt-row">
      <li><a class="current" href="javascript:void(0);">店铺信息</a></li>
    
    </ul>
    </div>
    <form id="store_form" method="post">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="store_id" value="<?php echo $output['store_array']['store_id'];?>" />
      <div class="wtap-form-default">
       
        <dl class="row">
          <dt class="tit">
            <label for="store_name"><em>*</em>店铺名称</label>
          </dt>
          <dd class="opt">
            <?php echo $output['store_array']['store_name'];?>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
          <label for="store_company_name">公司名称</label>
        </dt>
        <dd class="opt">
         <?php echo $output['store_array']['store_company_name'];?>
          <span class="err"></span>
        </dd>
      </dl>

        <dl class="row">
          <dt class="tit">
            <label for="state"><?php echo $lang['state'];?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="store_state1" class="cb-enable <?php if($output['store_array']['store_state'] == '1'){ ?>selected<?php } ?>" ><?php echo $lang['open'];?></label>
              <label for="store_state0" class="cb-disable <?php if($output['store_array']['store_state'] == '0'){ ?>selected<?php } ?>" ><?php echo $lang['close'];?></label>
              <input id="store_state1" name="store_state" <?php if($output['store_array']['store_state'] == '1'){ ?>checked="checked"<?php } ?> onclick="$('#tr_store_close_info').hide();" value="1" type="radio">
              <input id="store_state0" name="store_state" <?php if($output['store_array']['store_state'] == '0'){ ?>checked="checked"<?php } ?> onclick="$('#tr_store_close_info').show();" value="0" type="radio">
            </div>
            <span class="err"></span>
            <p class="notic"></p>
          </dd>
        </dl>
     
        <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
      </div>
    </form>

  </div>
</div>


<script type="text/javascript">
var BASE_SITE_URL = '<?php echo BASE_SITE_URL;?>';
$(function(){
   
    $('input[name=store_state][value=<?php echo $output['store_array']['store_state'];?>]').trigger('click');

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
       $("#store_form").submit();
    });


});
</script>