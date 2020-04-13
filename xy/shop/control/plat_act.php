<?php
/**
 * 平台活动管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class plat_actControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('activity');
    }
    /**
     * 活动列表
     */
    public function indexWt(){
        $this->activityWt();
    }
    /**
     * 活动列表/删除活动
     */
    public function activityWt()
    {
		Tpl::setDirquna('shop');
        Tpl::showpage('plat_act.index');
    }

    /**
     * 活动列表/删除活动XML
     */
    public function activity_xmlWt()
    {
        $condition = array();
		$condition['parent_id'] = 0;

        if ($_REQUEST['showanced']) {
            if (strlen($q = trim((string) $_REQUEST['act_name']))) {
                $condition['act_name'] = $q;
            }
            if (strlen($q = trim((string) $_REQUEST['act_state']))) {
                $condition['act_state'] = (int) $q;
            }

            $pdates = array();
            if (strlen($q = trim((string) $_REQUEST['pdate1'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "plat_act.act_end_date >= {$q}";
            }
            if (strlen($q = trim((string) $_REQUEST['pdate2'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "plat_act.act_start_date <= {$q}";
            }
            if ($pdates) {
                $condition['pdates'] = implode(' or ', $pdates);
            }

        } else {
            if (strlen($q = trim($_REQUEST['query'])) > 0) {
                switch ($_REQUEST['qtype']) {
                    case 'act_title':
                        $condition['act_name'] = $q;
                        break;
                }
            }
        }

        switch ($_REQUEST['sortname']) {
            case 'act_sort':
            case 'act_start_date':
            case 'act_end_date':
                $sort = $_REQUEST['sortname'];
                break;
            default:
                $sort = 'act_id';
                break;
        }
        if ($_REQUEST['sortorder'] != 'asc') {
            $sort .= ' desc';
        }

        $condition['order'] = $sort;

        $page = new Page();
        $page->setEachNum($_REQUEST['rp']);

        $activity = Model('plat_act');
        $list = (array) $activity->getList($condition, $page);

        $data = array();
        $data['now_page'] = $page->get('now_page');
        $data['total_num'] = $page->get('total_num');

        foreach ($list as $val) {
            $o = '';
            if ($val['act_state'] == 0 || $val['act_datetime'] < time()) {
                $o .= '<a class="btn red confirm-del-on-click" href="javascript:;" data-href="'.urlAdminShop('plat_act','del',array('act_id'=>$val['act_id'])).                   
                    '"><i class="fa fa-trash-o"></i>删除</a>';
            }

            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';

            $o .= '<li><a href="'.urlAdminShop('plat_act','edit',array('act_id'=>$val['act_id'])).'">编辑活动</a></li>';
			$o .= '<li><a href="'.urlAdminShop('plat_act','act_sub',array('act_id'=>$val['act_id'])).'">子活动管理</a></li>'; 
			$o .= '<li><a href="'.urlAdminShop('plat_act','act_store',array('act_id'=>$val['act_id'])).'">活动赞助商</a></li>'; 

            $o .= '</ul></span>';


            $i = array();
            $i['operation'] = $o;

            $i['act_sort'] = '<span class="editable" title="可编辑" style="width:50px;" data-live-inline-edit="act_sort">' .
                $val['act_sort'] . '</span>';

            $i['act_name'] = '<span class="editable" title="可编辑" style="width:333px;" data-live-inline-edit="act_name">' .
                $val['act_name'] . '</span>';

            $img = UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$val['act_image'];
            $i['act_image'] = <<<EOB
<a href="javascript:;" class="pic-thumb-tip" onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
<i class='fa fa-picture-o'></i></a>
EOB;

            $i['act_stime'] = date('Y-m-d', $val['act_stime']).'~'.date('Y-m-d', $val['act_etime']);
            $i['act_date'] = date('Y-m-d H:i:s', $val['act_datetime']);

            $i['act_state'] = $val['act_state'] == 1
                ? '<span class="yes"><i class="fa fa-check-bbs"></i>开启</span>'
                : '<span class="no"><i class="fa fa-ban"></i>关闭</span>';

            $data['list'][$val['act_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 新建活动/保存新建活动
     */
    public function newWt(){
        //新建处理
        if($_POST['form_submit'] != 'ok'){
			$gc_list = Model('plat_act_class')->getActClassList(array(), '', 100);
			Tpl::output('gc_list',$gc_list);
			Tpl::setDirquna('shop');
            Tpl::showpage('plat_act.add');
            exit;
        }
        //提交表单
        $obj_validate = new Validate();
        $validate_arr[] = array("input"=>$_POST["act_name"],"require"=>"true","message"=>Language::get('activity_new_title_null'));
        $validate_arr[] = array("input"=>$_POST["act_stime"],"require"=>"true","message"=>Language::get('activity_new_startdate_null'));
        $validate_arr[] = array("input"=>$_POST["act_etime"],"require"=>"true",'validator'=>'Compare','operator'=>'>','to'=>"{$_POST['activity_start_date']}","message"=>Language::get('activity_new_enddate_null'));
        $validate_arr[] = array("input"=>$_POST["act_price"],"require"=>"true","message"=>'费用不能为空');
        //$validate_arr[] = array('input'=>$_POST['act_type'],'require'=>'true','message'=>Language::get('activity_new_type_null'));
        $validate_arr[] = array('input'=>$_FILES['act_image']['name'],'require'=>'true','message'=>Language::get('activity_new_banner_null'));
        $validate_arr[] = array('input'=>$_POST['act_sort'],'require'=>'true','validator'=>'Range','min'=>0,'max'=>255,'message'=>Language::get('activity_new_sort_error'));
        $obj_validate->validateparam = $validate_arr;
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage(Language::get('error').$error,'','','error');
        }
        $upload = new UploadFile();
        $upload->set('default_dir',ATTACH_ACTIVITY);
        $result = $upload->upfile('act_image');
        if(!$result){
            showMessage($upload->error);
        }
        //保存
        $input  = array();
        $input['act_name']    = trim($_POST['act_name']);
        $input['parent_id']       = 0;
        $input['gc_name']     = '';
        $input['gc_id']     = intval($_POST['act_gc']);
        $input['act_image']   = $upload->file_name;
        $input['act_price']    = floatval($_POST['act_price']);
        $input['act_points']    = intval($_POST['act_points']);
        $input['act_storage']    = intval($_POST['act_storage']);
        $input['act_body']     = trim($_POST['act_body']);
        $input['act_sort']     = intval(trim($_POST['act_sort']));
        $input['act_addtime']= time();
        $input['act_datetime']= strtotime(trim($_POST['act_datetime']));
        $input['act_stime']= strtotime(trim($_POST['act_stime']));
        $input['act_etime'] = strtotime(trim($_POST['act_etime']));
        $input['act_state']    = intval($_POST['act_state']);
        $input['jh_province_id']    = intval($_POST['jh_province_id']);
        $input['jh_area_info']    = trim($_POST['jh_region']);
        $input['jh_address']    = trim($_POST['jh_address']);
        $input['jh_city_id']    = intval($_POST['jh_city_id']);
        $input['jh_area_id']    = intval($_POST['jh_area_id']);
        $input['act_province_id']    = intval($_POST['act_province_id']);
        $input['act_area_info']    = trim($_POST['act_region']);
        $input['act_address']    = trim($_POST['act_address']);
        $input['act_city_id']    = intval($_POST['act_city_id']);
        $input['act_area_id']    = intval($_POST['act_area_id']);
        $activity   = Model('plat_act');
        $result = $activity->add($input);
        if($result){
            $this->log(L('wt_add,activity_index').'['.$_POST['act_name'].']',null);
            showMessage(Language::get('wt_common_op_succ'),urlAdminShop('plat_act','activity'));
        }else{
            //添加失败则删除刚刚上传的图片,节省空间资源
            @unlink(BASE_UPLOAD_PATH.DS.ATTACH_ACTIVITY.DS.$upload->file_name);
            showMessage(Language::get('wt_common_op_fail'));
        }
    }

    /**
     * 异步修改
     */
    public function ajaxWt(){
        if(in_array($_GET['branch'],array('act_name','act_sort'))){
            $activity = Model('plat_act');
            $update_array = array();
            switch ($_GET['branch']){
                /**
                 * 活动主题
                 */
                case 'act_name':
                    if(trim($_GET['value'])=='')exit;
                    break;
                /**
                 * 排序
                 */
                case 'act_sort':
                    if(preg_match('/^\d+$/',trim($_GET['value']))<=0 or intval(trim($_GET['value']))<0 or intval(trim($_GET['value']))>255)exit;
                    break;
                default:
                        exit;
            }
            $update_array[$_GET['column']] = trim($_GET['value']);
            if($activity->updates($update_array,intval($_GET['id'])))
            echo 'true';
        }
    }

    /**
     * 删除活动
     */
    public function delWt()
    {
        $activityIds = array();
        foreach (explode(',', (string) $_REQUEST['act_id']) as $i) {
            $activityIds[(int) $i] = null;
        }
        unset($activityIds[0]);
        $activityIds = array_keys($activityIds);

        if (empty($activityIds)) {
            $this->jsonOutput(Language::get('activity_del_choose_activity'));
        }

        try{
            // 删除数据先删除横幅图片，节省空间资源
            foreach ($activityIds as $v) {
                $this->delBanner($v);
            }
        } catch (Exception $e) {
            $this->jsonOutput($e->getMessage());
        }

        $id = implode(",", $activityIds);

      /*   $activity   = Model('plat_act');
        $activity_detail    = Model('activity_detail');
        //获取可以删除的数据
        $condition_arr = array();
        $condition_arr['activity_state'] = '0';//已关闭
        $condition_arr['activity_enddate_greater_or'] = time();//过期
        $condition_arr['activity_id_in'] = $id;
        $activity_list = $activity->getList($condition_arr);
        if (empty($activity_list)){//没有符合条件的活动信息直接返回成功信息
            $this->jsonOutput();
        }
        $id_arr = array();
        foreach ($activity_list as $v){
            $id_arr[] = $v['activity_id'];
        }
        $id_new = "'".implode("','",$id_arr)."'";
        //只有关闭或者过期的活动，能删除
        if($activity_detail->del($id_new)){
            if($activity->del($id_new)){
                $this->log(L('wt_del,activity_index').'[ID:'.$id.']',null);
                $this->jsonOutput();
            }
        }  */

        $this->jsonOutput('操作失败');
    }

    /**
     * 编辑活动/保存编辑活动
     */
    public function editWt(){
        if($_POST['form_submit'] != 'ok'){
            if(empty($_GET['act_id'])){
                showMessage(Language::get('miss_argument'));
            }
            $activity   = Model('plat_act');
            $row    = $activity->getOneById(intval($_GET['act_id']));
            Tpl::output('activity',$row);
			$gc_list = Model('plat_act_class')->getActClassList(array(), '', 100);
			Tpl::output('gc_list',$gc_list);

			Tpl::setDirquna('shop');
            Tpl::showpage('plat_act.edit');
            exit;
        }
		
		 //提交表单
        $obj_validate = new Validate();
        $validate_arr[] = array("input"=>$_POST["act_name"],"require"=>"true","message"=>Language::get('activity_new_title_null'));
        $validate_arr[] = array("input"=>$_POST["act_stime"],"require"=>"true","message"=>Language::get('activity_new_startdate_null'));
        $validate_arr[] = array("input"=>$_POST["act_etime"],"require"=>"true",'validator'=>'Compare','operator'=>'>','to'=>"{$_POST['activity_start_date']}","message"=>Language::get('activity_new_enddate_null'));
        $validate_arr[] = array("input"=>$_POST["act_price"],"require"=>"true","message"=>'费用不能为空');
        //$validate_arr[] = array('input'=>$_POST['act_type'],'require'=>'true','message'=>Language::get('activity_new_type_null'));
        //$validate_arr[] = array('input'=>$_FILES['act_image']['name'],'require'=>'true','message'=>Language::get('activity_new_banner_null'));
        $validate_arr[] = array('input'=>$_POST['act_sort'],'require'=>'true','validator'=>'Range','min'=>0,'max'=>255,'message'=>Language::get('activity_new_sort_error'));
        $obj_validate->validateparam = $validate_arr;
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage(Language::get('error').$error,'','','error');
        }
			
        //构造更新内容
        $input  = array();
        if($_FILES['act_image']['name']!=''){
            $upload = new UploadFile();
            $upload->set('default_dir',ATTACH_ACTIVITY);
            $result = $upload->upfile('act_image');
            if(!$result){
                showMessage($upload->error);
            }
            $input['act_image']   = $upload->file_name;
        }
       
		
        $input['act_name']    = trim($_POST['act_name']);
       
        $input['gc_name']     = '';
        $input['gc_id']     = intval($_POST['act_gc']);
       // $input['act_image']   = $upload->file_name;
        $input['act_price']    = floatval($_POST['act_price']);
        $input['act_points']    = intval($_POST['act_points']);
        $input['act_storage']    = intval($_POST['act_storage']);
        $input['act_body']     = trim($_POST['act_body']);
        $input['act_sort']     = intval(trim($_POST['act_sort']));
        $input['act_addtime']= time();
        $input['act_datetime']= strtotime(trim($_POST['act_datetime']));
        $input['act_stime']= strtotime(trim($_POST['act_stime']));
        $input['act_etime'] = strtotime(trim($_POST['act_etime']));
        $input['act_state']    = intval($_POST['act_state']);
        $input['jh_province_id']    = intval($_POST['jh_province_id']);
        $input['jh_area_info']    = trim($_POST['jh_region']);
        $input['jh_address']    = trim($_POST['jh_address']);
        $input['jh_city_id']    = intval($_POST['jh_city_id']);
        $input['jh_area_id']    = intval($_POST['jh_area_id']);
        $input['act_province_id']    = intval($_POST['act_province_id']);
        $input['act_area_info']    = trim($_POST['act_region']);
        $input['act_address']    = trim($_POST['act_address']);
        $input['act_city_id']    = intval($_POST['act_city_id']);
        $input['act_area_id']    = intval($_POST['act_area_id']);
		
		
	

        $activity   = Model('plat_act');
        $row    = $activity->getOneById(intval($_POST['act_id']));
        $result = $activity->updates($input,intval($_POST['act_id']));
        if($result){
            if($_FILES['act_image']['name']!=''){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_ACTIVITY.DS.$row['act_image']);
            }
            $this->log(L('wt_edit,activity_index').'[ID:'.$_POST['act_id'].']',null);
            showMessage(Language::get('wt_common_save_succ'),urlAdminShop('plat_act','activity'));
        }else{
            if($_FILES['act_image']['name']!=''){
                @unlink(BASE_UPLOAD_PATH.DS.ATTACH_ACTIVITY.DS.$upload->file_name);
            }
            showMessage(Language::get('wt_common_save_fail'));
        }
    }

  
    /**
     * 店铺
     */
    public function act_storeWt(){
        $lang   = Language::getLangContent();
        if(empty($_GET['act_id'])){
            showMessage(Language::get('miss_argument'));
        }

        $list = Model()->table('plat_act_store')->where(array('act_id'=>intval($_GET['act_id'])))->select();
        Tpl::output('list',$list);
		Tpl::output('act_id',intval($_GET['act_id']));
		Tpl::setDirquna('shop');
		
		Tpl::showpage('plat_act_store.index');
		
    }
    /**
     * 店铺添加
     */
    public function store_addWt(){
        $lang   = Language::getLangContent();
        if (chksubmit()){
			if(empty($_POST['act_id'])){
				showMessage(Language::get('miss_argument'));
			}
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["store_id"], "require"=>"true", "message"=>'店铺ID不能为空!'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
				$obj_store = Model('store');
				$store_info = $obj_store->getStoreInfoByID(intval($_POST["store_id"]));
				if (empty($store_info)){
					showMessage('店铺不存在');
				}
                $insert_array = array();
                $insert_array['store_name'] = $store_info['store_name'];
                $insert_array['store_id'] = $store_info['store_id'];
                $insert_array['act_id'] = intval($_POST['act_id']);
                $insert_array['gc_sort'] = intval($_POST['gc_sort']);
                $result = Model()->table('plat_act_store')->insert($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('plat_act','store_add',array('act_id'=>intval($_POST['act_id']))),
                    'msg'=>'继续添加',
                    ),
                    array(
                    'url'=>urlAdminShop('plat_act','act_store',array('act_id'=>intval($_POST['act_id']))),
                    'msg'=>'返回列表',
                    )
                    );
                    //$this->log(L('wt_add,store_class').'['.$_POST['gc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],$url,'html','succ',1,5000);
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        if(empty($_GET['act_id'])||intval($_GET['act_id'])<1){
            showMessage(Language::get('miss_argument'));
        }
		Tpl::output('actid',intval($_GET['act_id']));
		Tpl::setDirquna('shop');
        Tpl::showpage('plat_act_store.add');
    }
    public function checkstoreWt(){
        $store_id = intval($_GET['store_id']);
        if ($store_id<1){
            echo ''; die;
        }
        $obj_store = Model('store');
        $store_info = $obj_store->getStoreInfoByID($store_id);
        if (is_array($store_info) && count($store_info)>0){
            if(strtoupper(CHARSET) == 'GBK'){
                $store_info['store_name'] = Language::getUTF8($store_info['store_name']);
            }
            echo json_encode(array('id'=>$store_info['store_id'],'name'=>$store_info['store_name']));
        }else {
            echo ''; die;
        }
    }
    /**
     * 删除dianpu
     */
    public function store_delWt(){
        $lang   = Language::getLangContent();
        if (intval($_GET['id']) > 0){
            $array = array(intval($_GET['id']));
            $result = Model()->table('plat_act_store')->where(array('id'=>intval($_GET['id'])))->delete();
            if ($result) {
                 showMessage($lang['wt_common_del_succ'],getReferer());
            }
        }
        showMessage($lang['wt_common_del_fail'],urlAdminShop('plat_act','act_store',array('act_id'=>intval($_GET['act_id']))));
    }
    /**
     * ajax操作
     */
    public function store_ajaxWt(){
        $update_array = array();
        switch ($_GET['branch']){
            //分类： 排序 显示 设置
            case 'gc_sort':
                $update_array['gc_sort'] = intval($_GET['value']);
                $result = Model()->table('plat_act_store')->where(array('id'=>intval($_GET['id'])))->update($update_array);
                $return = $result ? true : false;
                break;
        }
        exit(json_encode(array('result'=>$return)));
    }

    public function act_subWt()
    {
        if(empty($_GET['act_id'])||intval($_GET['act_id'])<1){
            showMessage(Language::get('miss_argument'));
        }
		Tpl::output('act_id',intval($_GET['act_id']));
		Tpl::setDirquna('shop');
        Tpl::showpage('plat_act_sub.index');
    }
    public function act_sub_xmlWt()
    {
        $condition = array();
		$condition['parent_id'] = intval($_GET['act_id']);

        if ($_REQUEST['showanced']) {
            if (strlen($q = trim((string) $_REQUEST['act_name']))) {
                $condition['act_name'] = $q;
            }
            if (strlen($q = trim((string) $_REQUEST['act_state']))) {
                $condition['act_state'] = (int) $q;
            }

            $pdates = array();
            if (strlen($q = trim((string) $_REQUEST['pdate1'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "plat_act.act_end_date >= {$q}";
            }
            if (strlen($q = trim((string) $_REQUEST['pdate2'])) && ($q = strtotime($q . ' 00:00:00'))) {
                $pdates[] = "plat_act.act_start_date <= {$q}";
            }
            if ($pdates) {
                $condition['pdates'] = implode(' or ', $pdates);
            }

        } else {
            if (strlen($q = trim($_REQUEST['query'])) > 0) {
                switch ($_REQUEST['qtype']) {
                    case 'act_title':
                        $condition['act_name'] = $q;
                        break;
                }
            }
        }

        switch ($_REQUEST['sortname']) {
            case 'act_sort':
            case 'act_start_date':
            case 'act_end_date':
                $sort = $_REQUEST['sortname'];
                break;
            default:
                $sort = 'act_id';
                break;
        }
        if ($_REQUEST['sortorder'] != 'asc') {
            $sort .= ' desc';
        }

        $condition['order'] = $sort;

        $page = new Page();
        $page->setEachNum($_REQUEST['rp']);

        $activity = Model('plat_act');
        $list = (array) $activity->getList($condition, $page);

        $data = array();
        $data['now_page'] = $page->get('now_page');
        $data['total_num'] = $page->get('total_num');

        foreach ($list as $val) {
            $o = '';
            if ($val['act_state'] == 0 || $val['act_datetime'] < time()) {
                $o .= '<a class="btn red confirm-del-on-click" href="javascript:;" data-href="'.urlAdminShop('plat_act','del',array('act_id'=>$val['act_id'])).                   
                    '"><i class="fa fa-trash-o"></i>删除</a>';
            }

            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';

            $o .= '<li><a href="'.urlAdminShop('plat_act','edit',array('act_id'=>$val['act_id'])).'">编辑活动</a></li>';
			
            $o .= '</ul></span>';


            $i = array();
            $i['operation'] = $o;

            $i['act_sort'] = '<span class="editable" title="可编辑" style="width:50px;" data-live-inline-edit="act_sort">' .
                $val['act_sort'] . '</span>';

            $i['act_name'] = '<span class="editable" title="可编辑" style="width:333px;" data-live-inline-edit="act_name">' .
                $val['act_name'] . '</span>';

            $img = UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$val['act_image'];
            $i['act_image'] = <<<EOB
<a href="javascript:;" class="pic-thumb-tip" onMouseOut="toolTip()" onMouseOver="toolTip('<img src=\'{$img}\'>')">
<i class='fa fa-picture-o'></i></a>
EOB;

            $i['act_stime'] = date('Y-m-d', $val['act_stime']).'~'.date('Y-m-d', $val['act_etime']);
            $i['act_date'] = date('Y-m-d H:i:s', $val['act_datetime']);

            $i['act_state'] = $val['act_state'] == 1
                ? '<span class="yes"><i class="fa fa-check-bbs"></i>开启</span>'
                : '<span class="no"><i class="fa fa-ban"></i>关闭</span>';

            $data['list'][$val['act_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }
    /**
     * 新建子活动
     */
    public function new2Wt(){
        //新建处理
        if($_POST['form_submit'] != 'ok'){
			if(empty($_GET['act_id'])||intval($_GET['act_id'])<1){
				showMessage(Language::get('miss_argument'));
			}
			Tpl::output('act_id',intval($_GET['act_id']));
			$gc_list = Model('plat_act_class')->getActClassList(array(), '', 100);
			Tpl::output('gc_list',$gc_list);
			Tpl::setDirquna('shop');
            Tpl::showpage('plat_act_sub.add');
            exit;
        }
		if(empty($_POST['act_id'])||intval($_POST['act_id'])<1){
			showMessage(Language::get('miss_argument'));
		}
        //提交表单
        $obj_validate = new Validate();
        $validate_arr[] = array("input"=>$_POST["act_name"],"require"=>"true","message"=>Language::get('activity_new_title_null'));
        $validate_arr[] = array("input"=>$_POST["act_stime"],"require"=>"true","message"=>Language::get('activity_new_startdate_null'));
        $validate_arr[] = array("input"=>$_POST["act_etime"],"require"=>"true",'validator'=>'Compare','operator'=>'>','to'=>"{$_POST['activity_start_date']}","message"=>Language::get('activity_new_enddate_null'));
        $validate_arr[] = array("input"=>$_POST["act_price"],"require"=>"true","message"=>'费用不能为空');
        //$validate_arr[] = array('input'=>$_POST['act_type'],'require'=>'true','message'=>Language::get('activity_new_type_null'));
        $validate_arr[] = array('input'=>$_FILES['act_image']['name'],'require'=>'true','message'=>Language::get('activity_new_banner_null'));
        $validate_arr[] = array('input'=>$_POST['act_sort'],'require'=>'true','validator'=>'Range','min'=>0,'max'=>255,'message'=>Language::get('activity_new_sort_error'));
        $obj_validate->validateparam = $validate_arr;
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage(Language::get('error').$error,'','','error');
        }
        $upload = new UploadFile();
        $upload->set('default_dir',ATTACH_ACTIVITY);
        $result = $upload->upfile('act_image');
        if(!$result){
            showMessage($upload->error);
        }
        //保存
        $input  = array();
        $input['act_name']    = trim($_POST['act_name']);
        $input['parent_id']       = intval($_POST['act_id']);
        $input['gc_name']     = '';
        $input['gc_id']     = intval($_POST['act_gc']);
        $input['act_image']   = $upload->file_name;
        $input['act_price']    = floatval($_POST['act_price']);
        $input['act_points']    = intval($_POST['act_points']);
        $input['act_storage']    = intval($_POST['act_storage']);
        $input['act_body']     = trim($_POST['act_body']);
        $input['act_sort']     = intval(trim($_POST['act_sort']));
        $input['act_addtime']= time();
        $input['act_datetime']= strtotime(trim($_POST['act_datetime']));
        $input['act_stime']= strtotime(trim($_POST['act_stime']));
        $input['act_etime'] = strtotime(trim($_POST['act_etime']));
        $input['act_state']    = intval($_POST['act_state']);
        $input['jh_province_id']    = intval($_POST['jh_province_id']);
        $input['jh_area_info']    = trim($_POST['jh_region']);
        $input['jh_address']    = trim($_POST['jh_address']);
        $input['jh_city_id']    = intval($_POST['jh_city_id']);
        $input['jh_area_id']    = intval($_POST['jh_area_id']);
        $input['act_province_id']    = intval($_POST['act_province_id']);
        $input['act_area_info']    = trim($_POST['act_region']);
        $input['act_address']    = trim($_POST['act_address']);
        $input['act_city_id']    = intval($_POST['act_city_id']);
        $input['act_area_id']    = intval($_POST['act_area_id']);
        $activity   = Model('plat_act');
        $result = $activity->add($input);
        if($result){
            $this->log(L('wt_add,activity_index').'['.$_POST['act_name'].']',null);
            showMessage(Language::get('wt_common_op_succ'),urlAdminShop('plat_act','act_sub',array('act_id'=>intval($_POST['act_id']))));
        }else{
            //添加失败则删除刚刚上传的图片,节省空间资源
            @unlink(BASE_UPLOAD_PATH.DS.ATTACH_ACTIVITY.DS.$upload->file_name);
            showMessage(Language::get('wt_common_op_fail'));
        }
    }




	
	
	
	
	
	
	
	










  /**
     * 活动细节列表
     */
    public function detailWt()
    {
        $states = array(
            L('activity_detail_index_to_audit'),
            L('activity_detail_index_passed'),
            L('activity_detail_index_unpassed'),
        );
        Tpl::output('states', $states);

        $activity_detail = Model('activity')->getOneById($_REQUEST['id']);
        Tpl::output('activity_detail', $activity_detail);
		Tpl::setDirquna('shop');
        Tpl::showpage('activity_detail.index');
    }

    /**
     * 活动细节列表XML
     */
    public function detail_xmlWt()
    {
        $condition = array();

        if ($_REQUEST['showanced']) {
            if (strlen($q = trim((string) $_REQUEST['store_name']))) {
                $condition['store_name'] = $q;
            }
            if (strlen($q = trim((string) $_REQUEST['item_name']))) {
                $condition['item_name'] = $q;
            }
            if (strlen($q = trim((string) $_REQUEST['activity_detail_state']))) {
                $condition['activity_detail_state'] = (int) $q;
            }
        } else {
            if (strlen($q = trim($_REQUEST['query'])) > 0) {
                switch ($_REQUEST['qtype']) {
                    case 'store_name':
                        $condition['store_name'] = $q;
                        break;
                    case 'item_name':
                        $condition['item_name'] = $q;
                        break;
                }
            }
        }

        switch ($_REQUEST['sortname']) {
            case 'activity_detail_sort':
            case 'activity_detail_state':
                $sort = 'activity_detail.' . $_REQUEST['sortname'];
                break;
            default:
                $sort = 'activity_detail.activity_detail_id';
                break;
        }
        if ($_REQUEST['sortorder'] != 'asc') {
            $sort .= ' desc';
        }

        $condition['activity_id'] = (int) $_GET['id'];
        $condition['order'] = $sort;

        $page= new Page();
        $page->setEachNum($_REQUEST['rp']);

        $activitydetail_model = Model('activity_detail');
        $list = (array) $activitydetail_model->getList($condition, $page);

        $data = array();
        $data['now_page'] = $page->get('now_page');
        $data['total_num'] = $page->get('total_num');

        $states = array(
            L('activity_detail_index_to_audit'),
            L('activity_detail_index_passed'),
            L('activity_detail_index_unpassed'),
        );

        foreach ($list as $val) {
            $o = '<a class="btn green" href="' .
                urlShop('goods', 'index', array('goods_id' => $val['item_id'])) .
                '"><i class="fa fa-list-alt"></i>查看</a>';

            $o .= '<span class="btn"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em><ul>';

            if ($val['activity_detail_state'] != 1) {
                $o .= '<li><a class="confirm-on-click" href="javascript:;" data-href="'.urlAdminShop('activity','deal',array('state'=>'1','activity_detail_id'=>$val['activity_detail_id'])).
                    '">通过</a></li>';
            }

            if ($val['activity_detail_state'] != 2) {
                $o .= '<li><a class="confirm-on-click" href="javascript:;" data-href="'.urlAdminShop('activity','deal',array('state'=>'2','activity_detail_id'=>$val['activity_detail_id'])).
                    '">拒绝</a></li>';
            }

            if ($val['activity_detail_state'] != 1) {
                $o .= '<li><a class="confirm-on-click" href="javascript:;" data-href="'.urlAdminShop('activity','del_detail',array('activity_detail_id'=>$val['activity_detail_id'])).
                    '">删除</a></li>';
            }

            $o .= '</ul></span>';

            $i = array();
            $i['operation'] = $o;

            $i['activity_detail_sort'] = '<span class="editable" title="可编辑" style="width:50px;" data-live-inline-edit="activity_detail_sort">' .
                $val['activity_detail_sort'] . '</span>';

            $i['item_name'] = $val['item_name'];

            $i['store_name'] = '<a target="_blank" href="' .
                urlShop('show_store', 'index', array('store_id' => $val['store_id'])) .
                '">' .
                $val['store_name'] .
                '</a>';

            $i['activity_detail_state_text'] = $states[(int) $val['activity_detail_state']];

            $data['list'][$val['activity_detail_id']] = $i;
        }

        echo Tpl::flexigridXML($data);
        exit;
    }

    /**
     * 活动内容处理
     */
    public function dealWt()
    {
        $ids = array();
        foreach (explode(',', (string) $_REQUEST['activity_detail_id']) as $i) {
            $ids[(int) $i] = null;
        }
        unset($ids[0]);
        $ids = array_keys($ids);

        if (empty($ids)) {
            showMessage(Language::get('activity_detail_del_choose_detail'));
        }

        // 获取id
        $id = implode(',', $ids);

        //创建活动内容对象
        $activity_detail    = Model('activity_detail');
        if($activity_detail->updates(array('activity_detail_state'=>intval($_GET['state'])),$id)){
            $this->log(L('wt_edit,activity_index').'[ID:'.$id.']',null);

            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 删除活动内容
     */
    public function del_detailWt()
    {
        $ids = array();
        foreach (explode(',', (string) $_REQUEST['activity_detail_id']) as $i) {
            $ids[(int) $i] = null;
        }
        unset($ids[0]);
        $ids = array_keys($ids);

        if (empty($ids)) {
            showMessage(Language::get('activity_detail_del_choose_detail'));
        }

        // 获取id
        $id = implode(',', $ids);

        $activity_detail    = Model('activity_detail');
        //条件
        $condition_arr = array();
        $condition_arr['activity_detail_id_in'] = $id;
        $condition_arr['activity_detail_state_in'] = "'0','2'";//未审核和已拒绝
        if($activity_detail->delList($condition_arr)){
            $this->log(L('wt_del,activity_index_content').'[ID:'.$id.']',null);

            $this->jsonOutput();
        } else {
            $this->jsonOutput('操作失败');
        }
    }

    /**
     * 根据活动编号删除横幅图片
     *
     * @param int $id
     */
    private function delBanner($id){
        $activity   = Model('plat_act');
        $row    = $activity->getOneById($id);
        //删除图片文件
        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_ACTIVITY.DS.$row['act_image']);
    }
}
