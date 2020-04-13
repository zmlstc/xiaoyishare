<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>设施管理</h3>
        <h5>商家店铺设施管理</h5>
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
          <th width="300" align="left">名称</th>
          <th width="150" align="center">分类</th>
          <th>图标</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <?php foreach($output['list'] as $k => $v){ ?>
        <tr class="edit">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
          <a class="btn red" href="javascript:if(confirm('删除设施'))window.location = '<?php echo urlAdminShop('facility','facility_del',array('f_id'=>$v['f_id']));?>';"><i class="fa fa-trash-o"></i><?php echo $lang['wt_del'];?></a>
          <a class="btn blue" href="<?php echo urlAdminShop('facility','facility_edit',array('f_id'=>$v['f_id']));?>"><i class="fa fa-pencil-square-o"></i><?php echo $lang['wt_edit'];?></a>
          </td>
          <td class="sort"><span wt_type="inline_edit" title="<?php echo $lang['can_edit'];?>" column_id="<?php echo $v['f_id'];?>" fieldname="f_sort" class="editable"><?php echo $v['f_sort'];?></span></td>
          <td class="name"><span wt_type="inline_edit" title="名称" column_id="<?php echo $v['f_id'];?>" fieldname="f_name" class="node_name editable"><?php echo $v['f_name'];?></span></td>
          <td><?php echo $output['fc_list'][$v['fc_id']]['fc_name']; ?></td>
          <td><?php if(!empty($v['f_img'])){?><img style="width: 20px;height: 20px;" src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_MOBILE.'/facility/'.$v['f_img'];?>" /><?php } ?></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr >
          <td class="no-data" colspan="100">没有符合条件的记录</td>
        </tr>
        <?php } ?>
      </tbody>
	      <tfoot>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <tr class="tfoot">
          <td colspan="16">
            <div class="pagination"> <?php echo $output['show_page'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
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
		title: '设施列表',// 表格标题
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
        window.location.href = '<?php echo urlAdminShop('facility','facility_add');?>';
    }
}
</script>