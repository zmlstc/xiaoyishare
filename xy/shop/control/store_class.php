<?php
/**
 * 店铺分类管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class store_classControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('store_class');
    }

    public function indexWt() {
        $this->store_classWt();
    }

    /**
     * 店铺分类
     */
    public function store_classWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('store_class');

        //删除
        if (chksubmit()){
            if (!empty($_POST['check_sc_id']) && is_array($_POST['check_sc_id']) ){
                $result = $model_class->delStoreClass(array('sc_id'=>array('in',$_POST['check_sc_id'])));
                if ($result) {
                    $this->log(L('wt_del,store_class').'[ID:'.implode(',',$_POST['check_sc_id']).']',1);
                    showMessage($lang['wt_common_del_succ']);
                }
            }
            showMessage($lang['wt_common_del_fail']);
        }
		$parentid=0;
		if(isset($_GET['pid'])&&intval($_GET['pid'])>0){
			$parentid= intval($_GET['pid']);
			$class_array = $model_class->getStoreClassInfo(array('sc_id'=>$parentid));
			if (empty($class_array)){
				$parentid=0;
			}
			Tpl::output('cInfo',$class_array);
		}

        $store_class_list = $model_class->getStoreClassList(array('parent_id'=>$parentid));
        Tpl::output('class_list',$store_class_list);
		Tpl::setDirquna('shop');
		if($parentid>0){
			Tpl::showpage('store_class_child.index');
		}else{
			Tpl::showpage('store_class.index');
		}
    }
	


    /**
     * 商品分类添加
     */
    public function store_class_addWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('store_class');
        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["sc_name"], "require"=>"true", "message"=>$lang['store_class_name_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['sc_name'] = $_POST['sc_name'];
                $insert_array['sc_sort'] = intval($_POST['sc_sort']);
                $insert_array['commis_rate'] = 0;//floatval($_POST['commis_rate']);                
				$insert_array['facility_ids'] = '';//implode(",",$_POST['fc_id']);
                $result = $model_class->addStoreClass($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('store_class','store_class_add'),
                    'msg'=>$lang['continue_add_store_class'],
                    ),
                    array(
                    'url'=>urlAdminShop('store_class','store_class'),
                    'msg'=>$lang['back_store_class_list'],
                    )
                    );
                    $this->log(L('wt_add,store_class').'['.$_POST['sc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],$url,'html','succ',1,5000);
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }
      /*   $model_facility = Model('facility_class');
		$facility_list = $model_facility->getFacilityClassList(array());		
        Tpl::output('f_list',$facility_list); */
		
		Tpl::setDirquna('shop');
        Tpl::showpage('store_class.add');
    }
	
    /**
     * 商品二级分类添加
     */
    public function store_childclass_addWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('store_class');
        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["sc_name"], "require"=>"true", "message"=>$lang['store_class_name_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert = array();
                $insert['sc_name'] = $_POST['sc_name'];
                $insert['sc_sort'] = intval($_POST['sc_sort']);
                $insert['commis_rate'] = floatval($_POST['commis_rate']);
				$insert['facility_ids'] = implode(",",$_POST['fc_id']);
                $insert['parent_id'] = intval($_POST['parent_id']);
                $insert['commis_rate_invstore'] = floatval($_POST['commis_rate_invstore']);
                $insert['commis_rate_invmember'] = floatval($_POST['commis_rate_invmember']);
                $insert['commis_rate_system'] = floatval($_POST['commis_rate_system']);				
                $insert['commis_rate_points'] = floatval($_POST['commis_rate_points']);
				
				if($insert['commis_rate']>80){
					 showMessage('平台总佣金比例不能超过80%');
				}
				if(($insert['commis_rate_invstore']+$insert['commis_rate_invmember']+$insert['commis_rate_system']+$insert['commis_rate_points'])!=100){
					  showMessage('分红比例总和必须等于100%');
				}
                $result = $model_class->addStoreClass($insert);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('store_class','store_childclass_add',array('pid'=>intval($_POST['parent_id']))),
                    'msg'=>$lang['continue_add_store_class'],
                    ),
                    array(
                    'url'=>urlAdminShop('store_class','store_class'),
                    'msg'=>$lang['back_store_class_list'],
                    )
                    );
                    $this->log(L('wt_add,store_class').'['.$_POST['sc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],$url,'html','succ',1,5000);
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }
		$pid = intval($_GET['pid']);
		$class_array = $model_class->getStoreClassInfo(array('sc_id'=>$pid));
		if (empty($class_array)){
			showMessage($lang['illegal_parameter']);
		}
		Tpl::output('pclass',$class_array);
        $model_facility = Model('facility_class');
		$facility_list = $model_facility->getFacilityClassList(array());		
        Tpl::output('f_list',$facility_list);
		
		Tpl::setDirquna('shop');
        Tpl::showpage('store_class_child.add');
    }

    /**
     * 编辑
     */
    public function store_class_editWt(){
        $lang   = Language::getLangContent();

        $model_class = Model('store_class');

        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["sc_name"], "require"=>"true", "message"=>$lang['store_class_name_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update = array();
                $update['sc_name'] = $_POST['sc_name'];
                $update['sc_sort'] = intval($_POST['sc_sort']);
                $update['commis_rate'] 		= 0;		
				$update['facility_ids'] = '';
                $result = $model_class->editStoreClass($update,array('sc_id'=>intval($_POST['sc_id'])));
                if ($result){
                    $this->log(L('wt_edit,store_class').'['.$_POST['sc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],urlAdminShop('store_class','store_class'));
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        $class_array = $model_class->getStoreClassInfo(array('sc_id'=>intval($_GET['sc_id'])));
        if (empty($class_array)){
            showMessage($lang['illegal_parameter']);
        }
        Tpl::output('class_array',$class_array);
		/* Tpl::output('facility_ids',explode(",", $class_array['facility_ids']));
        $model_facility = Model('facility_class');
		$facility_list = $model_facility->getFacilityClassList(array());		
        Tpl::output('f_list',$facility_list); */

		Tpl::setDirquna('shop');
        Tpl::showpage('store_class.edit');
    }

	/**
     * 编辑2级分类
     */
    public function store_childclass_editWt(){
        $lang   = Language::getLangContent();

        $model_class = Model('store_class');

        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["sc_name"], "require"=>"true", "message"=>$lang['store_class_name_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update = array();
                $update['sc_name'] = $_POST['sc_name'];
                $update['sc_sort'] = intval($_POST['sc_sort']);
                $update['commis_rate'] 		= floatval($_POST['commis_rate']);				
				$update['facility_ids'] = implode(",",$_POST['fc_id']);
				$update['commis_rate_invstore'] = floatval($_POST['commis_rate_invstore']);
                $update['commis_rate_invmember'] = floatval($_POST['commis_rate_invmember']);
                $update['commis_rate_system'] = floatval($_POST['commis_rate_system']);				
                $update['commis_rate_points'] = floatval($_POST['commis_rate_points']);
				
				if($update['commis_rate']>80){
					 showMessage('平台总佣金比例不能超过80%');
				}
				if(($update['commis_rate_invstore']+$update['commis_rate_invmember']+$update['commis_rate_system']+$update['commis_rate_points'])!=100){
					 showMessage('分红比例总和必须等于100%');
				}
                $result = $model_class->editStoreClass($update,array('sc_id'=>intval($_POST['sc_id'])));
                if ($result){
					Model('agent_store_class')->edit(array('agent_rate'=>$update['commis_rate']),array('sc_id'=>intval($_GET['sc_id']),'agent_rate'=>array('lt',$update['commis_rate'])));
                    $this->log(L('wt_edit,store_class').'['.$_POST['sc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],urlAdminShop('store_class','store_class'));
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        $class_array = $model_class->getStoreClassInfo(array('sc_id'=>intval($_GET['sc_id'])));
        if (empty($class_array)){
            showMessage($lang['illegal_parameter']);
        }
        Tpl::output('class_array',$class_array);
		//上级分类
		$pclass = $model_class->getStoreClassInfo(array('sc_id'=>intval($class_array['parent_id'])));
		Tpl::output('pclass',$pclass);
		
		Tpl::output('facility_ids',explode(",", $class_array['facility_ids']));
        $model_facility = Model('facility_class');
		$facility_list = $model_facility->getFacilityClassList(array());		
        Tpl::output('f_list',$facility_list);

		Tpl::setDirquna('shop');
        Tpl::showpage('store_class_child.edit');
    }
	
	
    /**
     * 删除分类
     */
    public function store_class_delWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('store_class');
        if (intval($_GET['sc_id']) > 0){
            $array = array(intval($_GET['sc_id']));
            $result = $model_class->delStoreClass(array('sc_id'=>intval($_GET['sc_id'])));
            if ($result) {
				Model('agent_store_class')->del(array('sc_id'=>intval($_GET['sc_id'])));
                 $this->log(L('wt_del,store_class').'[ID:'.$_GET['sc_id'].']',1);
                 showMessage($lang['wt_common_del_succ'],getReferer());
            }
        }
        showMessage($lang['wt_common_del_fail'],urlAdminShop('store_class','store_class'));
    }

    /**
     * ajax操作
     */
    public function ajaxWt(){
        $model_class = Model('store_class');
        $update_array = array();
        switch ($_GET['branch']){
            //分类：添加、修改操作中 检测类别名称是否有重复
            case 'sc_name':
                $condition = array();
                $condition['sc_name'] = $_GET['value'];
                $condition['sc_id'] = array('neq',intval($_GET['sc_id']));
                $class_list = $model_class->getStoreClassList($condition);
                if (empty($class_list)){
                    $update_array['sc_name'] = $_GET['value'];
                    $update = $model_class->editStoreClass($update_array,array('sc_id'=>intval($_GET['id'])));
                    $return = $update ? true : false;
                } else {
                    $return = false;
                }
                break;
            //分类： 排序 显示 设置
            case 'sc_sort':
                $model_class = Model('store_class');
                $update_array['sc_sort'] = intval($_GET['value']);
                $result = $model_class->editStoreClass($update_array,array('sc_id'=>intval($_GET['id'])));
                $return = $result ? true : false;
                break;
        }
        exit(json_encode(array('result'=>$return)));
    }
    
    /**
     * 验证分类名称
     */
    public function ajax_check_nameWt(){
        $model_class = Model('store_class');
        $condition['sc_name'] = $_GET['sc_name'];
        $condition['sc_id'] = array('neq',intval($_GET['sc_id']));
        $class_list = $model_class->getStoreClassList($condition);
        $return = empty($class_list) ? 'true' : 'false';
        echo $return;
    }
}
