<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('facility','facility_class');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>设施分类</h3>
        <h5>商家店铺设施分类管理</h5>
      </div><?php echo $output['top_link'];?> 
    </div>
  </div>
  <form id="facility_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="fc_id" value="<?php echo $output['class_array']['fc_id'];?>" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label class="fc_name"><em>*</em>分类名称</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['class_array']['fc_name'];?>" name="fc_name" id="fc_name" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="fc_sort"><?php echo $lang['wt_sort'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['class_array']['fc_sort'];?>" name="fc_sort" id="fc_sort" class="input-txt">
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
    if($("#facility_class_form").valid()){
     $("#facility_class_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#facility_class_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
            fc_name : {
                required : true,
                remote   : {
                url :'<?php echo urlAdminShop('facility','ajax_check_name');?>',
                type:'get',
                data:{
                    fc_name : function(){
                        return $('#fc_name').val();
                    },
                    fc_id : '<?php echo $output['class_array']['fc_id'];?>'
                  }
                }
            },
            fc_sort : {
                number   : true
            }
        },
        messages : {
            fc_name : {
                required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_no_null'];?>',
                remote   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_is_there'];?>'
            },
            fc_sort  : {
                number   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_sort_only_number'];?>'
            }
        }
    });
});
</script>