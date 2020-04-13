<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('member','realverify');?>" title="返回<?php echo $lang['pending'];?>列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>实名验证- 查看验证信息</h3>
        <h5></h5>
      </div>
    </div>
  </div>
  </table>

  <form id="form_store_verify" action="<?php echo urlAdminShop('member','real_verify');?>" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="id" type="hidden" value="<?php echo $output['joinin_detail']['id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">实名信息</th>
        </tr>
      </thead>
      <tbody>
	  <tr>
          <th class="w150">用户ID：</th>
          <td><?php echo $output['joinin_detail']['member_id'];?></td>
        </tr>
        <tr>
          <th class="w150">姓名：</th>
          <td><?php echo $output['joinin_detail']['truename'];?></td>
        </tr>
        <tr>
          <th class="w150">身份证号码：</th>
          <td><?php echo $output['joinin_detail']['cardid'];?></td>
        </tr>
      <tr>
        <th>身份证照片：</th>
        <td colspan="20">
		<a wttype="nyroModal"  href="<?php echo UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'realverify'.DS.$output['joinin_detail']['pic1'];?>"> <img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'realverify'.DS.$output['joinin_detail']['pic1'];?>" alt="" /> </a>
		<a wttype="nyroModal"  href="<?php echo UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'realverify'.DS.$output['joinin_detail']['pic2'];?>"> <img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'realverify'.DS.$output['joinin_detail']['pic2'];?>" alt="" /> </a>
		</td>
      </tr>
        <tr>
          <th class="w150">身份证有效期</th>
          <td><?php echo $output['joinin_detail']['iddate'];?> </td>
        </tr>
        <tr>
          <th>申请时间：</th>
          <td><?php echo date('Y-m-d H:i:s',$output['joinin_detail']['addtime']);?></td>
        </tr>


        <?php if(intval($output['joinin_detail']['state'])==1) {?>
       
        <tr>
          <th>审核时间：</th>
          <td><?php echo date('Y-m-d H:i:s',$output['joinin_detail']['verify_time']);?></td>
        </tr>
        <?php } ?>
       
      </tbody>
    </table>
    <?php if(intval($output['joinin_detail']['state'])==0) { ?>
    <div id="validation_message" style="color:red;display:none;"></div>
    <div class="bottom"><a id="btn_pass" class="wtap-btn-big wtap-btn-green mr10" href="JavaScript:void(0);">通过</a><a id="btn_fail" class="wtap-btn-big wtap-btn-red" href="JavaScript:void(0);">拒绝</a> </div>
    <?php } ?>
  </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_STATIC_URL;?>/js/jquery.nyroModal.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('a[wttype="nyroModal"]').nyroModal();

        $('#btn_fail').on('click', function() {
            
            if(confirm('确认拒绝通过？')) {
                $('#verify_type').val('fail');
                $('#form_store_verify').submit();
            }
        });
        $('#btn_pass').on('click', function() {
            var valid = true;
       
                $('#validation_message').hide();
                if(confirm('确认通过申请？')) {
                    $('#verify_type').val('pass');
                    $('#form_store_verify').submit();
                }
           
        });
    });
</script>