<?php
/**
 * 活动分类管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class plat_actclassControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('store_class');
    }

    public function indexWt() {
        $this->act_classWt();
    }

    /**
     * 店铺分类
     */
    public function act_classWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('plat_act_class');

        //删除
      /*   if (chksubmit()){
            if (!empty($_POST['check_sc_id']) && is_array($_POST['check_sc_id']) ){
                $result = $model_class->delActClass(array('gc_id'=>array('in',$_POST['check_sc_id'])));
                if ($result) {
                    $this->log(L('wt_del,store_class').'[ID:'.implode(',',$_POST['check_sc_id']).']',1);
                    showMessage($lang['wt_common_del_succ']);
                }
            }
            showMessage($lang['wt_common_del_fail']);
        } */
	 	$parentid=0;
	/*	if(isset($_GET['pid'])&&intval($_GET['pid'])>0){
			$parentid= intval($_GET['pid']);
			$class_array = $model_class->getStoreClassInfo(array('sc_id'=>$parentid));
			if (empty($class_array)){
				$parentid=0;
			}
			Tpl::output('cInfo',$class_array);
		} */

        $class_list = $model_class->getActClassList(array('parent_id'=>$parentid));
        Tpl::output('class_list',$class_list);
		Tpl::setDirquna('shop');
		/* if($parentid>0){
			Tpl::showpage('store_class_child.index');
		}else */
		{
			Tpl::showpage('plat_act_class.index');
		}
    }
	


    /**
     * 分类添加
     */
    public function class_addWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('plat_act_class');
        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["gc_name"], "require"=>"true", "message"=>'分类名称不能为空!'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['gc_name'] = $_POST['gc_name'];
                $insert_array['gc_sort'] = intval($_POST['gc_sort']);
                $result = $model_class->addActClass($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('plat_actclass','class_add'),
                    'msg'=>$lang['continue_add_store_class'],
                    ),
                    array(
                    'url'=>urlAdminShop('plat_actclass','act_class'),
                    'msg'=>$lang['back_store_class_list'],
                    )
                    );
                    $this->log(L('wt_add,store_class').'['.$_POST['gc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],$url,'html','succ',1,5000);
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

		
		Tpl::setDirquna('shop');
        Tpl::showpage('plat_act_class.add');
    }

    /**
     * 编辑
     */
    public function class_editWt(){
        $lang   = Language::getLangContent();

        $model_class = Model('plat_act_class');

        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["gc_name"], "require"=>"true", "message"=>'分类名称不能为空!'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update = array();
                $update['gc_name'] = $_POST['gc_name'];
                $update['gc_sort'] = intval($_POST['gc_sort']);
                $result = $model_class->editActClass($update,array('gc_id'=>intval($_POST['gc_id'])));
                if ($result){
                    $this->log(L('wt_edit,store_class').'['.$_POST['gc_name'].']',1);
                    showMessage($lang['wt_common_save_succ'],urlAdminShop('plat_actclass','act_class'));
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        $class_array = $model_class->getActClassInfo(array('gc_id'=>intval($_GET['gc_id'])));
        if (empty($class_array)){
            showMessage($lang['illegal_parameter']);
        }
        Tpl::output('class_array',$class_array);

		Tpl::setDirquna('shop');
        Tpl::showpage('plat_act_class.edit');
    }


	
    /**
     * 删除分类
     */
    public function class_delWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('plat_act_class');
        if (intval($_GET['gc_id']) > 0){
            $array = array(intval($_GET['gc_id']));
            $result = $model_class->delActClass(array('gc_id'=>intval($_GET['gc_id'])));
            if ($result) {
                 showMessage($lang['wt_common_del_succ'],getReferer());
            }
        }
        showMessage($lang['wt_common_del_fail'],urlAdminShop('plat_actclass','act_class'));
    }

    /**
     * ajax操作
     */
    public function ajaxWt(){
        $model_class = Model('plat_act_class');
        $update_array = array();
        switch ($_GET['branch']){
            //分类：添加、修改操作中 检测类别名称是否有重复
            case 'gc_name':
                $condition = array();
                $condition['gc_name'] = $_GET['value'];
                $condition['gc_id'] = array('neq',intval($_GET['gc_id']));
                $class_list = $model_class->getActClassList($condition);
                if (empty($class_list)){
                    $update_array['gc_name'] = $_GET['value'];
                    $update = $model_class->editActClass($update_array,array('gc_id'=>intval($_GET['id'])));
                    $return = $update ? true : false;
                } else {
                    $return = false;
                }
                break;
            //分类： 排序 显示 设置
            case 'gc_sort':
                $update_array['gc_sort'] = intval($_GET['value']);
                $result = $model_class->editActClass($update_array,array('gc_id'=>intval($_GET['id'])));
                $return = $result ? true : false;
                break;
        }
        exit(json_encode(array('result'=>$return)));
    }
    
    /**
     * 验证分类名称
     */
    public function ajax_check_nameWt(){
        $model_class = Model('plat_act_class');
        $condition['gc_name'] = $_GET['gc_name'];
        $condition['gc_id'] = array('neq',intval($_GET['gc_id']));
        $class_list = $model_class->getActClassList($condition);
        $return = empty($class_list) ? 'true' : 'false';
        echo $return;
    }
}
