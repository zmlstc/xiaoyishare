<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<!doctype html>
<html>
<head>
<link href="<?php echo AGENTS_TEMPLATES_URL?>/images/aset/style.css" rel="stylesheet" type="text/css">
<meta charset="utf-8">
<title>代理中心</title>
</head>

<body>
<div class="container">
	<div class="centent-left">
	<div class="logo"></div>
	<h1><?php echo  $output['agetn_join']['company_name'];?></h1>
		<h2>欢迎您的加盟！</h2>
</div>
 <form method="post" id="form_login">
  <?php Security::getToken();?>
  <div class="centent-right">
	<div class="title">设置管理账号信息</div>
	<div class="item"><div class="name"></div><input type="text" name="agent_name" placeholder="用户名不超过10个英文字母+数字" /></div>
	<div class="item"><div class="pass"></div><input type="password" name="agent_pwd" placeholder="密码不超过16个英文字母+数字" /></div>
	<div class="item"><div class="confirm"></div><input type="password"  name="agent_pwd2" placeholder="确认输入密码" /></div>
	<div class="item"><div class="place"></div><input type="text" readonly="readonly" placeholder="<?php echo $output['agetn_join']['company_address'];?>" style="background:#eee" /></div>
	 <input name="" class="input-button btn-submit" type="submit" class="submit" value="注册">
	<div class="tip">请您设置代理后台管理账号并注册，完成后将自动跳转至代理后台<a href="<?php echo AGENTS_SITE_URL;?>">登陆页面</a>。</div>
	</div>
</form>
</div>
<div class="copyright">小易共享平台 © 2019 <span>合众文化传媒</span> 版权所有</div>
</body>
</html>
<script>

$(function(){
	//初始化Input的灰色提示信息  
	$('input[tipMsg]').inputTipText({pwd:'password'});
	//登录方式切换
	$('.nc-login-mode').tabulous({
		 effect: 'flip'//动画反转效果
	});	

	
	$("#login_form").validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd');
            error_td.append(error);
            element.parents('dl:first').addClass('error');
        },
        success: function(label) {
            label.parents('dl:first').removeClass('error').find('label').remove();
        },
    	submitHandler:function(form){
    	    ajaxpost('login_form', '', '', 'onerror');
    	},
        onkeyup: false,
		rules: {
			user_name: "required",
			password: "required"
			<?php if(C('captcha_status_login') == '1') { ?>
            ,captcha : {
                required : true,
                remote   : {
                    url : 'index.php?act=seccode&op=check&nchash=<?php echo getNchash();?>',
                    type: 'get',
                    data:{
                        captcha : function(){
                            return $('#captcha').val();
                        }
                    },
                    complete: function(data) {
                        if(data.responseText == 'false') {
                        	document.getElementById('codeimage').src='index.php?act=seccode&op=makecode&type=50,120&nchash=<?php echo getNchash();?>&t=' + Math.random();
                        }
                    }
                }
            }
			<?php } ?>
		},
		messages: {
			user_name: "<i class='icon-exclamation-sign'></i>请输入已注册的用户名或手机号",
			password: "<i class='icon-exclamation-sign'></i><?php echo $lang['login_index_input_password'];?>"
			<?php if(C('captcha_status_login') == '1') { ?>
            ,captcha : {
                required : '<i class="icon-remove-circle" title="<?php echo $lang['login_index_input_checkcode'];?>"></i>',
				remote	 : '<i class="icon-remove-circle" title="<?php echo $lang['login_index_input_checkcode'];?>"></i>'
            }
			<?php } ?>
		}
	});

    // 勾选自动登录显示隐藏文字
    $('input[name="auto_login"]').click(function(){
        if ($(this).attr('checked')){
            $(this).attr('checked', true).next().show();
        } else {
            $(this).attr('checked', false).next().hide();
        }
    });
});
</script>