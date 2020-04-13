<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?w=agencies&t=agencies" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>代理管理 - <?php echo $lang['wt_new']?>代理</h3>
        <h5>代理商信息设置管理</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
    </div>
  </div>
  
  <form id="user_form" enctype="multipart/form-data" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="agent_name"><em>*</em>登录账户</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="agent_name" id="agent_name" class="input-txt">
          <span class="err"></span>
          <p class="notic">6-20位字符，可由中文、英文、数字及“_”、“-”组成。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="agent_passwd"><em>*</em>登录密码</label>
        </dt>
        <dd class="opt">
          <input type="text" id="agent_passwd" name="agent_passwd" class="input-txt">
          <span class="err"></span>
          <p class="notic">6-20位字符，可由英文、数字及标点符号组成。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="agent_title"><em>*</em>代理商名称</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="agent_title" name="agent_title" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="agent_contacts">联系人</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="agent_contacts" name="agent_contacts" class="input-txt">
          <span class="err"></span> </dd>
      </dl>
     
	  <dl class="row">
        <dt class="tit">
          <label for="agent_mobile">手机</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="agent_mobile" name="agent_mobile" class="input-txt">
          <span class="err"></span></dd>
      </dl>
     
	  <dl class="row">
        <dt class="tit">
          <label for="agent_rate">分拥比例</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="agent_rate" name="agent_rate" class="input-txt">%
          <span class="err"></span></dd>
      </dl>
	 <dl class="row">
        <dt class="tit">
          <label for="agent_state">状态</label>
        </dt>
        <dd class="opt">
          <select id="agent_state" name="agent_state">
            <option value="0" >关闭</option>
            <option value="1" >开启</option>
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
			agent_name: {
				required : true,
				minlength: 6,
				maxlength: 20,
				remote   : {
                    url :'<?php echo urlAdminShop('agencies','ajax',array('branch'=>'check_user_name'));?>',
                    type:'get',
                    data:{
                        agent_name : function(){
                            return $('#agent_name').val();
                        }
                    }
                }
			},
            agent_passwd: {
				required : true,
                maxlength: 20,
                minlength: 6
            },
            agent_title   : {
                required : true
                
            }
        },
        messages : {
			agent_name: {
				required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['member_add_name_null']?>',
				maxlength: '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['member_add_name_length']?>',
				minlength: '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['member_add_name_length']?>',
				remote   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['member_add_name_exists']?>'
			},
            agent_passwd : {
				required : '<i class="fa fa-exclamation-bbs"></i><?php echo '密码不能为空'; ?>',
                maxlength: '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['member_edit_password_tip']?>',
                minlength: '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['member_edit_password_tip']?>'
            },
            agent_title  : {
                required : '<i class="fa fa-exclamation-bbs"></i>请输入代理名称'
            }
        }
    });
});
</script> 
