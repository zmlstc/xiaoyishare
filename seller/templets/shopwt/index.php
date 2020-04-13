<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="wtsc-index">
  <div class="top-container">
    <div class="basic-info">
      <dl class="wtsc-seller-info">
        <dt class="seller-name">
          <h3><?php echo $_SESSION['seller_name']; ?></h3>
          <h5>(会员帐号：<?php echo $_SESSION['member_name']; ?>)</h5>
        </dt>
        <dd class="store-logo">
          <p><a title="编辑店铺设置" href="<?php echo urlSeller('setting', 'store_setting');?>"><img src="<?php echo getStoreLogo($output['store_info']['store_label'],'store_logo');?>"/></a></p>
         </dd>
        <dd class="seller-permission">管理权限：<strong><?php echo $_SESSION['seller_group_name'];?></strong></dd>
        <dd class="seller-last-login">最后登录：<strong><?php echo $_SESSION['seller_last_login_time'];?></strong> </dd>
        <dd class="store-name"><?php echo $lang['store_name'].$lang['wt_colon'];?><a href="<?php echo urlShop('show_store', 'index', array('store_id' => $_SESSION['store_id']), $output['store_info']['store_domain']);?>" ><?php echo $output['store_info']['store_name'];?></a></dd>
        <dd class="store-grade"><?php echo $lang['store_store_grade'].$lang['wt_colon'];?><strong><?php echo $output['store_info']['grade_name']; ?></strong></dd>
        <dd class="store-validity"><?php echo $lang['store_validity'].$lang['wt_colon'];?><strong><?php echo $output['store_info']['store_end_time_text'];?>
          <?php if ($output['store_info']['reopen_tip']) {?>
          <a href="<?php echo urlSeller('info','reopen');?>">马上续签</a>
          <?php } ?>
          </strong> </dd>
      </dl>
<?php if (!checkPlatformStore()) { ?>
      <div class="detail-rate">
        <h5><?php echo $lang['store_dynamic_evaluation'].$lang['wt_colon'];?></h5>
        <ul>
        <?php  foreach ($output['store_info']['store_credit'] as $value) {?>
        <li>
          <h5><?php echo $value['text'];?></h5>
          <div class="<?php echo $value['percent_class'];?>" title="<?php echo $value['percent_text'];?><?php echo $value['percent'];?>"><?php echo $value['credit'];?><i></i></div>
        </li>
        <?php } ?>
      </ul>
      </div>
<?php } ?>
    </div>
  </div>
  <div class="seller-cont">
    <div class="container type-b" >
      <div class="hd">
        <h3><i class="col1"></i>商城平台公告</h3>
        <h5>平台针对商家发布一些信息</h5>
      </div>
      <div class="content">
        <ul>
          <?php
			if(is_array($output['article_list_seller']) && !empty($output['article_list_seller'])) {
				foreach($output['article_list_seller'] as $val) {
			?>
          <li><a target="_blank" <?php if($val['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($val['article_url']!='')echo $val['article_url'];else echo urlShop('article', 'show', array('article_id'=>$val['article_id']));?>" title="<?php echo $val['article_title']; ?>">
            <?php echo $val['article_title'];?></a></li>
          <?php
				}
			}
			 ?>
        </ul>
    </div>
	 </div>

    <div class="container type-c">
      <div class="hd">
        <h3><i class="col3"></i>单品销售统计</h3>
        <h5>店铺最新销售统计，订单量和订单金额</h5>
      </div>
      <div class="content">
        <table class="wtsc-default-table">
          <thead>
            <tr>
              <th class="w50">项目</th>
              <th>订单量</th>
              <th class="w100">订单金额</th>
            </tr>
          </thead>
          <tbody>
            <tr class="bd-line">
              <td>昨日销量</td>
              <td><?php echo empty($output['daily_sales']['ordernum']) ? '--' : $output['daily_sales']['ordernum'];?></td>
              <td><?php echo empty($output['daily_sales']['orderamount']) ? '--' : $lang['currency'].$output['daily_sales']['orderamount'];?></td>
            </tr>
            <tr class="bd-line">
              <td>月销量</td>
              <td><?php echo empty($output['monthly_sales']['ordernum']) ? '--' : $output['monthly_sales']['ordernum'];?></td>
              <td><?php echo empty($output['monthly_sales']['orderamount']) ? '--' : $lang['currency'].$output['monthly_sales']['orderamount'];?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="container type-b">
      <div class="hd">
        <h3><i class="col1"></i><?php echo $lang['store_site_info'];?></h3>
        <h5>平台与商家联系方式</h5>
      </div>
      <div class="content">
        <dl>
          <?php
			if(is_array($output['phone_array']) && !empty($output['phone_array'])) {
				foreach($output['phone_array'] as $key => $val) {
			?>
          <dd><?php echo $lang['store_site_phone'].($key+1).$lang['wt_colon'];?><?php echo $val;?></dd>
          <?php
				}
			}
			 ?>
          <dd><?php echo $lang['store_site_email'].$lang['wt_colon'];?><?php echo C('site_email');?></dd>
		  <dd><a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $output['setting_config']['wt_qq']; ?>&amp;site=qq&amp;menu=yes" target="_blank">QQ：<img border="0" style=" vertical-align: middle;" src="http://wpa.qq.com/pa?p=2:<?php echo $output['setting_config']['wt_qq']; ?>:51"></a></dd>
        </dl>
      </div>
    </div>
  </div>
</div>
<script>
$(function(){
	var timestamp=Math.round(new Date().getTime()/1000/60);
    $.getJSON('<?php echo urlSeller('index','statistics');?>?rand='+timestamp, null, function(data){
        if (data == null) return false;
        for(var a in data) {
            if(data[a] != 'undefined' && data[a] != 0) {
                var tmp = '';
                if (a != 'goodscount' && a != 'imagecount') {
                    $('#wt_'+a).parents('a').addClass('num');
                }
                $('#wt_'+a).html(data[a]);
            }
        }
    });
});
</script>
