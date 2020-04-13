<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
  <div class="item-title"><a class="back" href="<?php echo urlAdminShop('plat_act','index',array('act_id'=>$output['act_id']));?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>活动管理</h3>
        <h5>商城活动页面管理</h5>
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
  <form method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <table class="flex-table">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" align="center" class="handle"><?php echo $lang['wt_handle'];?></th>
          <th width="100" align="center"><?php echo $lang['wt_sort'];?></th>
          <th width="300" align="left">店铺名称</th>
          <th width="100" align="center"></th>
          <th width="100" align="center"></th>
          <th width="100" align="center"></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <?php foreach($output['list'] as $k => $v){ ?>
        <tr class="edit">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
          <a class="btn red" href="javascript:if(confirm('删除'))window.location = '<?php echo urlAdminShop('plat_act','store_del',array('id'=>$v['id'],'act_id'=>$v['act_id']));?>';"><i class="fa fa-trash-o"></i><?php echo $lang['wt_del'];?></a>
         
		 
          </td>
          <td class="sort"><span wt_type="inline_edit" title="<?php echo $lang['can_edit'];?>" column_id="<?php echo $v['id'];?>" fieldname="gc_sort" class="editable"><?php echo $v['gc_sort'];?></span></td>
          <td class="name"><span   class="node_name "><?php echo $v['store_name'];?></span></td>
          <td></td>
		  <td></td>
		  <td></td>
          <td></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no-data">
          <td colspan="10"><?php echo $lang['wt_no_record'];?></td>
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
		title: '活动赞助商列表',// 表格标题
		reload: false,// 不使用刷新
		columnControl: false,// 不使用列控制
        buttons : [ 
                   {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation }
               ]
		});

    // 修改
    $('span[wt_type="inline_edit"]').inline_edit({w: 'plat_act',t: 'store_ajax'});
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = '<?php echo urlAdminShop('plat_act','store_add',array('act_id'=>$output['act_id']));?>';
    }
}
</script>