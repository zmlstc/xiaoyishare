<?php
/**
 * 会员中心——我是卖家
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class facilityControl extends BaseSellerControl {

    const MAX_MB_SLIDERS = 5;

    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	public function indexWt(){
		$this->facilityWt();
	}
    public function facilityWt()
    {
        $model_store = Model('store');
        $store_id = $_SESSION['store_id'];
        $store_info = $model_store->getStoreInfoByID($store_id);
        
        if (chksubmit()){
            
			$param['f_ids'] = implode(",",$_POST['f_id']); 
            $model_store->editStore($param, array('store_id' => $store_id));
            showDialog('保存成功！',urlSeller('facility','facility'),'succ');
        }
       
		$store_class = Model('store_class')->getStoreClassInfo(array('sc_id'=>intval($store_info['scid2'])));
        if(!empty($store_class)&& $store_class['facility_ids']!=''){
			$fc_list = Model('facility_class')->getFacilityClassList(array('fc_id'=>array('in',$store_class['facility_ids'])));	
			$f_list = Model('facility')->getFacilityList(array('fc_id'=>array('in',$store_class['facility_ids'])));
			$flist= array();
			if(!empty($f_list)&& is_array($f_list)){
				foreach($f_list as $v){
					$flist[$v['fc_id']][] = $v;
				}
			}
			$fclist= array();
			if(!empty($fc_list)&& is_array($fc_list)){
				foreach($fc_list as $v){
					$fclist[$v['fc_id']] = $v;
					$fclist[$v['fc_id']]['flist'] = $flist[$v['fc_id']];
				}
			}			
			Tpl::output('fc_list',$fclist);
		}
        Tpl::output('store_info',$store_info);
		Tpl::output('f_ids',explode(",", $store_info['f_ids']));
		$this->profile_menu('facility');
		 
        Tpl::showpage('facility_form');
    }

    private function profile_menu($menu_key='') {
        Language::read('member_layout');
        $menu_array = array(
            1=>array('menu_key'=>'facility','menu_name'=>'设施管理','menu_url'=>urlSeller('facility','index')),
           
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }

}
