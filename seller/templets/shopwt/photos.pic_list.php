<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="tabmenu">
  <?php include template('layout/submenu'); ?>
  <a id="open_uploader" href="JavaScript:void(0);" class="wtbtn wtbtn-aqua"><i class="icon-cloud-upload"></i><?php echo $lang['album_class_list_img_upload'];?></a>
  <div class="upload-con" id="uploader" style="display: none;">
    <form method="post" action="" id="fileupload" enctype="multipart/form-data">
      <input type="hidden" name="category_id" id="category_id" value="<?php echo $output['class_info']['ac_id']?>">
      <div class="upload-con-div">选择文件：
        <div class="wtsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="file" multiple="multiple"/>
          </span>
          <p><i class="icon-upload-alt"></i><?php echo $lang['album_class_list_img_upload'];?></p>
          </a> </div>
      </div>
      <div wttype="file_msg"></div>
      <div class="upload-pmgressbar" wttype="file_loading"></div>
      <div class="upload-txt"><span><?php echo $lang['album_batch_upload_description'].$output['setting_config']['image_max_filesize'].'KB'.$lang['album_batch_upload_description_1'];?></span> </div>
    </form>
  </div>
</div>
<div id="pictureFolder" class="wtsc-picture-folder">
  <dl class="wtsc-album-intro">
    <dt class="album-name"><?php echo $output['class_info']['ac_name']?></dt>
    <dd class="album-covers">
      <?php if($output['class_info']['ac_cover'] != ''){ ?>
      <img id="aclass_cover" src="<?php echo cthumb($output['class_info']['ac_cover'], 60,$_SESSION['store_id']);?>">
      <?php }else{?>
      <i class="icon-picture"></i>
      <?php }?>
    </dd>
    <dd class="album-info"><?php echo $output['class_info']['ac_des']?></dd>
  </dl>
  <table class="search-form">
    <tbody>
      <tr>
        <th><?php echo $lang['album_plist_batch_processing'];?></th>
        <td><?php if(is_array($output['pic_list']) && !empty($output['pic_list'])){?>
          <a href="JavaScript:void(0);" class="wtbtn-mini" onClick="checkAll()"><i class="icon-check"></i><?php echo $lang['album_plist_check_all'];?></a><a href="JavaScript:void(0);" class="wtbtn-mini" onClick="uncheckAll()"><i class="icon-check-sign"></i><?php echo $lang['album_plist_cancel'];?></a><a href="JavaScript:void(0);" class="wtbtn-mini" onClick="switchAll()"><i class="icon-check-empty"></i><?php echo $lang['album_plist_inverse'];?></a><a href="JavaScript:void(0);" class="wtbtn-mini" onClick="submit_form('del')"><i class="icon-trash"></i><?php echo $lang['album_class_delete'];?></a><a href="JavaScript:void(0);" class="wtbtn-mini" id="img_move"><i class="icon-move"></i><?php echo $lang['album_plist_move_album'];?></a>
          <?php if (!C('oss.open')) { ?>
          <a href="JavaScript:void(0);" class="wtbtn-mini" onClick="submit_form('watermark')"><i class=" icon-paste"></i><?php echo $lang['album_plist_add_watermark'];?></a>
          <?php } ?>
          <dd id="batchClass" style=" display:none;">
            <?php if(!empty($output['class_list'])){?>
            <span><?php echo $lang['album_plist_move_album_change'].$lang['wt_colon'];?></span>
            <select name="cid" id="cid" style="width:100px;">
              <?php foreach ($output['class_list'] as $v){?>
              <option value="<?php echo $v['ac_id']?>" style="width:80px;"><?php echo $v['ac_name']?></option>
              <?php }?>
            </select>
            <input type="button" onClick="submit_form('move')" value="<?php echo $lang['album_plist_move_album_begin'];?>" />
            <?php }else{?>
            <span><?php echo $lang['album_plist_move_album_only_one'];?><a href="JavaScript:void(0);" uri="<?php echo urlSeller('photos','album_add');?>" wt_type="dialog" dialog_title="<?php echo $lang['album_class_add'];?>"><?php echo $lang['album_class_add'];?></a><?php echo $lang['album_plist_move_album_only_two'];?></span>
            <?php }?>
          </dd>
          </dl>
          <?php }?></td>
        <th><?php echo $lang['album_sort'];?></th>
        <td class="w100"><?php if(is_array($output['pic_list']) && !empty($output['pic_list'])){?>
          <form name="select_sort" id="select_sort" action="<?php echo urlSeller('photos','album_pic_list');?>">
           
            <input type="hidden" name="id" value="<?php echo $output['class_info']['ac_id']?>" />
            <select name="sort" id="img_sort">
              <option value="0"  <?php if($_GET['sort'] == '0'){?>selected <?php }?> ><?php echo $lang['album_sort_upload_time_desc'];?></option>
              <option value="1"  <?php if($_GET['sort'] == '1'){?>selected <?php }?> ><?php echo $lang['album_sort_upload_time_asc'];?></option>
              <option value="2"  <?php if($_GET['sort'] == '2'){?>selected <?php }?> ><?php echo $lang['album_sort_img_size_desc'];?></option>
              <option value="3"  <?php if($_GET['sort'] == '3'){?>selected <?php }?> ><?php echo $lang['album_sort_img_siza_asc'];?></option>
              <option value="4"  <?php if($_GET['sort'] == '4'){?>selected <?php }?> ><?php echo $lang['album_sort_img_name_desc'];?></option>
              <option value="5"  <?php if($_GET['sort'] == '5'){?>selected <?php }?> ><?php echo $lang['album_sort_img_name_asc'];?></option>
            </select>
          </form>
          <?php }?></td>
      </tr>
    </tbody>
  </table>
  <?php if(is_array($output['pic_list']) && !empty($output['pic_list'])){?>
  <form name="checkboxform" id="checkboxform" method="POST" action="">
    <div class="wtsc-picture-list">
      <div class="alert alert-info"> <strong><?php echo $lang['album_plist_replace_same_type'];?></strong> </div>
      <ul>
        <?php $ii=0;?>
        <?php foreach($output['pic_list'] as $v){?>
        <?php
        	$curpage = intval($_GET['curpage']) ? intval($_GET['curpage']) : 1;
        	$ii++;
        ?>
        <li>
          <dl>
            <dt>
              <div class="picture"><a href="<?php echo urlSeller('photos','album_pic_info',array('id'=>$v['ap_id'],'class_id'=>$v['ac_id'],'sort'=>$_GET['sort']));?>?curpage=<?php echo ceil((15*($curpage-1)+$ii)/9);?>&start_index=<?php echo ($tmp = (15*($curpage-1)+$ii)%9) ? $tmp-1 :6;?>"> <img id="img_<?php echo $v['ap_id'];?>" src="<?php echo thumb($v, 240);?>"></a></div>
              <input id="C<?php echo $v['ap_id'];?>" name="id[]" value="<?php echo $v['ap_id'];?>" type="checkbox" class="checkbox"/>
              <input id="<?php echo $v['ap_id'];?>" class="editInput1" readonly onDblClick="$(this).unbind();_focus($(this));" value="<?php echo $v['ap_name']?>" title="<?php echo $lang['album_plist_double_click_edit'];?>" style="cursor:pointer;">
              <span onDblClick="_focus($(this).prev());" title="<?php echo $lang['album_plist_double_click_edit'];?>"><i class="icon-pencil"></i></span></dt>
            <dd class="date">
              <p><?php echo $lang['album_plist_upload_time'].$lang['wt_colon'].date("Y-m-d",$v['add_time'])?></p>
              <p><?php echo $lang['album_plist_original_size'].$lang['wt_colon'].$v['apic_spec']?></p>
            </dd>
            <dd class="buttons">
              <div class="upload-btn"><a href="javascript:void(0);"> <span>
                <input type="file" name="file_<?php echo $v['ap_id'];?>" id="file_<?php echo $v['ap_id'];?>" class="input-file" size="1" hidefocus="true" wttype="replace_image" />
                </span>
                <div class="upload-button"><i class="icon-upload-alt"></i><?php echo $lang['album_plist_replace_upload'];?></div>
                <input id="submit_button" style="display:none" type="button" value="<?php echo $lang['store_slide_image_upload'];?>" onClick="submit_form($(this))" />
                </a></div>
              <a href="JavaScript:void(0);" wt_type="dialog" dialog_title="<?php echo $lang['album_plist_move_album'];?>" uri="<?php echo urlSeller('photos','album_pic_move',array('cid'=>$output['class_info']['ac_id'],'id'=>$v['ap_id']));?>"><i class="icon-move"></i><?php echo $lang['album_plist_move_album'];?></a> <a href="JavaScript:void(0);" onclick="cover(<?php echo $v['ap_id'];?>)"><i class="icon-picture"></i><?php echo $lang['album_plist_set_to_cover'];?></a> <a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['album_plist_delete_confirm_message'];?>','<?php echo urlSeller('album','album_pic_del',array('id'=>$v['ap_id']));?>');"><i class="icon-trash"></i><?php echo $lang['album_plist_delete_img'];?></a> </dd>
          </dl>
        </li>
        <?php }?>
      </ul>
    </div>
  </form>
  <div class="pagination"><?php echo $output['show_page']; ?></div>
  <?php }else{?>
  <div class="warning-option"><i class="icon-warning-sign">&nbsp;</i><span><?php echo $lang['no_record'];?></span></div>
  <?php }?>
</div>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<script>
$(function() {
    //鼠标触及区域li改变class
    $(".wtsc-picture-list ul li").hover(function() {
        $(this).addClass("hover");
    }, function() {
        $(this).removeClass("hover");
    });
    //凸显鼠标触及区域、其余区域半透明显示
    $(".wtsc-picture-list > ul > li").jfade({
        start_opacity:"1",
        high_opacity:"1",
        low_opacity:".5",
        timing:"200"
    });

    // 替换图片
    $('input[wttype="replace_image"]').each(function(){
        $(this).fileupload({
            dataType: 'json',
            url: '<?php echo urlSeller('photos','replace_image_upload');?>?id=' + $(this).attr('id'),
            done: function (e,data) {
                var param = data.result;
                if(param.state == 'true'){
                    img_refresh(param.id);
                } else {
                    alert(param.message);
                }
            }
        });
    });


    // ajax 上传图片
    var upload_num = 0; // 上传图片成功数量
    $('#fileupload').fileupload({
        dataType: 'json',
        url: '<?php echo urlSeller('photos','image_upload');?>',
        add: function (e,data) {
        	$.each(data.files, function (index, file) {
                $('<div wttype=' + file.name.replace(/\./g, '_') + '><p>'+ file.name +'</p><p class="loading"></p></div>').appendTo('div[wttype="file_loading"]');
            });
        	data.submit();
        },
        done: function (e,data) {
            var param = data.result;
            $this = $('div[wttype="' + param.origin_file_name.replace(/\./g, '_') + '"]');
            $this.fadeOut(3000, function(){
                $(this).remove();
                if ($('div[wttype="file_loading"]').html() == '') {
                    setTimeout("window.location.reload()", 1000);
                }
            });
            if(param.state == 'true'){
                upload_num++;
                $('div[wttype="file_msg"]').html('<i class="icon-ok-sign">'+'</i>'+'<?php echo $lang['album_upload_complete_one'];?>'+upload_num+'<?php echo $lang['album_upload_complete_two'];?>');

            } else {
                $this.find('.loading').html(param.message).removeClass('loading');
            }
        }
    });
});
// 重新加载图片，替换上传使用
function img_refresh(id){
	$('#img_'+id).attr('src',$('#img_'+id).attr('src')+"?"+100*Math.random());
}

// 全选
function checkAll() {
	$('#batchClass').hide();
	$('input[type="checkbox"]').each(function(){
		$(this).attr('checked',true);
	});
}
// 取消
function uncheckAll() {
	$('#batchClass').hide();
	$('input[type="checkbox"]').each(function(){
		$(this).attr('checked',false);
	});
}
// 反选
function switchAll() {
	$('#batchClass').hide();
	$('input[type="checkbox"]').each(function(){
		$(this).attr('checked',!$(this).attr('checked'));
	});
}

//控制图片名称input焦点可编辑
function _focus(o){
	var name;
        obj = o;
        name=obj.val();
        obj.removeAttr("readonly");
        obj.attr('class','editInput2');
        obj.select();
        obj.blur(function(){
			if(name != obj.val()){
               _save(this);
			}else{
				obj.attr('class','editInput1');
				obj.attr('readonly','readonly');
			}
        });
}
function _save(obj){
		$.post('<?php echo urlSeller('photos','change_pic_name');?>', {id:obj.id,name:obj.value}, function(data) {
			if(data == 'false'){
				showError('<?php echo $lang['wt_common_op_fail'];?>');
			}else{
				showDialog('<?php echo $lang['wt_common_op_succ']?>','succ');
			}
		});
        obj.className = "editInput1";
        obj.readOnly = true;
}
function submit_form(type){
	if(type != 'move'){
		$('#batchClass').hide();
	}
	var id='';
	$('input[type=checkbox]:checked').each(function(){
		if(!isNaN($(this).val())){
			id += $(this).val();
		}
	});
	if(id == ''){
		alert('<?php echo $lang['album_plist_select_img']?>');
		return false;
	}
	if(type=='del'){
		if(!confirm('<?php echo $lang['album_plist_delete_confirm_message'];?>')){
			return false;
		}
	}
	if(type=='move'){
		$('#checkboxform').append('<input type="hidden" name="form_submit" value="ok" /><input type="hidden" name="cid" value="'+$('#cid').val()+'" />');
	}
	$('#checkboxform').attr('action','<?php echo SELLER_SITE_URL?>/index.php/photos/album_pic_'+type);
	ajaxpost('checkboxform', '', '', 'onerror');
}
// 相册封面
function cover(id){
	if($('#aclass_cover').attr('src') != $('#img_'+id).attr('src')){
		ajaxget('<?php echo urlSeller('photos','change_album_cover');?>?id='+id);
	}else{
		showError('<?php echo $lang['album_plist_not_set_same_image'];?>');
	}
}

$(function(){
	$("#img_sort").change(function(){
		$('#select_sort').submit();
	});
	$("#img_move").click(function(){
		if($('#batchClass').css('display') == 'none'){
			$('#batchClass').show();
		}else {
			$('#batchClass').hide();
		}
	});
});
</script>
