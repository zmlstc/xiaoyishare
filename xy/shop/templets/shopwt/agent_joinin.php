<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>代理商管理</h3>
        <h5>申请的代理商管理</h5>
      </div>
      <?php echo $output['top_link'];?> 
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
      <li></li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: '<?php echo urlAdminShop('agencies','get_joinin_xml');?>',
        colModel : [
            {display: '操作', name : 'operation', width : 60, sortable : false, align: 'center', className: 'handle-s'},
			{display: '注册手机', name : 'agent_phone', width : 80, sortable : false, align: 'left'},
            {display: '申请状态', name : 'joinin_state', width: 60, sortable : true, align : 'center'},                      
            {display: '联系人姓名', name : 'contacts_name', width : 80, sortable : false, align: 'left'},
            {display: '联系人电话', name : 'contacts_phone', width : 100, sortable : false, align: 'left'},
            {display: '公司名称', name : 'company_name', width : 180, sortable : false, align: 'left'},
            {display: '公司地址', name : 'company_province_id', width : 200, sortable : false, align: 'left'},
            {display: '代理区域', name : 'area_info', width : 200, sortable : false, align : 'left'}
            ],
        searchitems : [
            {display: '注册手机', name : 'agent_phone', isdefault: true}
            ],
        sortname: "joinin_state",
        sortorder: "asc",
        title: '代理申请列表'
    });
});


</script> 
