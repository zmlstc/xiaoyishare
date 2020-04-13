<?php
/**
 * 代理管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class agenciesControl extends SystemControl{
    const EXPORT_SIZE = 1000;
	private $links = array(
	    array('url'=>'agencies/agencies','text'=>'代理商'),
		array('url'=>'agencies/agent_joinin','text'=>'代理申请') 
    );
    public function __construct(){
        parent::__construct();
        Language::read('member');
    }

    public function indexWt() {
        $this->agenciesWt();
		
    }

    /**
     * 管理
     */
    public function agenciesWt(){
        $model_agent = Model('agent');
        $condition = array();
        
        $list = $model_agent->getAgentList($condition,'*', 15);
        Tpl::output('list', $list);
        Tpl::output('show_page', $model_agent->showpage());
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'agencies'));
		Tpl::setDirquna('shop');
        Tpl::showpage('agent_list');
    }
	
	/**
     * 新增
     */
    public function agencies_addWt(){
        $lang   = Language::getLangContent();
        $model_agencies = Model('agent');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["agent_name"], "require"=>"true", "message"=>'登陆名不能为空'),
                array("input"=>$_POST["agent_passwd"], "require"=>"true", "message"=>'密码不能为空'),
                array("input"=>$_POST["agent_title"], "require"=>"true", "message"=>'代理名称不能为空')
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['agent_name']    = trim($_POST['agent_name']);
                $insert_array['agent_passwd']  = md5(trim($_POST['agent_passwd']));
                $insert_array['agent_title']   = trim($_POST['agent_title']);
                $insert_array['agent_contacts']= trim($_POST['agent_contacts']);
				
                $insert_array['agent_mobile']      = trim($_POST['agent_mobile']);
                $insert_array['agent_rate']      = floatval($_POST['agent_rate']);
				$insert_array['add_time']      = time();
                $insert_array['agent_state']      = intval($_POST['agent_state']);
         

                $result = $model_agencies->addAgent($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('agencies','agencies'),
                    'msg'=>'返回代理列表',
                    ),
                    array(
                    'url'=>urlAdminShop('agencies','agencies_add'),
                    'msg'=>'继续添加代理',
                    ),
                    );
                    $this->log('添加代理商'.'[ '.$_POST['agent_name'].']',1);
                    showMessage('新增代理成功！',$url);
                }else {
                    showMessage('添加失败');
                }
            }
        }
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'agencies_add'));
		Tpl::setDirquna('shop');
        Tpl::showpage('agent.add');
    }
	
	
	    /**
     * ajax操作
     */
    public function ajaxWt(){
        switch ($_GET['branch']){
            /**
             * 验证会员是否重复
             */
            case 'check_user_name':
                $model_member = Model('agent');
                $condition['agent_name']   = $_GET['agent_name'];
                $list = $model_member->getAgentInfo($condition);
                if (empty($list)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
           
        }
    }

	
    /**
     * 代理区域管理
     */
    public function arealistWt(){
		$agent_id = intval($_GET['agent_id']);
        $model_agent_area = Model('agent_area');
		$agent_info = Model('agent')->getAgentInfoByID($agent_id);
        $condition = array();
        $condition['agent_id'] = $agent_id;
        $list = $model_agent_area->getAgentAreaList($condition,'*', 15);
        Tpl::output('list', $list);
		Tpl::output('agencies_info', $agent_info);
        Tpl::output('show_page', $model_agent_area->showpage());
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'index'));
		Tpl::setDirquna('shop');
        Tpl::showpage('agent_area_list');
    }	
	
		
    /**
     * 代理区域添加
     */
    public function agencies_area_addWt(){
		$agent_id = intval($_GET['agent_id']);
        $model_agencies_area = Model('agent_area');
		$agencies_info = Model('agent')->getAgentInfoByID($agent_id);
		if (empty($agencies_info)) {
            showMessage('代理商信息错误', '', 'html', 'error');
        }
		if (chksubmit()) {
           
            $saveArray = array();
            $saveArray['agent_id'] = $agent_id;
            $saveArray['agent_title'] = $agencies_info['agent_title'];
            $province_id = intval($_POST['province_id']);
            
			$saveArray['province_id'] = $province_id;
            $saveArray['area_info']    = $_POST['region'];
            $saveArray['area_id']    = intval($_POST['area_id']);
            $saveArray['city_id']    = intval($_POST['city_id']);
			if ($province_id <1||$saveArray['area_id']<1||$saveArray['city_id']<1) {
                showMessage('代理区域选择错误', '', 'html', 'error');
            }else{
				$area_info = Model('agent_area')->getAgentAreaInfo(array('area_id'=>$saveArray['area_id']));
				if(!empty($area_info)&&is_array($area_info)){
					showMessage('代理区域已存在，请选择其他区域', '', 'html', 'error');
				}else{
					$state = Model('agent_area')->addAgentArea($saveArray);
					if($state) {
						//$update_array = array($province_id,intval($_POST['area_id']),intval($_POST['city_id']));
						//$update_array['agent_id']    = $agent_id;
						//$result = Model('area')->editArea(array('is_agent'=>1),array('area_id'=>array('in',$update_array),'is_agent'=>0));
					} 
				}
			}


            showMessage('操作成功', urlAdminShop('agencies', 'arealist',array('agent_id'=>$agent_id)));
        }
		
		Tpl::output('agencies_info', $agencies_info);
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'index'));
		Tpl::setDirquna('shop');
        Tpl::showpage('agent_area_add');
    }
	
	/**
	 * 删除代理区域
	 */
	public function agencies_area_delWt(){
		$lang	= Language::getLangContent();
		if (!empty($_GET['a_id'])){
			$model_agencies_area = Model('agent_area');
			$ag_area = $model_agencies_area->getAgentAreaInfoByID(intval($_GET['a_id']));
			if(!empty($ag_area)){
			
				//删除
				$model_agencies_area->del(intval($_GET['a_id']));
				if($state) {
					/* $update_array = array();
					$update_array['agent_id']    = 0;
					$result = Model('store')->editStore($update_array, array('area_id' => $ag_area['area_id'])); */
				}	
			}
			
			showMessage('删除成功',urlAdminShop('agencies', 'arealist',array('agent_id'=>$_GET['agent_id'])));
		}else {
			showMessage('删除失败',urlAdminShop('agencies', 'arealist',array('agent_id'=>$_GET['agent_id'])));
			
		}
	}


	
	
	
	
	
	
    /**
     * 代理编辑
     */
    public function agencies_editWt(){
        $lang   = Language::getLangContent();
        
		$agent_id = intval($_GET['agent_id']);
        $model_agencies = Model('agent');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["agent_title"], "require"=>"true", "message"=>'请输入代理商名称'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update_array = array();
                if (!empty($_POST['agent_passwd'])){
                    $update_array['agent_passwd'] = md5($_POST['agent_passwd']);
                }
                $update_array['agent_title']   = trim($_POST['agent_title']);
                $update_array['agent_contacts']= trim($_POST['agent_contacts']);
				
                $update_array['agent_mobile']      = trim($_POST['agent_mobile']);
                $update_array['agent_rate']      = floatval($_POST['agent_rate']);
                $update_array['agent_state']      = intval($_POST['agent_state']);

				
              
                $result = $model_agencies->editAgent(array('agent_id'=>intval($_POST['agent_id'])),$update_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('agencies','agencies'),
                    'msg'=>'返回列表',
                    ),
                    );
                    
                    showMessage('编辑成功！',$url);
                }else {
                    showMessage('编辑失败！');
                }
            }
        }
        
        $agencies_array = $model_agencies->getAgentInfoByID($agent_id);

        Tpl::output('agencies_array',$agencies_array);
		Tpl::setDirquna('shop');
        Tpl::showpage('agent.edit');
    }

    
    /**
     * 代理 待审核列表
     */
    public function agent_joininWt(){

        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'agent_joinin'));
		Tpl::setDirquna('shop');

        Tpl::showpage('agent_joinin');
    }	
	

    /**
     * 输出XML数据
     */
    public function get_joinin_xmlWt() {
        $model_store_joinin = Model('agent_joinin');
        // 设置页码参数名称
        $condition = array();
        //$condition['joinin_state'] = array('gt',0);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('joinin_state', 'joinin_year', 'contacts_name', 'contacts_phone'
                , 'company_name', 'company_province_id', 'company_phone'
        );
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        //列表
        $store_list = $model_store_joinin->getList($condition, $page, $order); 

        // 开店状态
        $joinin_state_array = $this->get_store_joinin_state();

        $data = array();
        $data['now_page'] = $model_store_joinin->shownowpage();
        $data['total_num'] = $model_store_joinin->gettotalnum();
        foreach ($store_list as $value) {
            $param = array();
            if(in_array(intval($value['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
                $operation = "<a class='btn orange' href=\"".urlAdminShop('agencies','joinin_detail',array('id'=>$value['id']))."\"><i class=\"fa fa-check-bbs\"></i>审核</a>";
            } else {
                $operation = "<a class='btn green' href=\"".urlAdminShop('agencies','joinin_detail',array('id'=>$value['id']))."\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            }
            $param['operation'] = $operation;
			$param['agent_phone'] = $value['agent_phone'];
            $param['joinin_state'] = $joinin_state_array[$value['joinin_state']];
            $param['contacts_name'] = $value['contacts_name'];
            $param['contacts_phone'] = $value['contacts_phone'];
            $param['company_name'] = $value['company_name'];
            $param['company_province_id'] = $value['company_address'] . ' ' . $value['company_address_detail'];
            $param['area_info'] = $value['area_info'];
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    private function get_store_joinin_state() {
        $joinin_state_array = array(
            STORE_JOIN_STATE_NEW => '新申请',
            STORE_JOIN_STATE_VERIFY_FAIL => '审核失败',
            STORE_JOIN_STATE_FINAL => '申请成功',
        );
        return $joinin_state_array;
    }   	
	
    /**
     * 审核详细页
     */
    public function joinin_detailWt(){
        $model_joinin = Model('agent_joinin');
        $joinin_detail = $model_joinin->getOne(array('id'=>$_GET['id']));
        $joinin_detail_title = '查看';
        if(in_array(intval($joinin_detail['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
            $joinin_detail_title = '审核';
        }
        Tpl::output('joinin_detail_title', $joinin_detail_title);
        Tpl::output('joinin_detail', $joinin_detail);
	
		Tpl::setDirquna('shop');
		
		Tpl::showpage('agent_joinin.detail');
		
    }	
	
    /**
     * 审核
     */
    public function joinin_verifyWt() {
        $model_joinin = Model('agent_joinin');
        $joinin_detail = $model_joinin->getOne(array('id'=>$_POST['id']));
		if(intval($joinin_detail['joinin_state'])==STORE_JOIN_STATE_NEW){
			$param = array();
			$param['joinin_state'] = $_POST['verify_type'] === 'pass' ? STORE_JOIN_STATE_FINAL : STORE_JOIN_STATE_VERIFY_FAIL;
			$param['joinin_message'] = $_POST['joinin_message'];
			$ret=$model_joinin->modify($param, array('id'=>$_POST['id']));
			if ($ret) {
				$this->joinin_verify_open($joinin_detail);
			}
		}
		
    }	

    private function joinin_verify_open($joinin_detail) {
        $model_store    = Model('store');
        $model_agent = Model('agent');

        $agent =$model_agent->getAgentInfo(array('agent_mobile' => $joinin_detail['agent_phone']));
        if(!empty($agent)&& is_array($agent)) {
            showMessage('代理申请的手机号码已存在','');
        }

        if($_POST['verify_type'] === 'pass') {
            //开店
            $agent_array     = array();
            $agent_array['agent_name'] = $joinin_detail['agent_phone'];
            $agent_array['agent_passwd']     = '000';
            $agent_array['agent_title']   = $joinin_detail['company_name'];
            $agent_array['agent_contacts']        = $joinin_detail['contacts_name'];
            $agent_array['agent_email'] = $joinin_detail['contacts_email'];
            $agent_array['agent_mobile']  = $joinin_detail['agent_phone'];
            $agent_array['agent_rate']    = 2;
            $agent_array['add_time']= time();
			$agent_array['agent_state']= 1;
            $agent_array['join_id']     = $joinin_detail['id'];
			
			$agent_id = $model_agent->addAgent($agent_array);

            if($agent_id) {
                //写入地区
                $area_array = array();
                $area_array['agent_id'] = $agent_id;
                $area_array['agent_title'] = $joinin_detail['company_name'];
                $area_array['area_id'] = $joinin_detail['area_id'];
                $area_array['city_id'] = $joinin_detail['city_id'];
                $area_array['province_id'] = $joinin_detail['province_id'];
                $area_array['area_info'] = $joinin_detail['area_info'];
                $state = Model('agent_area')->addAgentArea($area_array);
            }

            if($state) {
				$subject = '代理商审核通知——'.C('site_name');
                $message = '您申请的代理审核通过，请点击下面网址进行设置密码：';
				$_url = $agent_id.'|'.md5(md5('Y#23W@89sx2sf!24df'.$agent_id).$joinin_detail['agent_phone'].time()).'|'.$joinin_detail['agent_phone'].'|'.time();
				$url = AGENTS_SITE_URL.'/index.php/aset/set?url='.base64_encode($_url);
				$message .='<br><a href="'.$url.'" target="_blank">'.$url.'</a>';
                $this->sendMail($joinin_detail['contacts_email'], $subject, $message);
                showMessage('代理申请成功',urlAdminShop('agencies','agent_joinin'));
            } else {
                showMessage('代理申请失败',urlAdminShop('agencies','agent_joinin'));
            }
        } else {
            showMessage('代理申请拒绝',urlAdminShop('agencies','agent_joinin'));
        }
    }	
	
	
	
	
}
