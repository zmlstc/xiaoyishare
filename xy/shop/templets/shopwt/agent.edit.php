<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('agencies','agencies');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>代理管理 - 编辑代理</h3>
        <h5>代理商信息设置管理</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
    </div>
  </div>
  
  <form id="user_form" enctype="multipart/form-data" method="post">
    <input type="hidden" name="form_submit" value="ok" />
	<input type="hidden" name="agent_id" value="<?php echo $output['agencies_array']['agent_id'];?>" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="agent_name">登录账户</label>
        </dt>
        <dd class="opt">
         <?php echo $output['agencies_array']['agent_name'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="agent_passwd"><em></em>登录密码</label>
        </dt>
        <dd class="opt">
          <input type="text" id="agent_passwd" name="agent_passwd" class="input-txt">
          <span class="err"></span>
          <p class="notic">不修改密码，请保持为空（6-20位字符，可由英文、数字及标点符号组成。）</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="agent_title"><em>*</em>代理商名称</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['agencies_array']['agent_title'];?>" id="agent_title" name="agent_title" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="agent_contacts">联系人</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['agencies_array']['agent_contacts'];?>" id="agent_contacts" name="agent_contacts" class="input-txt">
          <span class="err"></span> </dd>
      </dl>
     
	  <dl class="row">
        <dt class="tit">
          <label for="agent_mobile">手机</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['agencies_array']['agent_mobile'];?>" id="agent_mobile" name="agent_mobile" class="input-txt">
          <span class="err"></span></dd>
      </dl>
    
	  <dl class="row">
        <dt class="tit">
          <label for="agent_rate">分拥比例</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $output['agencies_array']['agent_rate'];?>" id="agent_rate" name="agent_rate" class="input-txt">%
          <span class="err"></span></dd>
      </dl>
	  <dl class="row">
        <dt class="tit">
          <label for="agent_state">状态</label>
        </dt>
        <dd class="opt">
          <select id="agent_state" name="agent_state">
            <option value="0" <?php if($output['agencies_array']['agent_state']==0){ echo 'selected';}?> >关闭</option>
            <option value="1" <?php if($output['agencies_array']['agent_state']==1){ echo 'selected';}?> >开启</option>
          </select>
          <span class="err"></span></dd>
      </dl>
	  
	  
	  
	  
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/ajaxfileupload/ajaxfileupload.js"></script> 
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.js"></script>
<link href="<?php echo STATIC_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">

$(function(){
	$("#agent_business_license_pic").change(function(){
		$("#agent_business_license").val($(this).val());
	});
	$("#agent_idcard_pic").change(function(){
		$("#agent_idcard").val($(this).val());
	});
	$("#agent_idcard_back_pic").change(function(){
		$("#agent_idcard_back").val($(this).val());
	});
	$('input[class="type-file-file"]').change(uploadChange);
	function uploadChange(){
		var filepath=$(this).val();
		var extStart=filepath.lastIndexOf(".");
		var ext=filepath.substring(extStart,filepath.length).toUpperCase();
		if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
			alert("file type error");
			$(this).attr('value','');
			return false;
		}
		
	}	

	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
    if($("#user_form").valid()){
     $("#user_form").submit();
	}
	});
    $('#user_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
            agent_title   : {
                required : true
                
            }
        },
        messages : {
            agent_title  : {
                required : '<i class="fa fa-exclamation-bbs"></i>请输入代理名称'
            }
        }
    });
});
</script> 
