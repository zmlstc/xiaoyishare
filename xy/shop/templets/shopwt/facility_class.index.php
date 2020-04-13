<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>设施分类</h3>
        <h5>商家店铺设施分类管理</h5>
      </div><?php echo $output['top_link'];?> 
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
  <form method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <table class="flex-table">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" align="center" class="handle"><?php echo $lang['wt_handle'];?></th>
          <th width="150" align="center">排序</th>
          <th width="300" align="left">分类名称</th>
          <th width="150" align="center"></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['class_list']) && is_array($output['class_list'])){ ?>
        <?php foreach($output['class_list'] as $k => $v){ ?>
        <tr class="edit">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
          <a class="btn red" href="javascript:if(confirm('删除设施分类'))window.location = '<?php echo urlAdminShop('facility','facility_class_del',array('fc_id'=>$v['fc_id']));?>';"><i class="fa fa-trash-o"></i><?php echo $lang['wt_del'];?></a>
          <a class="btn blue" href="<?php echo urlAdminShop('facility','facility_class_edit',array('fc_id'=>$v['fc_id']));?>"><i class="fa fa-pencil-square-o"></i><?php echo $lang['wt_edit'];?></a>
          </td>
          <td class="sort"><span wt_type="inline_edit" title="<?php echo $lang['can_edit'];?>" column_id="<?php echo $v['fc_id'];?>" fieldname="fc_sort" class="editable"><?php echo $v['fc_sort'];?></span></td>
          <td class="name"><span wt_type="inline_edit" title="分类名称" column_id="<?php echo $v['fc_id'];?>" fieldname="fc_name" class="node_name editable"><?php echo $v['fc_name'];?></span></td>
          <td></td>
          <td></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr >
          <td class="no-data" colspan="100">没有符合条件的记录</td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_STATIC_URL;?>/js/jquery.edit.js" charset="utf-8"></script> 
<script>
$(function(){
	$('.flex-table').flexigrid({
		height:'auto',// 高度自动
		usepager: false,// 不翻页
		striped:false,// 不使用斑马线
		resizable: false,// 不调节大小
		title: '设施分类列表',// 表格标题
		reload: false,// 不使用刷新
		columnControl: false,// 不使用列控制
        buttons : [ 
                   {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation }
               ]
		});

    // 修改分佣比例
    $('span[wt_type="inline_edit"]').inline_edit({w: 'facility',t: 'ajax'});
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = '<?php echo urlAdminShop('facility','facility_class_add');?>';
    }
}
</script>