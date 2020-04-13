<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>意见反馈</h3>
        <h5>意见反馈类型管理</h5>
      </div>
    <?php echo $output['top_link'];?> </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
      <li></li>
    </ul>
  </div>
  <form method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <table class="flex-table">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" align="center" class="handle"><?php echo $lang['wt_handle'];?></th>
          <th width="350" align="left">名称</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="treet1">
        <?php if(!empty($output['class_list']) && is_array($output['class_list'])){ ?>
        <?php foreach($output['class_list'] as $k => $v){ ?>
        <tr >
          <td class="sign">
            <img fieldid="<?php echo $v['f_id'];?>" status="close" src="<?php echo ADMIN_TEMPLATES_URL;?>/images/tv-item.gif">
           </td>
          <td class="handle">
            <a href="javascript:if(confirm('确定要删除分类吗？'))window.location = '<?php echo urlAdminMobile('mb_feedback','fbclass_del',array('f_id'=>$v['f_id']));?>';" class="btn red"><i class="fa fa-trash-o"></i><?php echo $lang['wt_del'];?></a>
            
            <span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em>
            <ul><li><a href="<?php echo urlAdminMobile('mb_feedback','fbclass_edit',array('f_id'=>$v['f_id']));?>">编辑分类</a></li>
            
            </ul>
            </span>
            </td>
          <td class="name"><?php echo $v['f_name'];?></td>
          <td></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr>
          <td class="no-data" colspan="100"><i class="fa fa-exclamation-triangle"></i><?php echo $lang['wt_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript">
$(function(){
	$('.flex-table').flexigrid({
		height:'auto',// 高度自动
		usepager: false,// 不翻页
		striped:false,// 不使用斑马线
		resizable: false,// 不调节大小
		title: '分类列表',// 表格标题
		reload: false,// 不使用刷新
		columnControl: false,// 不使用列控制
        buttons : [
                   {display: '<i class="fa fa-plus"></i>新增分类', name : 'add', bclass : 'add', title : '新增分类', onpress : fg_operation }
               ]
	});

    $('img[wt_type="flex"]').toggle(
        function(){
            $('tr[wttype="' + $(this).attr('fieldid') + '"]').show();
            $(this).attr('src', '<?php echo ADMIN_TEMPLATES_URL;?>/images/tv-collapsable.gif');
        },function(){
            $('tr[wttype="' + $(this).attr('fieldid') + '"]').hide();
            $(this).attr('src', '<?php echo ADMIN_TEMPLATES_URL;?>/images/tv-expandable.gif');
        }
    );
});
function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = '<?php echo urlAdminMobile('mb_feedback','fbclass_add');?>';
    }
}
</script> 
<script type="text/javascript" src="<?php echo ADMIN_STATIC_URL;?>/js/jquery.edit.js" charset="utf-8"></script> 
