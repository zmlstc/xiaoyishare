<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>地区管理</h3>
        <h5>可对系统内置的地区进行编辑</h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span>
    </div>
    <ul>
      <li>全站所有涉及的地区均来源于此处，强烈建议对此处谨慎操作。</li>
      <li>编辑地区信息后，需手动更新地区缓存(平台  > 设置 > 更新缓存 > 地区)，前台才会生效。</li>
      <li>所属大区为默认的全国性的几大区域，只有省级地区才需要填写大区域，目前全国几大区域有：华北、东北、华东、华南、华中、西南、西北、港澳台、海外</li>
      <li>所在层级为该地区的所在的层级深度，如北京>北京市>朝阳区,其中北京层级为1，北京市层级为2，朝阳区层级为3</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
//定义变量，点击返回上一级、新增分类自动获取当前父类时用到
var his_parent_ids = [0],cur_parent_id = 0;

$(function(){
    $("#flexigrid").flexigrid({
        url: '<?php echo urlAdminSystem('area','get_xml');?>',
        colModel : [
            {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '地区', name : 'area_name', width : 200, sortable : false, align: 'left'},
            {display: '简称', name : 'shortname', width : 100, sortable : false, align: 'left'},
            {display: '所属大区', name : 'area_region', width : 120, sortable : false, align: 'left'},
			{display: '所在层级', name : 'area_deep', width : 100, sortable : false, align : 'left'},
			{display: '上级地区ID', name : 'area_parent_id', width : 140, sortable : false, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-level-up"></i>返回上级地区', name : 'up', bclass : 'up', title : '返回上级地区', onpress : fg_operate }
            ],
        searchitems : [
            {display: '地区', name : 'area_name'}
            ],
        sortname: "",
        sortorder: "",
        rp: 40,
        title: '地区列表'
    });
});
function fg_operate(name, grid) {
    if (name == 'up') {
    	fg_up();
    }
}


function fg_show_children(area_id,parent_id) {
	$("#flexigrid").flexOptions({url: '<?php echo urlAdminSystem('area','get_xml');?>?parent_id='+area_id,query:'',qtype:''}).flexReload();
	his_parent_ids.push(parent_id);
	cur_parent_id = area_id;
}

function fg_up() {
	if (his_parent_ids.length == 0) {
		his_parent_ids.push(0);
	}
	$("#flexigrid").flexOptions({url: '<?php echo urlAdminSystem('area','get_xml');?>?parent_id='+his_parent_ids[his_parent_ids.length-1]}).flexReload();
	cur_parent_id = his_parent_ids[his_parent_ids.length-1];
	his_parent_ids.pop();
}
</script> 
