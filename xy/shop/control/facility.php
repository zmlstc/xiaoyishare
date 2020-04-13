<?php
/**
 * 设施分类管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class facilityControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('store_class');
    }
	private $_links = array(
        array('url'=>'facility/facility_list','text'=>'管理'),
        array('url'=>'facility/facility_class','text'=>'分类')
    );

    public function indexWt() {
        $this->facility_listWt();
    }

    /**
     * 分类
     */
    public function facility_classWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('facility_class');

        //删除
        if (chksubmit()){
            if (!empty($_POST['check_fc_id']) && is_array($_POST['check_fc_id']) ){
                $result = $model_class->delFacilityClass(array('fc_id'=>array('in',$_POST['check_fc_id'])));
                if ($result) {
                    $this->log('删除设施分类[ID:'.implode(',',$_POST['check_fc_id']).']',1);
                    showMessage($lang['wt_common_del_succ']);
                }
            }
            showMessage($lang['wt_common_del_fail']);
        }

        $class_list = $model_class->getFacilityClassList(array());
        Tpl::output('class_list',$class_list);
		 //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'facility_class'));
		Tpl::setDirquna('shop');
        Tpl::showpage('facility_class.index');
    }

    /**
     * 分类添加
     */
    public function facility_class_addWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('facility_class');
        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["fc_name"], "require"=>"true", "message"=>'分类名称不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['fc_name'] = $_POST['fc_name'];
                $insert_array['fc_sort'] = intval($_POST['fc_sort']);
                $result = $model_class->addFacilityClass($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('facility','facility_class_add'),
                    'msg'=>'继续添加分类',
                    ),
                    array(
                    'url'=>urlAdminShop('facility','facility_class'),
                    'msg'=>'设施分类列表',
                    )
                    );
                    $this->log('添加设施分类['.$_POST['fc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],$url,'html','succ',1,5000);
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }
		 //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'facility_class'));
		Tpl::setDirquna('shop');
        Tpl::showpage('facility_class.add');
    }

    /**
     * 编辑
     */
    public function facility_class_editWt(){
        $lang   = Language::getLangContent();

        $model_class = Model('facility_class');

        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["fc_name"], "require"=>"true", "message"=>'分类名称不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update_array = array();
                $update_array['fc_name'] = $_POST['fc_name'];
                $update_array['fc_sort'] = intval($_POST['fc_sort']);
                $result = $model_class->editFacilityClass($update_array,array('fc_id'=>intval($_POST['fc_id'])));
                if ($result){
                    $this->log('编辑设施分类['.$_POST['fc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],urlAdminShop('facility','facility_class'));
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        $class_array = $model_class->getFacilityClassInfo(array('fc_id'=>intval($_GET['fc_id'])));
        if (empty($class_array)){
            showMessage($lang['illegal_parameter']);
        }

        Tpl::output('class_array',$class_array);
		 //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'facility_class'));
		Tpl::setDirquna('shop');
        Tpl::showpage('facility_class.edit');
    }

    /**
     * 删除分类
     */
    public function facility_class_delWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('facility_class');
        if (intval($_GET['fc_id']) > 0){
            $array = array(intval($_GET['fc_id']));
            $result = $model_class->delFacilityClass(array('fc_id'=>intval($_GET['fc_id'])));
            if ($result) {
                 $this->log('删除设施分类[ID:'.$_GET['fc_id'].']',1);
                 showMessage($lang['wt_common_del_succ'],getReferer());
            }
        }
        showMessage($lang['wt_common_del_fail'],urlAdminShop('facility','facility_class'));
    }

    /**
     * ajax操作
     */
    public function ajaxWt(){
        $model_class = Model('facility_class');
        $update_array = array();
        switch ($_GET['branch']){
            //分类：添加、修改操作中 检测类别名称是否有重复
            case 'fc_name':
                $condition = array();
                $condition['fc_name'] = $_GET['value'];
                $condition['fc_id'] = array('neq',intval($_GET['fc_id']));
                $class_list = $model_class->getFacilityClassList($condition);
                if (empty($class_list)){
                    $update_array['fc_name'] = $_GET['value'];
                    $update = $model_class->editFacilityClass($update_array,array('fc_id'=>intval($_GET['id'])));
                    $return = $update ? true : false;
                } else {
                    $return = false;
                }
                break;
            //分类： 排序 显示 设置
            case 'fc_sort':
                $model_class = Model('facility_class');
                $update_array['fc_sort'] = intval($_GET['value']);
                $result = $model_class->editFacilityClass($update_array,array('fc_id'=>intval($_GET['id'])));
                $return = $result ? true : false;
                break;
            //设施： 排序 显示 设置
            case 'f_sort':
                $model_facility = Model('facility');
                $update_array['f_sort'] = intval($_GET['value']);
                $result = $model_facility->editFacility($update_array,array('f_id'=>intval($_GET['id'])));
                $return = $result ? true : false;
                break;
            //设施：添加、修改操作中 检测类别名称是否有重复
            case 'f_name':
                $model_facility = Model('facility');
                $condition = array();
                $condition['f_name'] = $_GET['value'];
                $condition['f_id'] = array('neq',intval($_GET['f_id']));
                $class_list = $model_facility->getFacilityList($condition);
                if (empty($class_list)){
                    $update_array['f_name'] = $_GET['value'];
                    $update = $model_facility->editFacility($update_array,array('f_id'=>intval($_GET['id'])));
                    $return = $update ? true : false;
                } else {
                    $return = false;
                }
                break;
        }
        exit(json_encode(array('result'=>$return)));
    }
    
    /**
     * 验证分类名称
     */
    public function ajax_check_nameWt(){
        $model_class = Model('facility_class');
        $condition['fc_name'] = $_GET['fc_name'];
        $condition['fc_id'] = array('neq',intval($_GET['fc_id']));
        $class_list = $model_class->getFacilityClassList($condition);
        $return = empty($class_list) ? 'true' : 'false';
        echo $return;
    }

	/**
     * 列表
     */
    public function facility_listWt(){
        $lang   = Language::getLangContent();
        $model_facility = Model('facility');

        //删除
        if (chksubmit()){
            if (!empty($_POST['check_f_id']) && is_array($_POST['check_f_id']) ){
                $result = $model_facility->delFacility(array('f_id'=>array('in',$_POST['check_f_id'])));
                if ($result) {
                    $this->log('删除设施[ID:'.implode(',',$_POST['check_f_id']).']',1);
                    showMessage($lang['wt_common_del_succ']);
                }
            }
            showMessage($lang['wt_common_del_fail']);
        }

        $list = $model_facility->getFacilityList(array(),12);		
        Tpl::output('show_page', $model_facility->showpage());
        Tpl::output('list',$list);
		
		$model_class = Model('facility_class');
        $class_list = $model_class->getFacilityClassList(array());
		$fc_list = array();
		if(!empty($class_list)&&is_array($class_list)){
			foreach($class_list as $value){
				$fc_list[$value['fc_id']]=$value;
			}
			
		}
        Tpl::output('fc_list',$fc_list);
		
		
		 //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'facility_list'));
		Tpl::setDirquna('shop');
        Tpl::showpage('facility.index');
    }
	
	/**
     * 添加
     */
    public function facility_addWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('facility');
        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["f_name"], "require"=>"true", "message"=>'名称不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
				$upload     = new UploadFile();
				
                $insert_array = array();
                $insert_array['f_name'] = $_POST['f_name'];
                $insert_array['f_sort'] = intval($_POST['f_sort']);
                $insert_array['fc_id']   = intval($_POST['fc_id']);
				if($_FILES['f_img']['name'] != ''){
                    $upload->set('default_dir',ATTACH_MOBILE.'/facility');
                    $result = $upload->upfile('f_img');
                    if (!$result){
                        showMessage($upload->error,'','','error');
                    }
                    $insert_array['f_img'] = $upload->file_name;
                }
				else{
					 showMessage('图标不能为空','','','error');
				}
                $result = $model_class->addFacility($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('facility','facility_add'),
                    'msg'=>'继续添加',
                    ),
                    array(
                    'url'=>urlAdminShop('facility','facility_list'),
                    'msg'=>'设施列表',
                    )
                    );
                    $this->log('添加设施['.$_POST['f_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],$url,'html','succ',1,5000);
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }
		$model_class = Model('facility_class');
        $class_list = $model_class->getFacilityClassList(array());
        Tpl::output('fc_list',$class_list);
		 //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'facility_list'));
		Tpl::setDirquna('shop');
        Tpl::showpage('facility.add');
    }
	/**
     * 编辑
     */
    public function facility_editWt(){
        $lang   = Language::getLangContent();

        $model_class = Model('facility');

        if (chksubmit()){//echo json_encode($_FILES);exit;
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
				array("input"=>$_POST["f_name"], "require"=>"true", "message"=>'名称不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
				$upload     = new UploadFile();
                $update_array = array();
                $update_array['f_name'] = $_POST['f_name'];
                $update_array['f_sort'] = intval($_POST['f_sort']);
                $update_array['fc_id']   = intval($_POST['fc_id']);
				if($_FILES['f_img']['name'] != ''){
                    $upload->set('default_dir',ATTACH_MOBILE.'/facility');
                    $result = $upload->upfile('f_img');
                    if (!$result){
                        showMessage($upload->error,'','','error');
                    }
                    $update_array['f_img'] = $upload->file_name;
                }
                $result = $model_class->editFacility($update_array,array('f_id'=>intval($_POST['f_id'])));
                if ($result){
                    $this->log('编辑设施['.$_POST['f_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],urlAdminShop('facility','facility_list'));
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        $class_array = $model_class->getFacilityInfo(array('f_id'=>intval($_GET['f_id'])));
        if (empty($class_array)){
            showMessage($lang['illegal_parameter']);
        }
        Tpl::output('class_array',$class_array);
		
		$model_class = Model('facility_class');
        $class_list = $model_class->getFacilityClassList(array());
        Tpl::output('fc_list',$class_list);
		
		 //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'facility_list'));
		
		Tpl::setDirquna('shop');
        Tpl::showpage('facility.edit');
    }

    /**
     * 删除
     */
    public function facility_delWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('facility');
        if (intval($_GET['f_id']) > 0){
            $array = array(intval($_GET['f_id']));
            $result = $model_class->delFacility(array('f_id'=>intval($_GET['f_id'])));
            if ($result) {
                 $this->log('删除设施[ID:'.$_GET['f_id'].']',1);
                 showMessage($lang['wt_common_del_succ'],getReferer());
            }
        }
        showMessage($lang['wt_common_del_fail'],urlAdminShop('facility','facility_list'));
    }

    
    /**
     * 验证名称
     */
    public function ajax_cf_nameWt(){
        $model_facility = Model('facility');
        $condition['f_name'] = $_GET['f_name'];
        $condition['f_id'] = array('neq',intval($_GET['f_id']));
        $facility_list = $model_facility->getFacilityList($condition);
        $return = empty($facility_list) ? 'true' : 'false';
        echo $return;
    }


	
	
}
