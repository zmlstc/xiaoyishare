<?php
/**
 * 登录
 *
 * 包括 登录 验证 退出 操作
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class LoginControl extends SystemControl {

    /**
     * 不进行父类的登录验证，所以增加构造方法重写了父类的构造方法
     */
    public function __construct(){
        Language::read('common,layout,login');
        $result = chksubmit(true,true,'num');
        if ($result){
            if ($result === -11){
                showMessage('非法请求');
            }elseif ($result === -12){
                showMessage(L('login_index_checkcode_wrong'));
            }
           
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["user_name"], "require"=>"true", "message"=>L('login_index_username_null')),
                array("input"=>$_POST["password"],  "require"=>"true", "message"=>L('login_index_password_null')),
                array("input"=>$_POST["captcha"],   "require"=>"true", "message"=>L('login_index_checkcode_null')),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage(L('error').$error);
            } else {
                $model_admin = Model('agent');
                $array  = array();
                $array['agent_name']    = $_POST['user_name'];
                $array['agent_passwd']= md5(trim($_POST['password']));
                $agent_info = $model_admin->getAgentInfo($array);
                if(is_array($agent_info) and !empty($agent_info)) {
                    
                    $group_name = '代理商';
                    
                    $array = array();
                    $array['name']  = $agent_info['agent_name'];
                    $array['id']    = $agent_info['agent_id'];
                    $array['time']  = $agent_info['agent_login_time'];
                    $array['ip']    = getIp();
                    $this->systemSetKey($array);
                    $update_info    = array(
                        'agent_login_num'=>($agent_info['agent_login_num']+1),
                        'agent_login_time'=>TIMESTAMP
                    );
                    $model_admin->editAgent(array('agent_id'=>$agent_info['agent_id']),$update_info);
                    
                    @header('Location: '.AGENTS_SITE_URL);exit;
                } else {
                    showMessage(L('login_index_username_password_wrong'),'login/login');
                }
            }
        }
        Tpl::output('html_title',L('login_index_need_login'));
        Tpl::showpage('login','login_layout');
    }
    public function loginWt(){}
    public function indexWt(){}
}
