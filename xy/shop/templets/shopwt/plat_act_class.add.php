<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('plat_actclass');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>活动分类</h3>
        <h5>商城活动分类管理设置</h5>
      </div>
    </div>
  </div>
  <form id="act_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="gc_name"><em>*</em>分类名称</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="gc_name" id="gc_name" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
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
$(function(){$("#submitBtn").click(function(){
	
    if($("#act_class_form").valid()){
     $("#act_class_form").submit();
	}
	});
});

$(document).ready(function(){
	$('#act_class_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
            sc_name : {
                required : true,
                remote   : {                
                url :'<?php echo urlAdminShop('plat_actclass','ajax_check_name');?>',
                type:'get',
                data:{
                    gc_name : function(){
                        return $('#gc_name').val();
                    }
                  }
                }
            },
            gc_sort : {
                number   : true
            }
        },
        messages : {
            gc_name : {
                required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_no_null'];?>',
                remote   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_is_there'];?>'
            },
            gc_sort  : {
                number   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_sort_only_number'];?>'
            }
        }
    });
});

</script>