<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('store','sname_modify');?>" title="返回<?php echo $lang['pending'];?>列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>店铺名称 - 查看店铺名申请信息</h3>
        <h5><?php echo $lang['wt_store_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
 
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">申请信息</th>
      </tr>
    </thead>
    <tbody><tr>
        <th class="w150">原店铺名：</th>
        <td><?php echo $output['detail']['store_name_old'];?></td>
      </tr>
      <tr>
        <th class="w150">新店铺名：</th>
        <td><?php echo $output['detail']['store_name'];?></td>
      </tr>
      <tr>
        <th>营业执照<br />
          电子版：</th>
        <td colspan="20"><a wttype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['detail']['zhizhao_pic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['detail']['zhizhao_pic']);?>" alt="" /> </a></td>
      </tr>
      <tr>
        <th>申请凭证<br />
          电子版：</th>
        <td colspan="20"><a wttype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['detail']['pingzheng_pic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['detail']['pingzheng_pic']);?>" alt="" /> </a></td>
      </tr>
      <tr>
        <th class="w150">申请时间：</th>
        <td><?php echo  date('Y-m-d', $output['detail']['addtime']);?></td>
      </tr>
    </tbody>
  </table>

  <form id="form_store_verify" action="<?php echo urlAdminShop('store','sname_verify');?>" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="id" type="hidden" value="<?php echo $output['detail']['id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
     
      <tbody>
	 
        <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) { ?>
        <tr>
          <th>审核意见：</th>
          <td colspan="2"><textarea id="joinin_message" name="joinin_message"></textarea></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if(intval($output['detail']['state'])==0) { ?>
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
            if($('#joinin_message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }
            if(confirm('确认拒绝申请？')) {
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