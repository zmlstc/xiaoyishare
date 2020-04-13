<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>
<style type="text/css">
.flexigrid .bDiv tr:nth-last-child(2) span.btn ul { bottom: 0; top: auto}
</style>


<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo urlAdminShop('agencies','agencies');?>" title="返回列表"><i class="fa fa-arrow-bbs-o-left"></i></a>
      <div class="subject">
        <h3>“<?php echo $output['agencies_info']['agent_title'];?>” 代理区域</h3>
        <h5>代理商区域信息</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span>
    </div>
    <ul>
      <li></li>
    </ul>
  </div>
  <form method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="submit_type" id="submit_type" value="" />
    <table class="flex-table">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" class="handle" align="center"><?php echo $lang['wt_handle'];?></th>
          <th width="160" align="center">代理区域</th>
        
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <?php foreach($output['list'] as $k => $v){ ?>
        <tr data-id="<?php echo $v['agent_id'];?>">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
            <a class="btn red" href="<?php echo urlAdminShop('agencies','agencies_area_del',array('a_id'=> $v['a_id'],'agent_id'=>$v['agent_id']));?>">删除</a>
           
		   </td>
          <td class="sort"><?php echo $v['area_info'];?></td>
          
          <td></td>
        </tr>
        <?php } ?>
		<tr>
          <td class="no-data" colspan="100">
			<div class="pagination"><?php echo $output['show_page'];?></div></td>
        </tr>
        <?php }else { ?>
        <tr>
          <td class="no-data" colspan="100"><i class="fa fa-exclamation-bbs"></i><?php echo $lang['wt_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_STATIC_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script type="text/javascript">
$(function(){
    $('.flex-table').flexigrid({
        height:'auto',// 高度自动
        usepager: false,// 不翻页
        striped:false,// 不使用斑马线
        resizable: false,// 不调节大小
        title: '代理商区域',// 表格标题
        reload: false,// 不使用刷新
        columnControl: false,// 不使用列控制
       
        buttons : [
                   {display: '<i class="fa fa-plus"></i>新增区域', name : 'add', bclass : 'add', onpress : fg_operation }
               ]
    });

});

function fg_operation(name, bDiv) {
	if (name == 'add') {
        window.location.href = '<?php echo urlAdminShop('agencies','agencies_area_add',array('a_id'=> $v['a_id'],'agent_id'=>$output['agencies_info']['agent_id']));?>';
    }
    if (name == 'del') {
        if ($('.trSelected', bDiv).length == 0) {
            showError('请选择要操作的数据项！');
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_del(itemids);
    } 
}

</script>