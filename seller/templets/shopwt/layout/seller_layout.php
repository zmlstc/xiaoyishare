<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商家中心</title>
<link href="<?php echo SELLER_TEMPLATES_URL?>/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo SELLER_TEMPLATES_URL?>/css/seller.css" rel="stylesheet" type="text/css">
<link href="<?php echo SELLER_TEMPLATES_URL;?>/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var SITEURL = '<?php echo BASE_SITE_URL;?>';var MEMBER_SITE_URL = '<?php echo MEMBER_SITE_URL;?>';var STATIC_SITE_URL = '<?php echo STATIC_SITE_URL;?>';var SELLER_TEMPLATES_URL = '<?php echo SELLER_TEMPLATES_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SELLER_TEMPLATES_URL;?>';</script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/seller.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/waypoints.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/common.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/member.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/dialog/dialog.js" id="wt_dialog" charset="utf-8"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="<?php echo STATIC_SITE_URL;?>/js/html5shiv.js"></script>
      <script src="<?php echo STATIC_SITE_URL;?>/js/respond.min.js"></script>
<![endif]-->
</head>

<body>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/ToolTip.js"></script>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<?php if (!empty($output['store_closed'])) { ?>
<div class="store-closed"><i class="icon-warning-sign"></i>
  <dl>
    <dt>您的店铺已被平台关闭</dt>
    <dd>关闭原因：<?php echo $output['store_close_info'];?></dd>
    <dd>在此期间，您的店铺以及商品将无法访问；如果您有异议或申诉请及时联系平台管理。</dd>
  </dl>
</div>
<?php } ?>
<header class="wtsc-head-box w">
  <div class="wrapper">
    <div class="wtsc-admin">
      <dl class="wtsc-admin-info">
        <dt class="admin-avatar"><img src="<?php echo getMemberAvatarForID($_SESSION['member_id']);?>" width="32" class="pngFix" alt=""/></dt>
        <dd class="admin-name"><?php echo $_SESSION['seller_name'];?></dd>
        <dd class="admin-permission">微笑每一天</dd>
      </dl>
      <div class="wtsc-admin-function"><a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$_SESSION['store_id']), $output['store_info']['store_domain']);?>" title="前往店铺" target="_blank"><i class="icon-home"></i></a><a href="<?php echo urlMember('member_security', 'auth',array('type'=>'modify_pwd'));?>" title="修改密码" target="_blank"><i class="icon-wrench"></i></a><a href="<?php echo urlSeller('logout', 'logout');;?>" title="安全退出"><i class="icon-signout"></i></a></div>
    </div>
    <div class="center-logo"> <a href="<?php echo BASE_SITE_URL;?>" target="_blank"><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('seller_center_logo');?>" class="pngFix" alt=""/></a>
      <h1>商家中心</h1>
    </div>
    <nav class="wtsc-nav">
      <dl class="<?php echo $output['current_menu']['model'] == 'index'?'current':'';?>">
        <dt><a href="<?php echo urlSeller('index');?>">首页</a></dt>
        <dd class="arrow"></dd>
      </dl>
      <?php if(!empty($output['menu']) && is_array($output['menu'])) {?>
      <?php foreach($output['menu'] as $key => $menu_value) {?>
      <dl class="<?php echo $output['current_menu']['model'] == $key?'current':'';?>">
        <dt><a href="<?php echo $key != 'fenxiao' ? '#':urlFenxiao($menu_value['child'][key($menu_value['child'])]['w'],$menu_value['child'][key($menu_value['child'])]['t']);?>"><?php echo $menu_value['name'];?></a></dt>
        <dd>
          <ul>
            <?php if(!empty($menu_value['child']) && is_array($menu_value['child'])) {?>
            <?php foreach($menu_value['child'] as $submenu_value) {?>
            <li> <a href="<?php echo $key != 'fenxiao' ? urlSeller($submenu_value['w'],$submenu_value['t']):urlFenxiao($submenu_value['w'],$submenu_value['t']);?>"> <?php echo $submenu_value['name'];?> </a> </li>
            <?php } ?>
            <?php } ?>
          </ul>
        </dd>
        <dd class="arrow"></dd>
      </dl>
      <?php } ?>
      <?php } ?>
    </nav>
        <div class="index-search-container">
      <div class="index-sitemap"><a href="javascript:void(0);">导航管理 <i class="icon-angle-down"></i></a>
        <div class="sitemap-menu-arrow"></div>
        <div class="sitemap-menu">
          <div class="title-bar">
            <h2> <i class="icon-sitemap"></i>管理导航<em>小提示：添加您经常使用的功能到首页侧边栏，方便操作。</em> </h2>
            <span id="closeSitemap" class="close">X</span></div>
          <div id="quicklink_list" class="content">
            <?php if(!empty($output['menu']) && is_array($output['menu'])) {?>
            <?php foreach($output['menu'] as $key => $menu_value) {?>
            <dl <?php echo ($key == 'sale' ? 'class="double"' : '');?>>
              <dt><?php echo $menu_value['name'];?></dt>
              <?php if(!empty($menu_value['child']) && is_array($menu_value['child'])) {?>
              <?php foreach($menu_value['child'] as $submenu_value) {?>
              <dd <?php if(!empty($output['seller_quicklink'])) {echo in_array($submenu_value['w'], $output['seller_quicklink'])?'class="selected"':'';}?>><i wttype="btn_add_quicklink" data-quicklink-w="<?php echo $submenu_value['w'];?>" class="icon-check" title="添加为常用功能菜单"></i><a href="<?php echo urlSeller($submenu_value['w'],$submenu_value['t']);?>"> <?php echo $submenu_value['name'];?> </a></dd>
              <?php } ?>
              <?php } ?>
            </dl>
            <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</header>
<?php if(!$output['seller_layout_no_menu']) { ?>
<div class="wtsc-box wrapper">
  <div id="layoutLeft" class="wtsc-box-left">
    <div id="sidebar" class="sidebar">
      <div class="column-title" id="main-nav"><span class="ico-<?php echo $output['current_menu']['model'];?>"></span>
        <h2><?php echo $output['current_menu']['model_name'];?></h2>
      </div>
      <div class="column-menu">
        <ul id="seller_center_left_menu">
          <?php if(!empty($output['left_menu']) && is_array($output['left_menu'])) {?>
          <?php foreach($output['left_menu'] as $submenu_value) {?>
          <li <?php echo $_GET['w'] == 'seller_center'?"id='quicklink_".$submenu_value['w']."'":'';?>class="<?php echo $submenu_value['w'] == $_GET['w']?'current':'';?>"> 
            <a href="<?php echo strpos($submenu_value['w'],'store_fx_')===0 ? urlFenxiao($submenu_value['w'],$submenu_value['t']):urlSeller($submenu_value['w'],$submenu_value['t']);?>"> <?php echo $submenu_value['name'];?> </a> </li>
          <?php } ?>
          <?php } else { ?>
          <?php if ($_GET['w'] == 'seller_center') { ?>
          <div class="add-quickmenu"><a href="javascript:void(0);"><i class="icon-plus"></i>添加常用功能菜单</a></div>
          <?php } ?>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
  <div id="layoutRight" class="wtsc-box-right">
    <div class="wtsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i><?php echo $output['current_menu']['model_name'];?><i class="icon-angle-right"></i><?php echo $output['current_menu']['name'];?></div>
    <div class="main-content" id="mainContent">
      <?php require_once($tpl_file); ?>
    </div>
  </div>
</div>
<?php } else { ?>
<div class="wrapper">
  <?php require_once($tpl_file); ?>
</div>
<?php } ?>
<script type="text/javascript">
</script>
<script type="text/javascript">
$(document).ready(function(){
    //添加删除快捷操作
    $('[wttype="btn_add_quicklink"]').on('click', function() {
        var $quicklink_item = $(this).parent();
        var item = $(this).attr('data-quicklink-w');
        if($quicklink_item.hasClass('selected')) {
            $.post("<?php echo urlSeller('index', 'quicklink_del');?>", { item: item }, function(data) {
                $quicklink_item.removeClass('selected');
                $('#quicklink_' + item).remove();
            }, "json");
        } else {
            var count = $('#quicklink_list').find('dd.selected').length;
            if(count >= 8) {
                showError('快捷操作最多添加8个');
            } else {
                $.post("<?php echo urlSeller('index', 'quicklink_add');?>", { item: item }, function(data) {
                    $quicklink_item.addClass('selected');
                    <?php if ($_GET['w'] == 'index') { ?>
                        var $link = $quicklink_item.find('a');
                        var menu_name = $link.text();
                        var menu_link = $link.attr('href');
                        var menu_item = '<li id="quicklink_' + item + '"><a href="' + menu_link + '">' + menu_name + '</a></li>';
                        $(menu_item).appendTo('#seller_center_left_menu').hide().fadeIn();
                    <?php } ?>
                }, "json");
            }
        }
    });
    //浮动导航  waypoints.js
    $("#sidebar,#mainContent").waypoint(function(event, direction) {
        $(this).parent().toggleClass('sticky', direction === "down");
        event.stopPropagation();
        });
    });
    // 搜索商品不能为空
    $('input[wttype="search_submit"]').click(function(){
        if ($('input[wttype="search_text"]').val() == '') {
            return false;
        }
    });
</script>
<?php require_once template('footer');?>
<div id="tbox">
  <div class="btn" id="msg"><a href="<?php echo urlSeller('msg', 'index');?>"><i class="msg"><?php if ($output['store_msg_num'] > 0) { ?><em><?php echo $output['store_msg_num'];?></em><?php } ?></i></a></div>
  <div class="btn" id="im"><i class="im"><em id="new_msg" style="display:none;"></em></i></div>
  <div class="btn" id="gotop"><i class="top"></i></div>
</div>
</body>
</html>
