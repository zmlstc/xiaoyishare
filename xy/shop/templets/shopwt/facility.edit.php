<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('facility');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>设施管理</h3>
        <h5>商家店铺设施管理</h5>
      </div><?php echo $output['top_link'];?> 
    </div>
  </div>
  <form id="facility_form" method="post"  enctype="multipart/form-data" >
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="f_id" value="<?php echo $output['class_array']['f_id'];?>" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label class="f_name"><em>*</em>名称</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['class_array']['f_name'];?>" name="f_name" id="f_name" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
	  <dl class="row">
        <dt class="tit">
          <label for=""><em>*</em>图标</label>
        </dt>
        <dd class="opt">
		 <div class='input-file-show'><span class="show"><?php if(!empty($output['class_array']['f_img'])){?> <a class="nyroModal" rel="gal" href="<?php echo UPLOAD_SITE_URL.DS.ATTACH_MOBILE.'/facility/'.$output['class_array']['f_img'];?>"> <i class="fa fa-picture-o" onMouseOver="toolTip('<img src=<?php echo UPLOAD_SITE_URL.DS.ATTACH_MOBILE.'/facility/'.$output['class_array']['f_img'];?>>')" onMouseOut="toolTip()"></i></a><?php } ?> </span><span class='type-file-box'>
            <input type='file' class='type-file-file' id='change_f_img' name='f_img' size='30' hidefocus='true'  wt_type='change_f_img' title='点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效'>
            </span></div>
         
          <span class="err"></span>
          <p class="notic">展示图片，建议大小一级分类120x120像素的PNG图片。</p>
        </dd>
      </dl>
	   <dl class="row">
        <dt class="tit">
          <label for="f_name"><em>*</em>分类</label>
        </dt>
        <dd class="opt">
          <div id="gcategory">
            <select class="class-select" name="fc_id" id="fc_id">
              <option value="0"><?php echo $lang['wt_please_choose'];?></option>
              <?php if(!empty($output['fc_list'])){ ?>
              <?php foreach($output['fc_list'] as $k => $v){ ?>
             
              <option value="<?php echo $v['fc_id'];?>" <?php if($output['class_array']['fc_id'] == $v['fc_id']){?>selected<?php }?>><?php echo $v['fc_name'];?></option>
              
              <?php } ?>
              <?php } ?>
            </select>
            </div>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="f_sort"><?php echo $lang['wt_sort'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['class_array']['f_sort'];?>" name="f_sort" id="f_sort" class="input-txt">
          <span class="err"></span>
          <p class="notic"><?php echo $lang['update_sort'];?></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
$(function(){
	var textButton="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='选择上传...' class='type-file-button' />";
    $(textButton).insertBefore("#change_f_img");
    $("#change_f_img").change(function(){
	$("#textfield1").val($("#change_f_img").val());
    });
});	
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#facility_form").valid()){
     $("#facility_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#facility_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
            f_name : {
                required : true,
                remote   : {
                url :'<?php echo urlAdminShop('facility','ajax_cf_name');?>',
                type:'get',
                data:{
                    f_name : function(){
                        return $('#f_name').val();
                    },
                    f_id : '<?php echo $output['class_array']['f_id'];?>'
                  }
                }
            },
            fc_id : {
                number   : true,
				min:1
            },
            f_sort : {
                number   : true
            }
        },
        messages : {
            f_name : {
                required : '<i class="fa fa-exclamation-bbs"></i>名称不能为空',
                remote   : '<i class="fa fa-exclamation-bbs"></i>名称已经存在'
            },
            fc_id : {
                number   : '<i class="fa fa-exclamation-bbs"></i>请选择分类',
				min: '<i class="fa fa-exclamation-bbs"></i>请选择分类！'
            },
            f_sort  : {
                number   : '<i class="fa fa-exclamation-bbs"></i>序号必须为数字'
            }
		}
    });
});
</script>