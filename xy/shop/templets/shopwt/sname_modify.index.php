<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>店铺名称申请</h3>
        <h5>店铺名称修改申请管理</h5>
      </div>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
      <li>点击审核按钮可以对店铺名称申请进行审核，点击查看按钮可以查看信息。</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: '<?php echo urlAdminShop('store','get_sname_xml');?>',
        colModel : [
            {display: '操作', name : 'operation', width : 60, sortable : false, align: 'center', className: 'handle-s'},
			{display: '申请新店铺名称', name : 'store_name', width : 200, sortable : false, align: 'left'},
            {display: '旧店铺名称', name : 'store_name_old', width : 200, sortable : false, align: 'left'},            
            {display: '申请状态', name : 'state', width: 60, sortable : true, align : 'center'},
            {display: '提交时间', name : 'addtime', width: 150, sortable : true, align : 'center'},                        
            {display: '审核时间', name : 'verify_time', width : 150, sortable : false, align: 'left'},
            {display: '本年修改次数', name : 'curr_num', width : 100, sortable : false, align: 'left'}
            ],
        searchitems : [
            {display: '店铺名称', name : 'store_name', isdefault: true}
           
            ],
        sortname: "id",
        sortorder: "asc",
        title: '店铺名申请列表'
    });
});


</script> 
