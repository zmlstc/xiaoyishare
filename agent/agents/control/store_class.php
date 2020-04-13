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
		$admininfo = $this->getAdminInfo();      
		
        $store_class_list = $model_class->getStoreClassList(array('parent_id'=>array('gt',0)),12);
        Tpl::output('class_list',$store_class_list);
		$parent_ids = array();
		$sc_ids = array();
		if(!empty($store_class_list)&&is_array($store_class_list)){
			foreach($store_class_list as $class){
				$parent_ids[] = $class['parent_id'];
				$sc_ids[] = $class['sc_id'];
			}
			 $psc_list = $model_class->getStoreClassList(array('sc_id'=>array('in',$parent_ids)));
			 $psclist = array();
			 if(!empty($psc_list)&& is_array($psc_list)){
				 foreach($psc_list as $psc){
					$psclist[$psc['sc_id']] = $psc; 
				 }
				 Tpl::output('psclist',$psclist); 
			 }
			 $asc_list = Model('agent_store_class')->getList(array('sc_id'=>array('in',$sc_ids),'agent_id'=>$admininfo['id']),50);
			$asclist= array();
			  if(!empty($asc_list)&& is_array($asc_list)){
				 foreach($asc_list as $asc){
					$asclist[$asc['sc_id']] = $asc; 
				 }
				 Tpl::output('asclist',$asclist); 
			 }
			 
		}
		Tpl::setDirquna('agents');
		
		Tpl::showpage('store_class.index');
		
    }

	/**
     * 编辑分类
     */
    public function store_class_editWt(){
        $lang   = Language::getLangContent();
		$admininfo = $this->getAdminInfo(); 

        $model_class = Model('store_class');

        if (chksubmit()){
            //验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["commis_rate"], "require"=>"true", "message"=>'佣金不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
				
				if($_POST['commis_rate']>100){
					 showMessage('平台总佣金比例不能超过100%');
				}
				
				$class_array = $model_class->getStoreClassInfo(array('sc_id'=>intval($_GET['sc_id'])));
				if (empty($class_array)||$class_array['parent_id']==0){
					showMessage($lang['illegal_parameter']);
				}
				if($_POST['commis_rate']<$class_array['commis_rate']){
					 showMessage('佣金不能低于平台最低佣金比列');
				}
				$model_agent = Model('agent_store_class');
				$aclass = $model_agent->getInfo(array('sc_id'=>intval($_POST['sc_id']),'agent_id'=>$admininfo['id']));
				if(!empty($aclass)&&is_array($aclass)){
					$result = $model_agent->edit(array('agent_rate'=>floatval($_POST['commis_rate'])),array('sc_id'=>intval($_POST['sc_id']),'agent_id'=>$admininfo['id']));
				}else{
					$data = array();
					$data['sc_id'] = intval($_POST['sc_id']);
					$data['agent_rate'] = floatval($_POST['commis_rate']);
					$data['agent_id'] = $admininfo['id'];
					$result = $model_agent->add($data);
				}
              
				
                if ($result){
                    $this->log(L('wt_edit,store_class').'['.$_POST['sc_id'].']',1);
                    showMessage($lang['wt_common_save_succ'],urlAgentAgents('store_class','store_class'));
                }else {
                    showMessage($lang['wt_common_save_fail']);
                }
            }
        }

        $class_array = $model_class->getStoreClassInfo(array('sc_id'=>intval($_GET['sc_id'])));
        if (empty($class_array)||$class_array['parent_id']==0){
            showMessage($lang['illegal_parameter']);
        }
        Tpl::output('pclass',$class_array);
		
		$aclass = Model('agent_store_class')->getInfo(array('sc_id'=>intval($class_array['sc_id']),'agent_id'=>$admininfo['id']));
		if(!empty($aclass)&&is_array($aclass)){
			Tpl::output('aclass',$aclass['agent_rate']);
		}else{
			Tpl::output('aclass',$class_array['commis_rate']);
		}
		
		

		Tpl::setDirquna('agents');
        Tpl::showpage('store_class.edit');
    }
	

}
