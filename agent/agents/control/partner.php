<?php
/**
 * 管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class partnerControl extends SystemControl{
    const EXPORT_SIZE = 1000;
	private $links = array(
	    array('url'=>'/partner/index','text'=>'合伙人'),
		array('url'=>'/partner/partner_add','text'=>'新增') 
    );
    public function __construct(){
        parent::__construct();
        Language::read('member');
    }

    public function indexWt() {
        $this->partnerWt();
    }

    /**
     * 会员管理
     */
    public function partnerWt(){
        $model_partner = Model('partner');
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['agent_id'] = $admininfo['id'];
        
        $list = $model_partner->getPartnerList($condition,'*', 15);
        Tpl::output('list', $list);
        Tpl::output('show_page', $model_partner->showpage());
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'index'));
		Tpl::setDirquna('agents');
        Tpl::showpage('partner_list');
    }
	
	/**
     * 新增代理商
     */
    public function partner_addWt(){
        $lang   = Language::getLangContent();
        $model_partner = Model('partner');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["partner_name"], "require"=>"true", "message"=>'请输入姓名'),
                array("input"=>$_POST["partner_jobnum"], "require"=>"true", "message"=>'工号不能为空'),
                array("input"=>$_POST["partner_mobile"], "require"=>"true", "message"=>'手机号码不能为空')
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
				$admininfo = $this->getAdminInfo();
                $insert_array = array();
                $insert_array['agent_id']    = $admininfo['id'];
                $insert_array['partner_name']    = trim($_POST['partner_name']);
                $insert_array['partner_jobnum']  = trim($_POST['partner_jobnum']);
                $insert_array['partner_mobile']   = trim($_POST['partner_mobile']);
                $insert_array['partner_rate'] = floatval($_POST['partner_rate']);
				
                $insert_array['partner_guding']      = floatval($_POST['partner_guding']);
                $insert_array['partner_way']      = intval($_POST['partner_way']);
                $insert_array['partner_txt']      = trim($_POST['partner_txt']);
				$insert_array['add_time']      = time();
                
				
				if (!empty($_FILES['agent_idcard_pic']['name'])){
					$upload = new UploadFile();
					$upload->set('default_dir',ATTACH_COMMON);
					$result = $upload->upfile('agent_idcard_pic');
					if ($result){
						$_POST['partner_idcardpic'] = $upload->file_name;
					}else {
						showMessage($upload->error,'','','error');
					}
				}
				
                if (!empty($_POST['partner_idcardpic'])){
					$insert_array['partner_idcardpic']     = trim($_POST['partner_idcardpic']);
				}
                

                $result = $model_partner->addPartner($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>'index.php/partner/partner',
                    'msg'=>'返回列表',
                    ),
                    array(
                    'url'=>'index.php/partner/partner_add',
                    'msg'=>'继续添加',
                    ),
                    );
                    $this->log('添加合伙人'.'[ '.$_POST['partner_name'].']',1);
                    showMessage('添加成功',$url);
                }else {
                    showMessage('添加失败');
                }
            }
        }
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'partner_add'));
		Tpl::setDirquna('agents');
        Tpl::showpage('partner.add');
    }
	
	
    /**
     * 代理编辑
     */
    public function partner_editWt(){
        $lang   = Language::getLangContent();
        
		$partner_id = intval($_GET['partner_id']);
        $model_partner = Model('partner');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["partner_name"], "require"=>"true", "message"=>'请输入姓名'),
                array("input"=>$_POST["partner_jobnum"], "require"=>"true", "message"=>'工号不能为空'),
                array("input"=>$_POST["partner_mobile"], "require"=>"true", "message"=>'手机号码不能为空')
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update_array = array();
                 $update_array['partner_name']    = trim($_POST['partner_name']);
                $update_array['partner_jobnum']  = trim($_POST['partner_jobnum']);
                $update_array['partner_mobile']   = trim($_POST['partner_mobile']);
                $update_array['partner_rate'] = floatval($_POST['partner_rate']);
				
                $update_array['partner_guding']      = floatval($_POST['partner_guding']);
                $update_array['partner_way']      = intval($_POST['partner_way']);
                $update_array['partner_txt']      = trim($_POST['partner_txt']);
                
				
				if (!empty($_FILES['agent_idcard_pic']['name'])){
					$upload = new UploadFile();
					$upload->set('default_dir',ATTACH_COMMON);
					$result = $upload->upfile('agent_idcard_pic');
					if ($result){
						$_POST['partner_idcardpic'] = $upload->file_name;
					}else {
						showMessage($upload->error,'','','error');
					}
				}
				
                if (!empty($_POST['partner_idcardpic'])){
					$update_array['partner_idcardpic']     = trim($_POST['partner_idcardpic']);
				}
				
              
                $result = $model_partner->editPartner(array('partner_id'=>intval($_POST['partner_id'])),$update_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>'index.php/partner/partner',
                    'msg'=>'返回列表',
                    ),
                    );
                    
                    showMessage('编辑成功！',$url);
                }else {
                    showMessage('编辑失败！');
                }
            }
        }
        
        $partner_array = $model_partner->getPartnerInfoByID($partner_id);

        Tpl::output('partner_array',$partner_array);
		Tpl::setDirquna('agents');
        Tpl::showpage('partner.edit');
    }

    
	
	
	
	
	
	
	
	
	
	
	
	
}
