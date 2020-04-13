<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('plat_act','act_store',array('act_id'=>$output['actid']));?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>活动管理</h3>
        <h5>商城活动页面管理</h5>
      </div>
    </div>
  </div>
  <form id="act_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
	<input type="hidden" name="act_id" id="act_id" value='<?php echo $output['actid'];?>'/>
    <div class="wtap-form-default">
	    <dl class="row">
        <dt class="tit">
          <label><em>*</em>店铺ID</label>
        </dt>
        <dd class="opt">
          <input type="text" name="store_id" id="store_id" class="input-txt" onchange="javascript:checkstore();">
          <input type="hidden" name="sid" id="sid" value='0'/>
          <span class="err"></span>
          <p class="notic">输入店铺id</p>
        </dd>
      </dl>
      <dl class="row" id="tr_storeinfo">
        <dt class="tit">符合条件的店铺</dt>
        <dd class="opt" id="td_storeinfo"></dd>
      </dl>
	
      <dl class="row">
        <dt class="tit">
          <label for="gc_sort"><?php echo $lang['wt_sort'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" value="255" name="gc_sort" id="gc_sort" class="input-txt">
          <span class="err"></span>
          <p class="notic"><?php echo $lang['update_sort'];?></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){	
	$("#tr_storeinfo").hide();
	$("#submitBtn").click(function(){
		
		if($("#act_class_form").valid()){
		 $("#act_class_form").submit();
		}
	});
});
function checkstore(){
	var store_id = $.trim($("#store_id").val());
	if(store_id == ''){
		$("#sid").val('0');
		alert('店铺ID不能为空');
		return false;
	}
	$.getJSON("<?php echo urlAdminShop('plat_act','checkstore');?>", {'store_id':store_id}, function(data){
	        if (data)
	        {
		        $("#tr_storeinfo").show();
				var msg= " "+ data.name ;
				$("#store_id").val(data.id);
				$("#sid").val(data.id);
		        $("#td_storeinfo").text(msg);
	        }
	        else
	        {
	        	$("#store_id").val('');
	        	$("#sid").val('0');
		        alert("店铺id错误");
	        }
	});
}
$(document).ready(function(){
	$('#act_class_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
            store_id : {
                required : true
            },
            gc_sort : {
                number   : true
            }
        },
        messages : {
            store_id : {
                required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_no_null'];?>'
            },
            gc_sort  : {
                number   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_sort_only_number'];?>'
            }
        }
    });
});

</script>