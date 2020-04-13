<?php
/**
 * 积分管理
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class invitesetControl extends SystemControl{
    const EXPORT_SIZE = 5000;
    public function __construct(){
        parent::__construct();
        Language::read('points');
        
    }

    public function indexWt() {
        $this->settingWt();
    }


    /**
     * 规则设置
     */
    public function settingWt() {
        Language::read('setting');
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['commis_rate_invstore'] = floatval($_POST['commis_rate_invstore'])?floatval($_POST['commis_rate_invstore']):0;
            $update_array['commis_rate_invmember'] = floatval($_POST['commis_rate_invmember'])?floatval($_POST['commis_rate_invmember']):0;
            $update_array['commis_rate_invpoints'] = floatval($_POST['commis_rate_invpoints'])?floatval($_POST['commis_rate_invpoints']):0;
            $update_array['commis_rate_system'] = floatval($_POST['commis_rate_system'])?floatval($_POST['commis_rate_system']):0;
			if(($update_array['commis_rate_invstore']+$update_array['commis_rate_invmember']+$update_array['commis_rate_invpoints']+$update_array['commis_rate_system'])!=100)
			{
				 showMessage('总和必须等于100%');
			}
			
			$result = $model_setting->updateSetting($update_array);
            if ($result === true){
                $this->log('推广佣金设置',1);
                showMessage(L('wt_common_save_succ'));
            }else {
                showMessage(L('wt_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
		Tpl::setDirquna('shop');
        Tpl::showpage('inviteset.setting');
    }

}
