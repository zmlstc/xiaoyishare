<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>活动分类</h3>
        <h5>商城活动分类管理设置</h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
      <li>对分类作任何更改后，都需要到 设置 -> 更新缓存 清理店铺分类，新的设置才会生效</li>
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
          <th width="300" align="left">分类名称</th>
          <th width="100" align="center"></th>
          <th width="100" align="center"></th>
          <th width="100" align="center"></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['class_list']) && is_array($output['class_list'])){ ?>
        <?php foreach($output['class_list'] as $k => $v){ ?>
        <tr class="edit">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
          <a class="btn red" href="javascript:if(confirm('<?php echo $lang['del_store_class'];?>'))window.location = '<?php echo urlAdminShop('plat_actclass','class_del',array('gc_id'=>$v['gc_id']));?>';"><i class="fa fa-trash-o"></i><?php echo $lang['wt_del'];?></a>
          <span class="btn"><em><i class="fa fa-cog"></i><?php echo $lang['nc_set'];?><i class="arrow"></i></em>
            <ul>
              <li> <a href="<?php echo urlAdminShop('plat_actclass','class_edit',array('gc_id'=>$v['gc_id']));?>">编辑该分类</a></li>
              
            </ul>
            </span>
		 
          </td>
          <td class="sort"><span wt_type="inline_edit" title="<?php echo $lang['can_edit'];?>" column_id="<?php echo $v['gc_id'];?>" fieldname="gc_sort" class="editable"><?php echo $v['gc_sort'];?></span></td>
          <td class="name"><span wt_type="inline_edit" title="分类名称" column_id="<?php echo $v['gc_id'];?>" fieldname="gc_name" class="node_name editable"><?php echo $v['gc_name'];?></span></td>
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
		title: '活动分类列表',// 表格标题
		reload: false,// 不使用刷新
		columnControl: false,// 不使用列控制
        buttons : [ 
                   {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation }
               ]
		});

    // 修改分佣比例
    $('span[wt_type="inline_edit"]').inline_edit({w: 'plat_actclass',t: 'ajax'});
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = '<?php echo urlAdminShop('plat_actclass','class_add');?>';
    }
}
</script>