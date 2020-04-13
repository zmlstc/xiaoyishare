<?php defined('ShopWT') or exit('Access Invalid!');?> 
<style>
.public {
    /*width: 768px;*/
    width: 960px;
    margin: 0 auto;
    padding: 30px 0;
    overflow: hidden;
}
.information {
    overflow: hidden;
    width: 100%;
    padding-bottom: 50px;
}

.individual {
    width: 620px;
    float: left;
    /*padding-left: 20px;*/
}

.information .info table {
    width: 100%;
    border-collapse: collapse;
}

.wrap_bottom {
    width: 806px;
    height: 2px;
    overflow: hidden;
    background: #E9E9E9;
    position: absolute;
    bottom: -3px;
}

.eject_btn { width: 137px; height: 32px; line-height: 32px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/btn.gif) 0 -198px; position: absolute; top: 10px; right: 20px; z-index: 3; }
.eject_btn b { display: block; width: 97px; height: 32px; padding-left: 40px; color: #3e3e3c; font-size: 14px; cursor: pointer; }

.eject_btn .ico1 { background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 15px -656px; }
.eject_btn .ico2 { background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 15px -188px; }
.eject_btn .ico3 { background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 12px -943px; }

.eject_btn_two { width: 137px; height: 32px; line-height: 32px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/btn.gif) 0 -198px;}

.eject_pos1 { position: absolute; top: 10px; right: 20px; z-index: 3; }
.eject_pos2 { position: absolute; top: 10px; right: 170px; z-index: 3; }
.eject_pos3 {  top: 10px; left:0; z-index: 3; float:left;}
.eject_pos4 { position: absolute; top: 10px; right: 130px; z-index: 3; }

.eject_btn_two b { display: block; height: 32px; color: #3e3e3c; font-size: 14px; cursor: pointer; }
.eject_btn_two .ico1 { width: 107px; padding-left: 30px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 15px -1330px; }
.eject_btn_two .ico2 { width: 97px; padding-left: 40px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 15px -1282px; }
.eject_btn_two .ico3 { width: 87px; padding-left: 50px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 22px -1416px; }
.eject_btn_two .ico4 { width: 102px; padding-left: 35px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 12px -1710px; }
.eject_btn .ico5 { width: 102px; padding-left: 35px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 15px -656px; }

.eject_btn_three { width: 97px; height: 32px; line-height: 32px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/btn.gif) 0 -880px; }
.eject_btn_three b { display: block; height: 32px; color: #3e3e3c; font-size: 14px; cursor: pointer; }
.eject_btn_three .ico3 { padding-left: 40px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 22px -1330px; }
.eject_btn_three .ico4 { padding-left: 40px; background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 22px -1374px; }


.clear { clear: both; height: 0;}
.align1 { text-align: left; }
.align2 { text-align: center; }
.align3 { vertical-align: top; padding-top: 15px; }
.align4 { text-align: right; padding-right: 30px; }

.width1 { width: 30px; }
.width2 { width: 80px; }
.width3 { width: 170px; }
.width4 { width: 60px; }
.width5 { width: 180px; }
.width6 { width: 600px; }
.width7 { width: 300px; }
.width8 { width: 140px; }
.width9 { width: 400px; }
.width10 { width: 270px; }
.width11 { width: 200px; }
.width12 { width: 430px; }
.width13 { width: 120px; }
.width14 { width: 210px; }
.width15 { width: 50px; }
.width16 { width: 460px; }
.width17 { width: 556px; }

.table table {
    width: 100%;
    border-collapse: collapse;
}

.table .line_bold {
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/line_bold.gif) repeat-x 0 bottom;
}
.table th {
    font-weight: normal;
    height: 32px;
}
.public .gray th {
    color: #919191;
}

.public .gray th {
    color: #919191;
}

#treet1 {
background-color: white;
}


.table .last_line td {
    border-bottom: 0px;
}
.table .line td {
    border-top: 1px solid #E2E2E2;
    border-bottom: 1px solid #E2E2E2;
}
.table td {
    padding-top: 10px;
    padding-bottom: 10px;
}

.padding5 {
    padding-left: 71px;
}
.table .line_bold {
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/line_bold.gif) repeat-x 0 bottom;
}

.right_ico {
    display: block;
    width: 10px;
    height: 10px;
    overflow: hidden;
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 0 -1215px;
    cursor: pointer;
}

.wrong_ico {
    display: block;
    width: 10px;
    height: 10px;
    overflow: hidden;
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 0 -1814px;
    cursor: pointer;
}

table .add1_ico {
    float: left;
    padding-left: 23px;
    color: #919191;
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 10px -1180px;
}

.table .edit1 {
    float: left;
    padding-left: 23px;
    color: #919191;
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 10px -729px;
}

.table .delete {
    float: left;
    padding-left: 25px;
    color: #919191;
    background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/member/ico.gif) no-repeat 10px -633px;
}

.individual {
    width: 620px;
    float: left;
    /*padding-left: 20px;*/
}
.information .info th {
    font-weight: normal;
    text-align: left;
    color: #666;
    padding-right: 20px;
    height: 40px;
}


.biz_pub_btn, .biz_pub_btn:hover {
margin-right: 10px;
background: url(<?php echo STATIC_SITE_URL;?>/keyword/images/pub_btn.png) no-repeat;
width: 190px;
height: 30px;
display: inline-block;
line-height: 30px;
text-align: center;
color: #fff;
font-weight: bolder;
text-decoration: none;
border: 0px;
}
</style>
<script type="text/javascript">
$(function(){
    $('*[ectype="dialog"]').click(function(){
        var id = $(this).attr('dialog_id');
        var title = $(this).attr('dialog_title') ? $(this).attr('dialog_title') : '';
        var url = $(this).attr('uri');
        var width = $(this).attr('dialog_width');
        ajax_form(id, title, url, width);
        return false;
    });
});

/* 显示Ajax表单 */
function ajax_form(id, title, url, width)
{
    if (!width)
    {
        width = 400;
    }
    var d = DialogManager.create(id);
    d.setTitle(title);
    d.setContents('ajax', url);
    d.setWidth(width);
    d.show('center');

    return d;
}

function js_success(dialog_id)
{
    DialogManager.close(dialog_id);
    var url = window.location.href;
    url =  url.indexOf('#') > 0 ? url.replace(/#/g, '') : url;
    window.location.replace(url);
}

function js_fail(str)
{
    $('#warning').html('<label class="error">' + str + '</label>');
    $('#warning').show();
}

function drop_confirm(msg, url){
    if(confirm(msg)){
        window.location = url;
    }
}
</script>

<script charset="utf-8" type="text/javascript" src="<?php echo SHOP_STATIC_SITE_URL;?>/js/jqtreetable.js" ></script>
<script charset="utf-8" type="text/javascript" src="<?php echo STATIC_SITE_URL;?>/js/utils.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<link rel="stylesheet" type="text/css" href="<?php echo SELLER_TEMPLATES_URL;?>/css/jqtreetable.css" />

<script type="text/javascript">
//<!CDATA[
$(function()
{
    var map = <?php echo $output['map'];?>;
    var path = "<?php echo STATIC_SITE_URL;?>/keyword/images/";
    if (map.length > 0)
    {
        var option = {openImg: path + "menutree/tv-collapsable.gif", shutImg: path + "menutree/tv-expandable.gif", leafImg: path + "menutree/tv-item.gif", lastOpenImg: path + "menutree/tv-collapsable-last.gif", lastShutImg: path + "menutree/tv-expandable-last.gif", lastLeafImg: path + "menutree/tv-item-last.gif", vertLineImg: path + "menutree/vertline.gif", blankImg: path + "menutree/blank.gif", collapse: false, column: 1, striped: false, highlight: false, state:false};
        $("#treet1").jqTreeTable(map, option);
    }
    var t = new EditableTable($('#my_category'));
});
//]]>
</script>



<div class="blank"></div>
<div class="wtsc-form-default">
    <div class="wrap">
                
                <div class="public">
                <form method="post" enctype="multipart/form-data" id="profile_form" action="<?php echo urlSeller('wechat_menu'); ?>">
                    <div class="information">
                        <div class="info individual">
                            <table border="0" style="border:0px solid gray">
                                 <tr>
                                    <th style="text-align:right;width:90px;">1:</th>
                                    <td><b style="color:red;">该功能只有服务号才能使用。</b></td>
                                </tr>
                                <tr>
                                    <th style="text-align:right;width:90px;">2:</th>
                                    <td>先填写微信AppId和微信AppSecret（这个2个值需要到微信公众平台获取）</td>
                                </tr>
                                <tr>
                                    <th style="text-align:right;width:90px;">3:</th>
                                    <td>按钮数最多只能创建3个，子按钮数最多创建5个，否则创建失败!</td>
                                </tr>
                                <tr>
                                    <th style="text-align:right;width:90px;">4:</th>
                                    <td>关键词需于《关键词自动回复》里的关键词对应,KEY值用字母或数字组成</td>
                                </tr>

                                <tr>
                                    <th style="text-align:right;"><font style="color:red;">*</font>微信AppId:</th>
                                    <td><input type="text" style="width:260px;" class="text width_normal" name="appid" value="<?php echo $output['info']['appid'];?>" /></td>
                                </tr>
                                 <tr>
                                    <th style="text-align:right;"><font style="color:red;">*</font>微信AppSecret:</th>
                                    <td><input type="text" style="width:260px;" class="text width_normal" name="appsecret" value="<?php echo $output['info']['appsecret'];?>" /></td>
                                </tr>
 
                                    <th></th>
                                    <td><input type="submit" class="biz_pub_btn" value="保存修改" /></td>
                                </tr>
                        </table>    
                        </div>
                    </div>
                </form>
                </div>
      
            </div>
            
             <div class="wrap">
                <div class="eject_btn_two eject_pos3" title="{$lang.gcategory_add}" style="left:20px;right:0;">
                    <b class="ico3" ectype="dialog" dialog_title="新增分类" dialog_id="my_category_add" dialog_width="480" uri="<?php echo urlSeller('wechat_menu', 'add'); ?>">新增分类</b>
                </div>
                <br/><br/>
                
                
            <div class="public table" style="float:left;padding-top:0;">
                <table id="my_category">
                    <?php if($output['gcategories']){ ?>
                    <tr class="line_bold" >
                        <th class="width1"></th>
                        <th class="align1" colspan="4"></th>
                    </tr>
                    <tr class="gray" ectype="table_header">
                      <th></th>
                        <th class="align1" coltype="editable" column="cate_name" checker="check_required" inputwidth="50%">分类名称</th>
                        <th class="width15" coltype="editable" column="sort_order" checker="check_max" inputwidth="30px">排序</th>
                        <th class="width15" coltype="switchable" column="if_show" checker="" onclass="right_ico" offclass="wrong_ico">显示</th>
                        <th class="width3">操作</th>
                    </tr>
                    <?php } ?>
                <?php if($output['gcategories']){
                ?>  
                <tbody id="treet1">
                <?php } ?>
                <?php if(is_array($output['gcategories']) && !empty($output['gcategories'])){ 
                      $count = count($output['gcategories']) - 1;
                ?>
                <?php foreach($output['gcategories'] as $key => $gcate){ ?>
                <tr class="line<?php if($key == $count){ ?> last_line<?php } ?>" ectype="table_item" idvalue="<?php echo $gcate['cate_id'];?>">
                    <td class="align2"><input type="checkbox" class="checkitem" value="<?php echo $gcate['cate_id'];?>" /></td>
                    <td class="width7" style="width:245px;">&nbsp;&nbsp;<span ectype="editobj"><?php echo $gcate['cate_name'];?></span></td>
                    <td class="align2"><span ectype="editobj"><?php echo $gcate['sort_order'];?></span></td>
                    <td style="padding-left:30px;">
                        <p class="padding2"><span <?php if($gcate['if_show']){ ?>class="right_ico" status="on"<?php }else{ ?>class="wrong_ico" status="off" <?php } ?>></span></p><!--ectype="editobj"-->
                        </td>
                    <td class="padding5">
                        <?php if($gcate['layer'] < 2){ ?>
                         <a href="javascript:void(0);" ectype="dialog" dialog_width="500" dialog_title="新增下级" dialog_id="my_category_add" uri="<?php echo urlSeller('wechat_menu','add',array('pid'=>$gcate['cate_id']));?>" class="add1_ico">新增下级</a>
                        <?php } ?>
                        <a href="javascript:void(0);" ectype="dialog" dialog_width="480" dialog_title="编辑" dialog_id="my_category_edit" uri="<?php echo urlSeller('wechat_menu','edit',array('id'=>$gcate['cate_id']));?>" class="edit1">编辑</a>  <a href="javascript:drop_confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗', '<?php echo urlSeller('wechat_menu','drop',array('id'=>$gcate['cate_id']));?>');" class="delete">删除</a>

                        </td>
                </tr>
                <?php } ?>
                <?php } ?>
                <?php if($output['gcategories']){ ?>  
                </tbody>
                <?php } ?>
                <?php if($output['gcategories']){ ?>  
                <tr class="line_bold line_bold_bottom">
                    <td colspan="5">&nbsp;</td>
                </tr>
                <tr>
                
                </tr>
                <?php } ?>
                </table>
            </div>         
                <div class="information">
                        <div class="info individual">
                            <table>

                                 <tr>
                                    <td >
                                    <a href="<?php echo urlSeller('wechat_menu','creat_menu');?>"  onclick="return confirm('是否生成自定义菜单？');" ><input type="button" class="biz_pub_btn" value="生成菜单" /></a>
                                    <a href="<?php echo urlSeller('wechat_menu','delete_menu');?>"  onclick="return confirm('是否删除已有的自定义菜单？');" ><input type="button" class="biz_pub_btn" value="删除自定义菜单" /></a>
                                    </td>
                                </tr>
                        </table>    
                        </div>
                    </div>   
            <!--<div class="wrap_bottom"></div>-->
        </div>
        <iframe name="pop_warning" style="display:none"></iframe>
</div>
<div class="blank"></div>