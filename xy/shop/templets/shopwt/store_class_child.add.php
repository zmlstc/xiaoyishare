<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('store_class','store_class',array('pid'=>$output['cInfo']['sc_id']));?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['store_class'];?></h3>
        <h5><?php echo $lang['store_class_subhead'];?></h5>
      </div>
    </div>
  </div>
  <form id="store_class_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
	 <dl class="row">
        <dt class="tit">
          <label for="sc_name">上级分类名称</label>
        </dt>
        <dd class="opt">
          <?php echo $output['pclass']['sc_name'];?>
		  
		   <input type="hidden" value="<?php echo $output['pclass']['sc_id'];?>" name="parent_id" id="parent_id" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="sc_name"><em>*</em><?php echo $lang['store_class_name'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="sc_name" id="sc_name" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>平台总佣金</label>
        </dt>
        <dd class="opt">
          <input id="commis_rate" class="w60" type="text" value="" name="commis_rate">
          <i>%</i> <span class="err"></span>
          <p class="notic mb10">比例必须为0-100的数字，支持小数点后2位。</p>
          
        </dd>
      </dl>
	  
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>邀请店铺返佣金比例</label>
        </dt>
        <dd class="opt">
          <input id="commis_rate_invstore" class="w60" type="text" value="" name="commis_rate_invstore">
          <i>%</i> <span class="err"></span>
          <p class="notic mb10">比例必须为0-100的数字，支持小数点后2位。</p>
          
        </dd>
      </dl>
	  
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>邀请会员返佣金比例</label>
        </dt>
        <dd class="opt">
          <input id="commis_rate_invmember" class="w60" type="text" value="" name="commis_rate_invmember">
          <i>%</i> <span class="err"></span>
          <p class="notic mb10">比例必须为0-100的数字，支持小数点后2位。</p>
          
        </dd>
      </dl>
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>系统返佣金比例</label>
        </dt>
        <dd class="opt">
          <input id="commis_rate_system" class="w60" type="text" value="" name="commis_rate_system">
          <i>%</i> <span class="err"></span>
          <p class="notic mb10">比例必须为0-100的数字，支持小数点后2位。</p>
          
        </dd>
      </dl>
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>积分返佣金比例</label>
        </dt>
        <dd class="opt">
          <input id="commis_rate_points" class="w60" type="text" value="" name="commis_rate_points">
          <i>%</i> <span class="err"></span>
          <p class="notic mb10">比例必须为0-100的数字，支持小数点后2位。</p>
          
        </dd>
      </dl>

	  <dl class="row">
        <dt class="tit">
          <label>配套设施</label>
        </dt>
        <dd class="opt">
         <div id="spec_div" class="scrollbar-box">
            <?php if(is_array($output['f_list']) && !empty($output['f_list'])){?>
            <div class="wtap-type-spec-list">
              <?php foreach($output['f_list'] as $k=>$val){?>             
                  <label>
                    <input class="checkitem" wt_type="change_default_fc_value" type="checkbox" value="<?php echo $val['fc_id'];?>" name="fc_id[]">
                    <?php echo $val['fc_name'];?></label>
                 
              <?php }?>
            </div>
            <?php }else{?>
            <div>如果当前选项中没有适合的设施分类，可以去<a href="JavaScript:void(0);" onclick="window.parent.openItem('facility|facility_class')">设施分类管理</a>功能中添加新的分类</div>
            <?php }?>
          </div>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="sc_sort"><?php echo $lang['wt_sort'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" value="255" name="sc_sort" id="sc_sort" class="input-txt">
          <span class="err"></span>
          <p class="notic"><?php echo $lang['update_sort'];?></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
	
    if($("#store_class_form").valid()){
     $("#store_class_form").submit();
	}
	});
});

$(document).ready(function(){
	$('#store_class_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
            sc_name : {
                required : true,
                remote   : {                
                url :'<?php echo urlAdminShop('store_class','ajax_check_name');?>',
                type:'get',
                data:{
                    sc_name : function(){
                        return $('#sc_name').val();
                    }
                  }
                }
            },
            sc_sort : {
                number   : true
            }
        },
        messages : {
            sc_name : {
                required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_no_null'];?>',
                remote   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_name_is_there'];?>'
            },
            sc_sort  : {
                number   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['store_class_sort_only_number'];?>'
            }
        }
    });
});

</script>