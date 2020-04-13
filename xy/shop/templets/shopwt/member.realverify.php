<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['member_index_manage']?></h3>
        <h5><?php echo $lang['member_shop_manage_subhead']?></h5>
      </div> <?php echo $output['top_link'];?>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
    
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: '<?php echo urlAdminShop('member','get_rv_xml');?>',
        colModel : [
            {display: '操作', name : 'operation', width : 120, sortable : false, align: 'center'},
            {display: '会员ID', name : 'member_id', width : 140, sortable : true, align: 'center'},
            {display: '姓名', name : 'truename', width : 150, sortable : true, align: 'left'},
            {display: '身份证号码', name : 'cardid', width : 150, sortable : true, align: 'left'},
			{display: '申请状态', name : 'state', width: 60, sortable : true, align : 'center'}, 
            {display: '申请时间', name : 'addtime_txt', width : 150, sortable : false, align : 'left'}, 
            {display: '审核时间', name : 'verify_time_txt', width : 150, sortable : false, align : 'left'}
            ],
        searchitems : [
            {display: '会员ID', name : 'member_id'}
            ],
        sortname: "member_id",
        sortorder: "desc",
        title: '实名列表'
    });
	
});


function submit_delete(id){
	if (typeof id == 'number') {
    	var id = new Array(id.toString());
	};
	if(confirm('确认删除这 ' + id.length + ' 项吗？')){
		id = id.join(',');
        window.location.href = '<?php echo urlAdminShop('member','rverify_del');?>?member_id='+id;
    }
}

</script> 

