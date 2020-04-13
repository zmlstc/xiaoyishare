<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('agencies','arealist',array('agent_id'=>$output['agencies_info']['agent_id']));?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>新增 “<?php echo $output['agencies_info']['agent_title'];?>” 代理区域</h3>
        <h5>代理商区域信息</h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
      <li></li>
    </ul>
  </div>
  <form id="store_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="agent_id" value="<?php echo $output['agencies_info']['agent_id']; ?>" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="store_name">代理商名称</label>
        </dt>
        <dd class="opt">
         <?php echo $output['agencies_info']['agent_title'];?>
        </dd>
      </dl>
  
      <dl class="row">
        <dt class="tit">
          <label for="province_id">代理地区</label>
        </dt>
        <dd class="opt">
          <input type="hidden" name="region" id="region" value="" />
          <input type="hidden" value="0" name="province_id" id="province_id">
          <input type="hidden" value="0" name="city_id" id="city_id">
          <input type="hidden" value="0" name="area_id" id="area_id">
          <span class="err"></span>
        </dd>
      </dl>

      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><?php echo $lang['wt_submit'];?></a></div>
    </div>
  </form>
</div>
<script type="text/javascript">
$(function(){

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#store_form").valid()){
            $('#province_id').val($("#region").fetch('area_id_1'));
            $('#city_id').val($("#region").fetch('area_id_2'));
            $('#area_id').val($("#region").fetch('area_id_3'));
            $("#store_form").submit();
        }
    });
    $('#region').wt_region({show_deep:3});

    $('#store_form').validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
            area_id: {
                required : true
            }
        },
        messages : {
            area_id: {
                required : '<i class="fa fa-exclamation-bbs"></i>请选择到最后一级地区'
            }
        }
    });
});
</script> 
