<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<iframe frameborder='0' height="670px" width="910px" scrolling="auto" src="<?php echo urlSeller('goods_online','edit_body_ajax',array('goods_id'=>$_GET['goods_id']));?>"></iframe>