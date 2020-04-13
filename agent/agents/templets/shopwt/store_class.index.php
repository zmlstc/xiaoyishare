<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['store_class'];?></h3>
        <h5><?php echo $lang['store_class_subhead'];?></h5>
      </div>
    </div>
  </div>

  <form method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <table class="flex-table">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" align="center" class="handle"><?php echo $lang['wt_handle'];?></th>
          <th width="20" align="center"></th>
          <th width="350" align="left"><?php echo $lang['store_class_name'];?></th>
          <th width="100" align="center">系统最低佣金</th>
          <th width="100" align="center">佣金</th>
          <th width="80" align="center"></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['class_list']) && is_array($output['class_list'])){ ?>
        <?php foreach($output['class_list'] as $k => $v){ ?>
        <tr class="edit">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
          <a class="btn red" href="<?php echo urlAgentAgents('store_class','store_class_edit',array('sc_id'=>$v['sc_id']));?>">编辑佣金</a>
         
          </td>
          <td class="sort"></td>
          <td class="name"><?php echo $output['psclist'][$v['parent_id']]['sc_name'].' -> '.$v['sc_name'];?></td>
          <td><?php echo $v['commis_rate'];?>%</td>
		  <td><?php if(!empty($output['asclist'][$v['sc_id']])){ echo $output['asclist'][$v['sc_id']]['agent_rate'];}else{ echo $v['commis_rate'];}?>%</td>
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

<script>
$(function(){
	$('.flex-table').flexigrid({
		height:'auto',// 高度自动
		usepager: false,// 不翻页
		striped:false,// 不使用斑马线
		resizable: false,// 不调节大小
		//title: '店铺分类列表',// 表格标题
		reload: false,// 不使用刷新
		columnControl: false// 不使用列控制
       
		});
});

</script>