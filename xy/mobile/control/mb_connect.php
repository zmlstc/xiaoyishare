<?php
/**
 * 手机端微信公众账号二维码设置
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class mb_connectControl extends SystemControl{
    private $links = array(
        array('url'=>'mb_connect/wx','text'=>'微信登录'),
        array('url'=>'mb_connect/wap_wx','text'=>'微信小程序登录'),
        array('url'=>'mb_connect/h5_wx','text'=>'微信H5登录'),
        array('url'=>'mb_connect/qq','text'=>'QQ互联'),
        array('url'=>'mb_connect/sina','text'=>'新浪微博')
    );
    public function __construct(){
        parent::__construct();
        Language::read('setting');
    }

    public function indexWt() {
        $this->wxWt();
    }

    /**
     * 微信登录
     */
    public function wxWt() {
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['app_weixin_isuse']   = $_POST['app_weixin_isuse'];
            $update_array['app_weixin_appid']   = $_POST['app_weixin_appid'];
            $update_array['app_weixin_secret']  = $_POST['app_weixin_secret'];
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                $this->log('第三方账号登录，微信登录设置');
                showMessage(Language::get('wt_common_save_succ'));
            }else {
                showMessage(Language::get('wt_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'wx'));
	Tpl::setDirquna('mobile');
        Tpl::showpage('mb_connect_wx.edit');
    }

    /**
     * 小程序微信登录
     */
    public function wap_wxWt() {
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['wap_weixin_isuse']   = $_POST['wap_weixin_isuse'];
            $update_array['wap_weixin_appid']   = $_POST['wap_weixin_appid'];
            $update_array['wap_weixin_secret']  = $_POST['wap_weixin_secret'];
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                $this->log('第三方账号登录，WAP微信登录设置');
                showMessage(Language::get('wt_common_save_succ'));
            }else {
                showMessage(Language::get('wt_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'wap_wx'));
	Tpl::setDirquna('mobile');
        Tpl::showpage('mb_connect_wap_wx.edit');
    }
	
    /**
     * 微信H5登录
     */
    public function h5_wxWt() {
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['h5_weixin_isuse']   = $_POST['h5_weixin_isuse'];
            $update_array['h5_weixin_appid']   = $_POST['h5_weixin_appid'];
            $update_array['h5_weixin_secret']  = $_POST['h5_weixin_secret'];
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                $this->log('第三方账号登录，微信h5登录设置');
                showMessage(Language::get('wt_common_save_succ'));
            }else {
                showMessage(Language::get('wt_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'h5_wx'));
	Tpl::setDirquna('mobile');
        Tpl::showpage('mb_connect_h5_wx.edit');
    }

    /**
     * QQ互联登录
     */
    public function qqWt() {
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['app_qq_isuse']   = $_POST['app_qq_isuse'];
            $update_array['app_qq_akey']   = $_POST['app_qq_akey'];
            $update_array['app_qq_skey']  = $_POST['app_qq_skey'];
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                $this->log('第三方账号登录，QQ互联登录设置');
                showMessage(Language::get('wt_common_save_succ'));
            }else {
                showMessage(Language::get('wt_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'qq'));
	Tpl::setDirquna('mobile');
        Tpl::showpage('mb_connect_qq.edit');
	
    }

    /**
     * 新浪微博登录
     */
    public function sinaWt() {
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['app_sina_isuse']   = $_POST['app_sina_isuse'];
            $update_array['app_sina_akey']   = $_POST['app_sina_akey'];
            $update_array['app_sina_skey']  = $_POST['app_sina_skey'];
            $result = $model_setting->updateSetting($update_array);
            if ($result){
                $this->log('第三方账号登录，新浪微博登录设置');
                showMessage(Language::get('wt_common_save_succ'));
            }else {
                showMessage(Language::get('wt_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'sina'));
	Tpl::setDirquna('mobile');
        Tpl::showpage('mb_connect_sn.edit');
    }
}
