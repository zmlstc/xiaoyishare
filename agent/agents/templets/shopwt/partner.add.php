<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?w=partner&t=partner" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>合伙人管理 - <?php echo $lang['wt_new']?>合伙人</h3>
        <h5>合伙人信息设置管理</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
    </div>
  </div>
  
  <form id="user_form" enctype="multipart/form-data" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="partner_name"><em>*</em>姓名</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="partner_name" id="partner_name" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="partner_jobnum"><em>*</em>工号</label>
        </dt>
        <dd class="opt">
          <input type="text" id="partner_jobnum" name="partner_jobnum" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
     
      <dl class="row">
        <dt class="tit">
          <label for="member_ww">身份证照片</label>
        </dt>
        <dd class="opt">
         <div class="input-file-show"><span class="type-file-box">
            <input class="type-file-file" id="agent_idcard_pic" name="agent_idcard_pic" type="file" size="30" hidefocus="true" title="点击按钮选择文件并提交表单后上传生效">
            <input type="text" name="agent_idcard" id="agent_idcard" class="type-file-text" />
            <input type="button" name="button2" id="button2" value="选择上传..." class="type-file-button" />
            </span></div>
          <p class="notic">请使用jpg/gif/png格式的图片。</p>
		 </dd>
      </dl>
     
	  <dl class="row">
        <dt class="tit">
          <label for="partner_mobile"><em>*</em>手机</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="partner_mobile" name="partner_mobile" class="input-txt">
          <span class="err"></span></dd>
      </dl>
	  	<dl class="row">
        <dt class="tit">
          <label for="partner_way">提成方式</label>
        </dt>
        <dd class="opt">
          <select id="partner_way" name="partner_way">
            <option value="1" >按比例</option>
          <option value="2" >定额</option>
          </select>
          <span class="err"></span></dd>
      </dl>
      <dl class="row" id="select-way2" style="display:none;">
        <dt class="tit">
          <label for="partner_guding">定额</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="partner_guding" name="partner_guding" class="input-txt">元
          <span class="err"></span></dd>
      </dl>
	  <dl class="row" id="select-way1">
        <dt class="tit">
          <label for="partner_rate">提成比例</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" id="partner_rate" name="partner_rate" class="input-txt">%
          <span class="err"></span></dd>
      </dl>
		 <dl class="row">
        <dt class="tit">
          <label for="partner_rate">备注</label>
        </dt>
       <dd class="opt">
          <textarea name="partner_txt" rows="2" class="textarea w400"  maxlength="50" ></textarea>
        <p class="err"></p>
      </dd>
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
	$("#agent_idcard_pic").change(function(){
		$("#agent_idcard").val($(this).val());
	});
	$("#partner_way").change(function(){
		var _way=$(this).children('option:selected').val();
		if(_way==1){
			$('#select-way2').hide();
			$('#select-way1').show();
		}else if(_way==2){
			$('#select-way2').show();
			$('#select-way1').hide();
		}
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
			partner_name: {
				required : true
			},
            partner_jobnum: {
				required : true
            },
            partner_mobile   : {
                required : true
                
            }
        },
        messages : {
			partner_name: {
				required : '<i class="fa fa-exclamation-bbs"></i>请输入姓名'
			},
            partner_jobnum : {
				required : '<i class="fa fa-exclamation-bbs"></i>请输入给工号'
            },
            partner_mobile  : {
                required : '<i class="fa fa-exclamation-bbs"></i>请输入手机号码'
            }
        }
    });
});
</script> 
