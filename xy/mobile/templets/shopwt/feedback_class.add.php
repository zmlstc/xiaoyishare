<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminMobile('mb_feedback','fbclass');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>意见反馈 - <?php echo $lang['wt_new'];?></h3>
        <h5>意见反馈类型管理</h5>
      </div>
    </div>
  </div>

  <form id="fb_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="ac_name"><em>*</em>分类名称</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="f_name" id="f_name" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#fb_class_form").valid()){
     $("#fb_class_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#fb_class_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
            f_name : {
                required : true
            }
        },
        messages : {
            f_name : {
                required : '<i class="fa fa-exclamation-bbs"></i>请输入分类名称'
            }
        }
    });
});
</script>