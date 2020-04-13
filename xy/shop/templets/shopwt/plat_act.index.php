<?php defined('ShopWT') or exit('Access Denied By ShopWT');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>活动管理</h3>
        <h5>商城活动页面管理</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-bell-o"></i>
      <h4 title="<?php echo $lang['wt_prompts_title'];?>"><?php echo $lang['wt_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['wt_prompts_span'];?>"></span> </div>
    <ul>
      <li>只有关闭或者过期的活动才能删除</li>
      <li>活动列表排序越小越靠前显示</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
  <div class="wtap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
  <div class="wtap-search-bar">
    <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
    <div class="title">
      <h3>高级搜索</h3>
    </div>
    <form method="get" name="formSearch" id="formSearch">
      <input type="hidden" name="showanced" value="1" />
      <div id="searchCon" class="content">
        <div class="layout-box">
          <dl>
            <dt>活动标题</dt>
            <dd>
              <label>
                <input type="text" name="activity_title" class="s-input-txt" placeholder="请输入活动标题关键字" />
              </label>
            </dd>
          </dl>
          <dl>
            <dt>活动状态</dt>
            <dd>
              <label>
                <select name="activity_state" class="s-select">
                  <option value="">-请选择-</option>
                  <option value="1">开启</option>
                  <option value="0">关闭</option>
                </select>
              </label>
            </dd>
          </dl>

            <dl>
              <dt>活动时期筛选</dt>
              <dd>
                <label>
                    <input type="text" name="pdate1" data-dp="1" class="s-input-txt" placeholder="结束时间不晚于" />
                </label>
                <label>
                    <input type="text" name="pdate2" data-dp="1" class="s-input-txt" placeholder="开始时间不早于" />
                </label>
              </dd>
            </dl>

        </div>
      </div>
      <div class="bottom"> <a href="javascript:void(0);" id="wtsubmit" class="wtap-btn wtap-btn-green">提交查询</a> <a href="javascript:void(0);" id="wtreset" class="wtap-btn wtap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['wt_cancel_search'];?></a> </div>
    </form>
  </div>
</div>
<script>
$(function(){
    var flexUrl = '<?php echo urlAdminShop('plat_act','activity_xml');?>';

    $("#flexigrid").flexigrid({
        url: flexUrl,
        colModel: [
            {display: '操作', name: 'operation', width: 150, sortable: false, align: 'center', className: 'handle'},
            {display: '排序', name: 'act_sort', width: 100, sortable: 1, align: 'left'},
            {display: '活动标题', name: 'act_name', width: 350, sortable: false, align: 'left'},
            {display: '图片', name: 'act_image', width: 80, sortable: false, align: 'left'},
            {display: '报名时间', name: 'act_stime', width: 200, sortable: 1, align: 'center'},
            {display: '活动时间', name: 'act_date', width: 120, sortable: 1, align: 'center'},
            {display: '状态', name: 'act_state', width: 80, sortable: false, align: 'center'}
        ],
        buttons: [
            {
                display: '<i class="fa fa-plus"></i>新增活动',
                name: 'add',
                bclass: 'add',
                title: '平台发起新活动',
                onpress: function() {
                    location.href = '<?php echo urlAdminShop('plat_act','new');?>';
                }
            },
            {
                display: '<i class="fa fa-trash"></i>批量删除',
                name: 'del',
                bclass: 'del',
                title: '将选定行数据批量删除',
                onpress: function() {
                    var ids = [];
                    $('.trSelected[data-id]').each(function() {
                        ids.push($(this).attr('data-id'));
                    });
                    if (ids.length < 1 || !confirm('确定删除?')) {
                        return false;
                    }
                    var href = '<?php echo urlAdminShop('plat_act','del');?>?act_id=__IDS__'.replace('__IDS__', ids.join(','));

                    $.getJSON(href, function(d) {
                        if (d && d.result) {
                            $("#flexigrid").flexReload();
                        } else {
                            alert(d && d.message || '操作失败！');
                        }
                    });
                }
            }
        ],
        searchitems: [
            {display: '活动标题', name: 'act_name', isdefault: true}
        ],
        sortname: "act_id",
        sortorder: "desc",
        title: '活动列表'
    });

    // 高级搜索提交
    $('#wtsubmit').click(function(){
        $("#flexigrid").flexOptions({url: flexUrl + '&' + $("#formSearch").serialize(),query:'',qtype:''}).flexReload();
    });

    // 高级搜索重置
    $('#wtreset').click(function(){
        $("#flexigrid").flexOptions({url: flexUrl}).flexReload();
        $("#formSearch")[0].reset();
    });

    $("input[data-dp='1']").datepicker({dateFormat: 'yy-mm-dd'});

});

$('a[data-href]').live('click', function() {
    if ($(this).hasClass('confirm-del-on-click') && !confirm('确定删除?')) {
        return false;
    }

    $.getJSON($(this).attr('data-href'), function(d) {
        if (d && d.result) {
            $("#flexigrid").flexReload();
        } else {
            alert(d && d.message || '操作失败！');
        }
    });
});

$("span[data-live-inline-edit='act_sort']").live('click', function() {
    var $this = $(this);
    var $input = $('<input type="text" style="width:50px;">');
    $input.val(parseInt($this.html()) || 0);
    $this.after($input);
    $this.hide();
    $input.focus();
    $input.change(function() {
        var v2 = parseInt($input.val()) || 0;
        $.get('<?php echo urlAdminShop('plat_act','ajax',array('branch'=>'act_sort'));?>', {
            id: $this.parents('tr').attr('data-id'),
            column: 'act_sort',
            value: v2
        }, function(d) {
            if (d == 'true') {
                $this.html(v2);
            } else {
                alert('操作失败！');
            }
            $input.remove();
            $this.show();
        });
    });
});

$("span[data-live-inline-edit='act_name']").live('click', function() {
    var $this = $(this);
    var $input = $('<input type="text" style="width:333px;">');
    $input.val($this.html());
    $this.after($input);
    $this.hide();
    $input.focus();
    $input.change(function() {
        var v2 = $.trim($input.val());
        if (!v2) {
            alert('请输入标题！');
            $input.focus();
            return false;
        }
        $.get('<?php echo urlAdminShop('plat_act','ajax',array('branch'=>'act_name'));?>', {
            id: $this.parents('tr').attr('data-id'),
            column: 'act_name',
            value: v2
        }, function(d) {
            if (d == 'true') {
                $this.html(v2);
            } else {
                alert('操作失败！');
            }
            $input.remove();
            $this.show();
        });
    });
});

</script>
