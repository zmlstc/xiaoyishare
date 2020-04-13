<?php
/**
 * 默认展示页面
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class asetControl extends CommControl{
    public function __construct(){
        parent::__construct();
       
    }
    public function indexWt(){
        //$_url = '2|'.md5(md5('Y#23W@89sx2sf!24df2').'13800000000').'|13800000000';
		//$url = AGENTS_SITE_URL.'/index.php/aset/set?url='.base64_encode($_url);
		//echo $url;
    }
	public function setWt(){
        //输出管理员信息
        
		$url = base64_decode(str_replace(' ','+',trim($_GET['url'])));//echo $url;exit;
		$agent = explode("|", $url);
		if(count($agent)!=4){
			showMessage('参数错误！',AGENTS_SITE_URL);
		}
		if(strtotime('-7 day') >intval($agent[3])){
			showMessage('地址已失效！',AGENTS_SITE_URL);
		}
		$md5 = md5(md5('Y#23W@89sx2sf!24df'.$agent[0]).$agent[2].$agent[3]);
		if($md5 !=$agent[1]){
			showMessage('参数错误！！',AGENTS_SITE_URL .'/index.php/aset');
		}
		$agentInfo = Model('agent')->getAgentInfoByID(intval($agent[0]));
		
		if(!empty($agentInfo)&&$agentInfo['agent_mobile']==$agent[2]){
			if($agentInfo['agent_passwd']==='000')
			{
				showMessage('地址已失效！',AGENTS_SITE_URL);exit;
			}
			$agetn_join = Model('agent_joinin')->getOne(array('id'=>$agentInfo['join_id']));
			 Tpl::output('agetn_join',$agetn_join);
		}else{
			showMessage('参数错误',AGENTS_SITE_URL .'/index.php/aset');
		}
		 if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST['agent_name'], "require"=>"true","validator"=>"Length","min"=>"6","max"=>"20","message"=>"登陆名不能为空且6至20个字符"),
				array("input"=>$_POST['agent_pwd'], "require"=>"true","validator"=>"Length","min"=>"6","max"=>"20","message"=>"登陆密码不能为空且6至20个字符"),
				
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}
			if(trim($_POST['agent_pwd'])!=trim($_POST['agent_pwd'])){
				showMessage('两次输入的密码不一致！');
			}
			$data = array();
            $data['agent_name'] = $_POST['agent_name'];
            $data['agent_passwd'] = md5($_POST['agent_pwd']);
			
			$ret = Model('agent')->editAgent(array('agent_id'=>$agentInfo['agent_id']), $data);
			if($ret){
				showMessage('注册成功！',AGENTS_SITE_URL ,'succ');
			}else{
				showMessage('提交失败！');
			}
		 }
		//echo json_encode($agetn_join); 
        Tpl::output('agentInfo',$agentInfo);
        

        Tpl::showpage('aset');
    }



	
}
