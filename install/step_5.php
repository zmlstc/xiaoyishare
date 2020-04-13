<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $html_title;?></title>
<link href="css/install.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../system/static/js/jquery.min.js"></script>
<link href="../system/static/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../system/static/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="../system/static/js/jquery.mousewheel.js"></script>
</head>

<body>
<?php echo $html_header;?>
<div class="main">
  <div class="step-box" id="step4">
    <div class="text-nav">
      <h2>Step.4 恭喜，完成安装</h2>
      <h5>选择下面的链接进入前台或后台</h5>
    </div>
    <div class="procedure-nav">
      <div class="schedule-ico"><span class="a"></span><span class="b"></span><span class="c"></span><span class="d"></span></div>
      <div class="schedule-point-now"><span class="a"></span><span class="b"></span><span class="c"></span><span class="d"></span></div>
      <div class="schedule-point-bg"><span class="a"></span><span class="b"></span><span class="c"></span><span class="d"></span></div>
      <div class="schedule-line-now"><em></em></div>
      <div class="schedule-line-bg"></div>
      <div class="schedule-text"><span class="a">阅读使用协议</span><span class="b">系统环境检测</span><span class="c">数据库设置</span><span class="d">完成安装</span></div>
    </div>
  </div>
	<div class="final-tip">还差一步：请登录后台，菜单路经：<b>系统-设置-更新缓存</b>，更新缓存后完成安装。</div>
  <div class="final-intro">
    <p><strong>后台管理链接：</strong><a href="<?php echo $auto_site_url;?>/wt"><?php echo $auto_site_url;?>/wt</a></p>
  </div>
</div>
<?php echo $html_footer;?>
<script type="text/javascript">
$(document).ready(function(){
	//自定义滚定条
	$('#text-box').perfectScrollbar();
});
</script>
</body>
</html>
