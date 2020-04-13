<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAgentAgents('store_class');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['store_class'];?></h3>
        <h5><?php echo $lang['store_class_subhead'];?></h5>
      </div>
    </div>
  </div>
  <form id="store_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="sc_id" value="<?php echo $output['pclass']['sc_id'];?>" />
    <div class="wtap-form-default">
	 <dl class="row">
        <dt class="tit">
          <label for="sc_name">分类名称</label>
        </dt>
        <dd class="opt">
          <?php echo $output['pclass']['sc_name'];?>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
	  <dl class="row">
        <dt class="tit">
          <label for="sc_name">平台最低佣金</label>
        </dt>
        <dd class="opt">
          <?php echo $output['pclass']['commis_rate'];?>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
	  
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>佣金</label>
        </dt>
        <dd class="opt">
          <input id="commis_rate" class="w60" type="text" value="<?php echo $output['aclass'];?>" name="commis_rate">
          <i>%</i> <span class="err"></span>
          <p class="notic mb10">佣金必须大于等于平台最低佣金；比例必须为0-100的数字，支持小数点后2位。</p>
          
        </dd>
      </dl>
	  
	
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#store_class_form").valid()){
     $("#store_class_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#store_class_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
            commis_rate : {
                required : true
            }
        },
        messages : {
            commis_rate : {
                required : '<i class="fa fa-exclamation-bbs"></i>佣金不能为空'
            }
        }
    });
});
</script>