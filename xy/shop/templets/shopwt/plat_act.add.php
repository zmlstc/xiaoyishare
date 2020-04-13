<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <a class="back" href="<?php echo urlAdminShop('plat_act','activity');?>" title="返回活动列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['activity_index_manage'];?> - <?php echo $lang['wt_new'];?></a></h3>
        <h5>商城活动页面管理</h5>
      </div>
    </div>
  </div>
  <form id="add_form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="wtap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="act_name"><em>*</em><?php echo $lang['activity_index_title'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_name" name="act_name" class="input-txt" >
          <span class="err"></span>
          <p class="notic">
            <?php //echo $lang['activity_new_title_tip'];?>
          </p>
        </dd>
      </dl>
      <dl class="row" >
        <dt class="tit">
          <label><em>*</em>活动分类</label>
        </dt>
        <dd class="opt">
          <select name="act_gc">
            <option value="0">选择分类</option>
			<?php if(!empty($output['gc_list'])&&is_array($output['gc_list'])){?>
			<?php foreach($output['gc_list'] as $v){?>
            <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
			<?php } ?>
			<?php } ?>
            </optgroup>
          </select>
          <span class="err"></span>
          <p class="notic"><?php echo $lang['activity_new_type_tip'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>活动费用</label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_price" name="act_price" class="input-txt" value="0" />元
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>活动获得积分</label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_points" name="act_points" class="input-txt" value="0" />积分
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>活动人数</label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_storage" name="act_storage" class="input-txt" value="10" />人
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>活动时间</label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_datetime" name="act_datetime" class="input-txt" />
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>报名开始时间</label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_stime" name="act_stime" class="input-txt" />
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>报名结束时间</label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_etime" name="act_etime" class="input-txt"/>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>图片</label>
        </dt>
        <dd class="opt">
          <div class="input-file-show"><span class="type-file-box">
            <input type="file" class="type-file-file" id="act_image" name="act_image" size="30" hidefocus="true"  wt_type="upload_act_image" title="点击按钮选择文件并提交表单后上传生效">
            </span></div>
          <span class="err"></span>
          <p class="notic">支持jpg、jpeg、gif、png格式</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="act_body"><?php echo $lang['activity_new_desc'];?></label>
        </dt>
        <dd class="opt">
          <textarea name="act_body" id="act_body" rows="10" class="tarea"></textarea>
          <span class="err"></span>
          <p class="notic">&nbsp;</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="store_province_id">集合地点</label>
        </dt>
        <dd class="opt">
          <input type="hidden" name="jh_region" id="jh_region" value="<?php echo $output['store_array']['area_info']; ?>" />
          <input type="hidden" value="<?php echo $output['store_array']['province_id']; ?>" name="jh_province_id" id="jh_province_id">
          <input type="hidden" value="<?php echo $output['store_array']['city_id']; ?>" name="jh_city_id" id="sjh_city_id">
          <input type="hidden" value="<?php echo $output['store_array']['area_id']; ?>" name="jh_area_id" id="jh_area_id">
		  <input type="text" id="jh_address" name="jh_address" class="input-txt" value="">
          <span class="err"></span>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="store_province_id">活动地点</label>
        </dt>
        <dd class="opt">
          <input type="hidden" name="act_region" id="act_region" value="<?php echo $output['store_array']['area_info']; ?>" />
          <input type="hidden" value="<?php echo $output['store_array']['province_id']; ?>" name="act_province_id" id="act_province_id">
          <input type="hidden" value="<?php echo $output['store_array']['city_id']; ?>" name="act_city_id" id="act_city_id">
          <input type="hidden" value="<?php echo $output['store_array']['area_id']; ?>" name="act_area_id" id="act_area_id">
		  <input type="text" id="act_address" name="act_address" class="input-txt" value="">
          <span class="err"></span>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="act_sort"><em>*</em><?php echo $lang['wt_sort'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" id="act_sort" name="act_sort" class="input-txt" value="0">
          <span class="err"></span>
          <p class="notic"><?php echo $lang['activity_new_sort_tip1'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="act_sort"><?php echo $lang['activity_openstate'];?></label>
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="act_state1" class="cb-enable selected" ><?php echo $lang['activity_openstate_open'];?></label>
            <label for="act_state0" class="cb-disable"><?php echo $lang['activity_openstate_close'];?></label>
            <input id="act_state1" name="act_state" checked="checked" value="1" type="radio">
            <input id="act_state0" name="act_state" value="0" type="radio">
          </div>
          <p class="notic"></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="wtap-btn-big wtap-btn-green" id="submitBtn"><span><?php echo $lang['wt_submit'];?></span></a></div>
    </div>
  </form>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_SITE_URL;?>/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.css"  />
<script src="<?php echo STATIC_SITE_URL;?>/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#add_form").valid()){
		$('#jh_province_id').val($("#jh_region").fetch('area_id_1'));
		$('#jh_city_id').val($("#jh_region").fetch('area_id_2'));
		$('#jh_area_id').val($("#jh_region").fetch('area_id_3'));
		$('#act_province_id').val($("#act_region").fetch('area_id_1'));
		$('#act_city_id').val($("#act_region").fetch('area_id_2'));
		$('#act_area_id').val($("#act_region").fetch('area_id_3'));
		$("#add_form").submit();
	}
	});
});
$(document).ready(function(){
	$("#act_stime").datepicker({dateFormat: 'yy-mm-dd'});
	$("#act_etime").datepicker({dateFormat: 'yy-mm-dd'});
	$("#act_datetime").datetimepicker({controlType: 'select'});
    $("#jh_region").wt_region();
    $("#act_region").wt_region();
	
    jQuery.validator.methods.greaterThanDate = function(value, element, param) {
        var date1 = new Date(Date.parse(param.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 < date2;
    };
    jQuery.validator.methods.lessThanDate = function(value, element, param) {
        var date1 = new Date(Date.parse(param.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 > date2;
    };
    jQuery.validator.methods.greaterThanStartDate = function(value, element) {
        var start_date = $("#act_stime").val();
        var date1 = new Date(Date.parse(start_date.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 < date2;
    };
	
	$("#add_form").validate({
		errorPlacement: function(error, element){
			var error_td = element.parents('dl').find('span.err');
            error_td.append(error);
        },
        rules : {
        	act_name: {
        		required : true
        	},
			act_gc: {
				required : true
			},
			act_price: {
                required: true,
				min: 0
			},
            act_points: {
                required: true,
                digits: true,
                min: 0
            },
            act_storage: {
                required: true,
                digits: true,
                min: 2
            },
        	act_datetime: {
        		required : true,
				date      : false
        	},
        	act_stime: {
        		required : true,
				date      : false
        	},
        	act_etime: {
        		required : true,
				greaterThanStartDate : true
        	},
        	act_image: {
        		required: true,
				accept : 'png|jpe?g|gif'
			},
        	act_sort: {
        		required : true,
        		min:0,
        		max:255
        	}
        },
        messages : {
        	act_name: {
        		required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['activity_new_title_null'];?>'
        	},
			act_gc: {
				required : '<i class="icon-exclamation-sign"></i>请选择分类'
			},
			act_price: {
                required : '<i class="icon-exclamation-sign"></i>费用不能为空',
                min: '<i class="icon-exclamation-sign"></i>最小为0'
			},
            act_points: {
                required : '<i class="icon-exclamation-sign"></i>积分不能为空',
                digits: '<i class="icon-exclamation-sign"></i>积分必须为数字',
                min: '<i class="icon-exclamation-sign"></i>积分最小为0'
            },
            act_storage: {
                required : '<i class="icon-exclamation-sign"></i>人数不能为空',
                digits: '<i class="icon-exclamation-sign"></i>人数必须为数字',
                min: '<i class="icon-exclamation-sign"></i>人数不能小于2'
            },
        	act_datetime: {
        		required : '<i class="fa fa-exclamation-bbs"></i>活动时间不能为空'
        	},
        	act_stime: {
        		required : '<i class="fa fa-exclamation-bbs"></i>报名开始时间不能为空'
        	},
        	act_etime: {
        		required : '<i class="fa fa-exclamation-bbs"></i>报名结束时间不能为空',
                greaterThanStartDate : '<i class="icon-exclamation-sign"></i>结束时间必须大于开始时间'
        	},
			act_image: {
        		required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['activity_new_banner_null'];?>',
				accept   : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['activity_new_ing_wrong'];?>'
			},
        	act_sort: {
        		required : '<i class="fa fa-exclamation-bbs"></i><?php echo $lang['activity_new_sort_null'];?>',
        		min:'<i class="fa fa-exclamation-bbs"></i><?php echo $lang['activity_new_sort_minerror'];?>',
        		max:'<i class="fa fa-exclamation-bbs"></i><?php echo $lang['activity_new_sort_maxerror'];?>'
        	}
        }
	});
});

$(function(){
// 模拟活动页面横幅Banner上传input type='file'样式
	var textButton="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='选择上传...' class='type-file-button' />"
    $(textButton).insertBefore("#act_image");
    $("#act_image").change(function(){
	$("#textfield1").val($("#act_image").val());
    });
});
</script>