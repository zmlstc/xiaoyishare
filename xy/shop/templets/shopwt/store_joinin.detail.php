<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('store','store_joinin');?>" title="返回<?php echo $lang['pending'];?>列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['wt_store_manage'];?> - 查看“<?php echo $output['joinin_detail']['shop_phone'];?>”的店铺注册信息</h3>
        <h5><?php echo $lang['wt_store_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">公司及联系人信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">公司名称：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_name'];?></td>
      </tr>
      <tr>
        <th>公司所在地：</th>
        <td><?php echo $output['joinin_detail']['company_address'];?></td>
        <th>公司详细地址：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_address_detail'];?></td>
      </tr>
      <tr>
        <th>公司电话：</th>
        <td><?php echo $output['joinin_detail']['company_phone'];?></td>
        <th>联系人姓名：</th>
        <td><?php echo $output['joinin_detail']['contacts_name'];?></td>
        <th>联系人电话：</th>
        <td><?php echo $output['joinin_detail']['contacts_phone'];?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">营业执照信息（副本）</th>
      </tr>
    </thead>
    <tbody><tr>
        <th class="w150">企业类型：</th>
        <td><?php echo $output['joinin_detail']['company_type']=='1'?'企业':'个体';?></td>
      </tr>
      <tr>
        <th class="w150">营业执照号：</th>
        <td><?php echo $output['joinin_detail']['business_licence_number'];?></td>
      </tr>
      <tr>
        <th>营业执照<br />
          电子版：</th>
        <td colspan="20"><a wttype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['organization_code_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['organization_code_electronic']);?>" alt="" /> </a></td>
      </tr>
      <tr>
        <th class="w150">法人：</th>
        <td><?php echo $output['joinin_detail']['company_user_name'];?></td>
      </tr>
      <tr>
        <th>法人身份证<br />
          电子版：</th>
        <td colspan="20"><a wttype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['company_user_name_pic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['company_user_name_pic']);?>" alt="" /> </a></td>
      </tr>
      
    </tbody>
  </table>

  <form id="form_store_verify" action="<?php echo urlAdminShop('store','store_joinin_verify');?>" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="sj_id" type="hidden" value="<?php echo $output['joinin_detail']['sj_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">店铺经营信息</th>
        </tr>
      </thead>
      <tbody>
	  <tr>
          <th class="w150">注册手机号码：</th>
          <td><?php echo $output['joinin_detail']['shop_phone'];?></td>
        </tr>
        <tr>
          <th class="w150">商家账号：</th>
          <td><?php echo $output['joinin_detail']['seller_name'];?></td>
        </tr>
        <tr>
          <th class="w150">店铺名称：</th>
          <td><?php echo $output['joinin_detail']['store_name'];?></td>
        </tr>
        <tr>
          <th class="w150">开店时长：</th>
          <td><?php echo $output['joinin_detail']['joinin_year'];?> 年</td>
        </tr>
        <tr>
          <th>店铺行业：</th>
          <td><?php echo $output['joinin_detail']['scinfo'];?></td>
        </tr>


        <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_PAY, STORE_JOIN_STATE_FINAL))) {?>
        <tr>
          <th>付款凭证：</th>
          <td><a wttype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['paying_money_certificate']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['paying_money_certificate']);?>" alt="" /> </a></td>
        </tr>
        <tr>
          <th>付款凭证说明：</th>
          <td><?php echo $output['joinin_detail']['paying_money_certif_exp'];?></td>
        </tr>
        <?php } ?>
        <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) { ?>
        <tr>
          <th>审核意见：</th>
          <td colspan="2"><textarea id="joinin_message" name="joinin_message"></textarea></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) { ?>
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
         /*    $('[wttype="commis_rate"]').each(function(commis_rate) {
                rate = $(this).val();
                if(rate == '') {
                    valid = false;
                    return false;
                }

                var rate = Number($(this).val());
                if(isNaN(rate) || rate < 0 || rate >= 100) {
                    valid = false;
                    return false;
                }
            }); */
            //if(valid) {
                $('#validation_message').hide();
                if(confirm('确认通过申请？')) {
                    $('#verify_type').val('pass');
                    $('#form_store_verify').submit();
                }
            /* } else {
                $('#validation_message').text('请正确填写分佣比例');
                $('#validation_message').show();
            } */
        });
    });
</script>