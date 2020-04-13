<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="wtsc-form-default">
  <form method="post"  action="<?php echo urlSeller('facility','facility');?>" id="my_store_form">
    <input type="hidden" name="form_submit" value="ok" />

 
	<?php if(!empty($output['fc_list'])&& is_array($output['fc_list'])){
		 foreach($output['fc_list'] as $fc){ ?>
			<dl>
			  <dt><?php echo $fc['fc_name'].$lang['wt_colon']; ?></dt>
			  <dd>
				  <?php if(is_array($fc['flist']) && !empty($fc['flist'])){?>
					<div class="wtap-type-spec-list">
					  <?php foreach($fc['flist'] as $k=>$val){?>             
						  <label>
							<input class="checkitem"  type="checkbox" value="<?php echo $val['f_id'];?>" name="f_id[]" <?php if(in_array($val['f_id'],$output['f_ids'])){ echo 'checked=checked';} ?>>
							<?php echo $val['f_name'];?></label>
						 
					  <?php }?>
					</div>
					<?php } ?>
			  </dd>
			</dl>
		 <?php }?>
		 
		 <div class="bottom">
        <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['store_goods_class_submit'];?>" /></label>
      </div>
	<?php } else{?>
		<div style="text-align: center;padding-top: 50px;">平台未绑定有相关设施，可联系平台处理。
       </div>
	
	<?php } ?>
  </form>
</div>
<script type="text/javascript">
var SITEURL = "<?php echo BASE_SITE_URL; ?>";

$(function(){



});
</script>
