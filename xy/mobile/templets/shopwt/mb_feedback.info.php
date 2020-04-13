<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminMobile('mb_feedback','flist');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>意见反馈 - 查看</h3>
        <h5>意见反馈管理</h5>
      </div>
    </div>
  </div>

    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label >反馈类型</label>
        </dt>
        <dd class="opt"><?php echo $output['info']['fbclass'];?></dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label >反馈人</label>
        </dt>
        <dd class="opt"><?php echo $output['info']['member_name'];?>(id:<?php echo $output['info']['member_id'];?>)</dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label >反馈内容</label>
        </dt>
        <dd class="opt"><?php echo $output['info']['content'];?></dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label >图片</label>
        </dt>
        <dd class="opt"><?php if(!empty($output['info']['fb_image_240'])&&is_array($output['info']['fb_image_240'])){ 
		foreach($output['info']['fb_image_240'] as $v){ ?>
			<img src="<?php echo $v;?>" />
			
		<?php } }?></dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label >联系方式</label>
        </dt>
        <dd class="opt"><?php echo $output['info']['lxway'];?></dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label >反馈时间</label>
        </dt>
        <dd class="opt"><?php echo date('Y-m-d H:i:s',$output['info']['ftime']);?></dd>
      </dl>
      <div class="bot"></div>
    </div>
</div>
