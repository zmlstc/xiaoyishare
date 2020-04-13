<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<title><?php echo $output['html_title'];?></title>
<link href="<?php echo AGENTS_STATIC_URL?>/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo AGENTS_TEMPLATES_URL?>/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo AGENTS_STATIC_URL?>/font/css/font-awesome.min.css" rel="stylesheet" />
<script type="text/javascript">
var AGENTS_SITE_URL = '<?php echo AGENTS_SITE_URL;?>';
var AGENTS_TEMPLATES_URL = '<?php echo AGENTS_TEMPLATES_URL;?>';
var AGENTS_STATIC_URL = '<?php echo AGENTS_STATIC_URL;?>';
var SITEURL = '<?php echo AGENTS_SITE_URL;?>';
</script>
<script type="text/javascript" src="<?php echo AGENTS_STATIC_URL;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/common.js"></script>
<script type="text/javascript" src="<?php echo AGENTS_STATIC_URL;?>/js/dialog/dialog.js" id="wt_dialog" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo AGENTS_STATIC_URL;?>/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo AGENTS_STATIC_URL;?>/js/jquery.bgColorSelector.js"></script>
<script type="text/javascript" src="<?php echo AGENTS_STATIC_URL;?>/js/admincp.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/ajaxfileupload/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.js" id="cssfile2"></script>
<link href="<?php echo STATIC_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="admiwtp-map ui-widget-content" wttype="map_nav" style="display:none;" id="draggable" >
  <div class="title ui-widget-header" >
    <h3><?php echo $lang['wt_admin_navigation'];?></h3>
    <h5><?php echo $lang['wt_admin_navigation_subhead'];?></h5>
    <span><a wttype="map_off" href="JavaScript:void(0);">X</a></span> </div>
  <div class="content"> <?php echo $output['map_nav'];?> </div>
  <script>
//固定层移动
$(function(){
	//管理显示与隐藏
            $("#admin-manager-btn").click(function () {
                if ($(".manager-menu").css("display") == "none") {
                    $(".manager-menu").css('display', 'block'); 
					$("#admin-manager-btn").attr("title","关闭快捷管理"); 
					$("#admin-manager-btn").removeClass().addClass("arrow-close");
                }
                else {
                    $(".manager-menu").css('display', 'none');
					$("#admin-manager-btn").attr("title","显示快捷管理");
					$("#admin-manager-btn").removeClass().addClass("arrow");
                }           
            });
	
	$("#draggable").draggable({
		handle: "div.title"
	});
	$("div.title").disableSelection()

	

});
//裁剪图片后返回接收函数
function call_back(picname){
    $.getJSON('index.php?w=index&t=save_avatar&avatar=' + picname, function(data){
        if (data) {
            $('img[wttype="admin_avatar"]').attr('src', '<?php echo UPLOAD_SITE_URL . '/' . ATTACH_ADMIN_AVATAR?>/' + picname);
        }
    });
}
</script> 
</div>
<div class="admiwtp-header">
  <div class="bgSelector"></div>
  <div id="foldSidebar"><i class="fa fa-compress" title="侧边导航展开/收起"></i></div>
  <div class="admiwtp-name">
    <h2><?php echo $output['html_title'];?><br>代理商管理中心</h2>
  </div>
  <div class="wt-module-menu">
    <ul class="wt-row">
      <?php echo $output['top_nav'];?>
    </ul>
  </div>
  <div class="admiwtp-header-r">
    <div class="manager">
      <dl>
        <dt class="name"><?php echo $output['admin_info']['name'];?></dt>
        <dd class="group"><?php echo $output['admin_info']['gname'];?></dd>
      </dl>
      <span class="avatar">
      <img alt="" wttype="admin_avatar" src="<?php  AGENTS_TEMPLATES_URL.'/images/login/admin.png';?>"> </span><i class="arrow" id="admin-manager-btn" title="显示快捷管理菜单"></i>
      <div class="manager-menu">
        <div class="title">
          <h4>最后登录</h4>
          <a href="javascript:void(0);" onclick="CUR_DIALOG = ajax_form('modifypw', '修改密码', 'index.php?w=index&t=modifypw');" class="edit-password">修改密码</a> </div>
        <div class="login-date">
          <?php if($output['admin_info']['time'] > 0) { echo date('Y-m-d H:i:s', $output['admin_info']['time']);} else { echo '--';}?>
          <span>(IP:
          <?php if (!empty($output['admin_info']['ip'])) { echo $output['admin_info']['ip'];} else { echo '--';}?>
          )</span> </div>
        
      </div>
    </div>
    <ul class="operate wt-row">
     
      <li><a class="homepage show-option" target="_blank" href="<?php echo BASE_SITE_URL;?>" title="新窗口打开商城首页">&nbsp;</a></li>
      <li><a class="login-out show-option" href="index.php?w=index&t=logout" title="退出">&nbsp;</a></li>
    </ul>
  </div>
  <div class="clear"></div>
</div>
<div class="admiwtp-container unfold">
  <div class="admiwtp-container-left">
    <div class="top-border"><span class="nav-side"></span><span class="sub-side"></span></div>
    <?php echo $output['left_nav'];?>
    <div class="about" title="关于系统" onclick="ajax_form('about', '', '<?php echo urlAdmin('aboutus');?>', 640);"><i class="fa fa-copyright"></i><span>ShopWT.com</span></div>
  </div>
  <div class="admiwtp-container-right">
    <div class="top-border"></div>
    <iframe src="" id="workspace" name="workspace" style="overflow: visible;" frameborder="0" width="100%" height="94%" scrolling="yes" onload="window.parent"></iframe>
  </div>
</div>
</body>
</html>
