<?php
/**
 * 会员管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class memberControl extends SystemControl{
    const EXPORT_SIZE = 1000;
	private $links = array(
	    array('url'=>'member/index','lang'=>'member_index_manage'),
		array('url'=>'member/predeposit_add','lang'=>'member_index_predeposit'), 
		array('url'=>'member/realverify','text'=>'实名验证'),
    );
    public function __construct(){
        parent::__construct();
        Language::read('member');
    }

    public function indexWt() {
        $this->memberWt();
    }

    /**
     * 会员管理
     */
    public function memberWt(){
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'index'));
		Tpl::setDirquna('shop');
        Tpl::showpage('member.index');
    }

    /**
     * 会员修改
     */
    public function member_editWt(){
        $lang   = Language::getLangContent();
        $model_member = Model('member');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
            array("input"=>$_POST["member_email"], "require"=>"true", 'validator'=>'Email', "message"=>$lang['member_edit_valid_email']),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $update_array = array();
                $update_array['member_id']          = intval($_POST['member_id']);
                if (!empty($_POST['member_passwd'])){
                    $update_array['member_passwd'] = md5($_POST['member_passwd']);
                }
				if (!empty($_POST['member_paypwd'])){
                    $update_array['member_paypwd'] = md5($_POST['member_paypwd']);
                }
                $update_array['member_email']       = $_POST['member_email'];
                $update_array['member_truename']    = $_POST['member_truename'];
                $update_array['member_sex']         = $_POST['member_sex'];
                $update_array['member_qq']          = $_POST['member_qq'];
                $update_array['member_ww']          = $_POST['member_ww'];
		$update_array['member_state']       = $_POST['memberstate'];
                $update_array['inform_allow']       = $_POST['inform_allow'];
                $update_array['is_buy']             = $_POST['isbuy'];
                $update_array['is_allowtalk']       = $_POST['allowtalk'];
				$update_array['member_mobile'] 		= $_POST['member_mobile'];
				$update_array['member_email_bind'] 	= intval($_POST['emailbind']);
				$update_array['member_mobile_bind'] = intval($_POST['mobilebind']);
				
                if (!empty($_POST['member_avatar'])){
                    $update_array['member_avatar'] = $_POST['member_avatar'];
                }
                $result = $model_member->editMember(array('member_id'=>intval($_POST['member_id'])),$update_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('member','member'),
                    'msg'=>$lang['member_edit_back_to_list'],
                    ),
                    array(
                    'url'=>urlAdminShop('member','member_edit',array('member_id'=>intval($_POST['member_id']))),
                    'msg'=>$lang['member_edit_again'],
                    ),
                    );
                    $this->log(L('wt_edit,member_index_name').'[ID:'.$_POST['member_id'].']',1);
                    showMessage($lang['member_edit_succ'],$url);
                }else {
                    showMessage($lang['member_edit_fail']);
                }
            }
        }
        $condition['member_id'] = intval($_GET['member_id']);
        $member_array = $model_member->getMemberInfo($condition);

        Tpl::output('member_array',$member_array);
		Tpl::setDirquna('shop');
        Tpl::showpage('member.edit');
    }

    /**
     * 新增会员
     */
    public function member_addWt(){
        $lang   = Language::getLangContent();
        $model_member = Model('member');
        /**
         * 保存
         */
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["member_name"], "require"=>"true", "message"=>$lang['member_add_name_null']),
                array("input"=>$_POST["member_passwd"], "require"=>"true", "message"=>'密码不能为空'),
                array("input"=>$_POST["member_email"], "require"=>"true", 'validator'=>'Email', "message"=>$lang['member_edit_valid_email'])
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['member_name']    = trim($_POST['member_name']);
                $insert_array['member_passwd']  = trim($_POST['member_passwd']);
                $insert_array['member_email']   = trim($_POST['member_email']);
                $insert_array['member_truename']= trim($_POST['member_truename']);
                $insert_array['member_sex']     = trim($_POST['member_sex']);
                $insert_array['member_qq']      = trim($_POST['member_qq']);
                $insert_array['member_ww']      = trim($_POST['member_ww']);
                //默认允许举报商品
                $insert_array['inform_allow']   = '1';
                if (!empty($_POST['member_avatar'])){
                    $insert_array['member_avatar'] = trim($_POST['member_avatar']);
                }

                $result = $model_member->addMember($insert_array);
                if ($result){
                    $url = array(
                    array(
                    'url'=>urlAdminShop('member','member'),
                    'msg'=>$lang['member_add_back_to_list'],
                    ),
                    array(
                    'url'=>urlAdminShop('member','member_add'),
                    'msg'=>$lang['member_add_again'],
                    ),
                    );
                    $this->log(L('wt_add,member_index_name').'[ '.$_POST['member_name'].']',1);
                    showMessage($lang['member_add_succ'],$url);
                }else {
                    showMessage($lang['member_add_fail']);
                }
            }
        }
		Tpl::setDirquna('shop');
        Tpl::showpage('member.add');
    }
	
	/**
	 * 删除会员
	 */
	public function member_delWt(){
		$lang	= Language::getLangContent();
		if (!empty($_GET['member_id'])){
			$model_member = Model('member');
			$condition['member_id'] = intval($_GET['member_id']);
			$member_array = $model_member->getMemberInfo($condition);
			//$rs = $model_member->del(intval($_GET['member_id']));
			$rs = true;
			if ($rs){
				//删除该会员商品,店铺
				//获得该会员店铺信息
				$model_store = Model('store');
				$model_goods = Model('goods');
				$model_order = Model('order');
				//删除店铺
				//$model_store->delStoreEntirely($member_array['store_id']);
				//删除商品
				//$model_goods->delGoodsAll($member_array['store_id']);
				//删除会员
				$model_member->del(intval($_GET['member_id']));
				
			}
			showMessage('删除成功',urlAdminShop('member'));
		}else {
			showMessage('删除失败',urlAdminShop('member'));
			
		}
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
                $model_member = Model('member');
                $condition['member_name']   = $_GET['member_name'];
                $condition['member_id'] = array('neq',intval($_GET['member_id']));
                $list = $model_member->getMemberInfo($condition);
                if (empty($list)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
                /**
             * 验证邮件是否重复
             */
            case 'check_email':
                $model_member = Model('member');
                $condition['member_email'] = $_GET['member_email'];
                $condition['member_id'] = array('neq',intval($_GET['member_id']));
                $list = $model_member->getMemberInfo($condition);
                if (empty($list)){
                    echo 'true';exit;
                }else {
                    echo 'false';exit;
                }
                break;
        }
    }

    /**
     * 输出XML数据
     */
    public function get_xmlWt() {
        $model_member = Model('member');
		/**
		 * 删除
		 */
		if ($_POST['form_submit'] == 'ok'){
			
			/**
			 * 删除
			 */
			if (!empty($_POST['del_id'])){
				if($GLOBALS['setting_config']['ucenter_status'] == '1') {
					$model_ucenter = Model('ucenter');
				}
				if (is_array($_POST['del_id'])){
					foreach ($_POST['del_id'] as $k => $v){
						$v = intval($v);
						$rs = true;//$model_member->del($v);
						if ($rs){
							//删除该会员商品,店铺
							//获得该会员店铺信息
							$member = $model_member->infoMember(array(
								'member_id'=>$v
							));
							$model_store = Model('store');
							$model_goods = Model('goods');
							$model_order = Model('order');
							//删除店铺					
							//$model_store->del($member['store_id']);
							//删除商品
							//$model_goods->dropGoodsByStore($member['store_id']);
							//删除用户
							$model_member->del($v);
						}
					}
				}
				showMessage($lang['member_index_del_succ']);
			}else {
				showMessage($lang['member_index_choose_del']);
			}
		}
        $member_grade = $model_member->getMemberGradeArr();
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('member_id','member_name','member_avatar','member_email','member_mobile','member_sex','member_truename','member_birthday'
                ,'member_time','member_login_time','member_login_ip','member_points','member_exppoints','member_grade','available_predeposit'
                ,'freeze_predeposit','available_rc_balance','freeze_rc_balance','inform_allow','is_buy','is_allowtalk','member_state'
        );
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $member_list = $model_member->getMemberList($condition, '*', $page, $order);

        $sex_array = $this->get_sex();

        $data = array();
        $data['now_page'] = $model_member->shownowpage();
        $data['total_num'] = $model_member->gettotalnum();
        foreach ($member_list as $value) {
            $param = array();
            $param['operation'] = "<a class='btn blue' href='".urlAdminShop('member','member_edit',array('member_id'=>$value['member_id']))."'><i class='fa fa-pencil-square-o'></i>编辑</a>
			<a href='javascript:submit_delete(". $value['member_id'] .");' class='btn red'><i class='fa fa-trash-o'></i>删除</a>";
            $param['member_id'] = $value['member_id'];
            $param['member_name'] = "<img src=".getMemberAvatarForID($value['member_id'])." class='user-avatar' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getMemberAvatarForID($value['member_id']).">\")'>".$value['member_name'];
            $param['member_email'] = $value['member_email'];
            $param['member_mobile'] = $value['member_mobile'];
            $param['member_sex'] = $sex_array[$value['member_sex']];
            $param['member_truename'] = $value['member_truename'];
            $param['member_birthday'] = $value['member_birthday'];
            $param['member_time'] = date('Y-m-d', $value['member_time']);
            $param['member_login_time'] = date('Y-m-d', $value['member_login_time']);
            $param['member_login_ip'] = $value['member_login_ip'];
            $param['member_points'] = $value['member_points'];
            $param['member_exppoints'] = $value['member_exppoints'];
            $param['member_grade'] = ($t = $model_member->getOneMemberGrade($value['member_exppoints'], false, $member_grade))?$t['level_name']:'';
            $param['available_predeposit'] = wtPriceFormat($value['available_predeposit']);
            $param['freeze_predeposit'] = wtPriceFormat($value['freeze_predeposit']);
            $param['available_rc_balance'] = wtPriceFormat($value['available_rc_balance']);
            $param['freeze_rc_balance'] = wtPriceFormat($value['freeze_rc_balance']);
            $param['inform_allow'] = $value['inform_allow'] ==  '1' ? '<span class="yes"><i class="fa fa-check-bbs"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_buy'] = $value['is_buy'] ==  '1' ? '<span class="yes"><i class="fa fa-check-bbs"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $param['is_allowtalk'] = $value['is_allowtalk'] ==  '1' ? '<span class="yes"><i class="fa fa-check-bbs"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
	    $param['member_state'] = $value['member_state'] ==  '1' ? '<span class="yes"><i class="fa fa-check-bbs"></i>是</span>' : '<span class="no"><i class="fa fa-ban"></i>否</span>';
            $data['list'][$value['member_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 性别
     * @return multitype:string
     */
    private function get_sex() {
        $array = array();
        $array[1] = '男';
        $array[2] = '女';
        $array[3] = '保密';
        return $array;
    }
    /**
     * csv导出
     */
    public function export_csvWt() {
        $model_member = Model('member');
        $condition = array();
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['member_id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        $order = '';
        $param = array('member_id','member_name','member_avatar','member_email','member_mobile','member_sex','member_truename','member_birthday'
                ,'member_time','member_login_time','member_login_ip','member_points','member_exppoints','member_grade','available_predeposit'
                ,'freeze_predeposit','available_rc_balance','freeze_rc_balance','inform_allow','is_buy','is_allowtalk','member_state'
        );
        if (in_array($_GET['sortname'], $param) && in_array($_GET['sortorder'], array('asc', 'desc'))) {
            $order = $_GET['sortname'] . ' ' . $_GET['sortorder'];
        }
        if (!is_numeric($_GET['curpage'])){
            $count = $model_member->getMemberCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl',urlAdminShop('member'));
				Tpl::setDirquna('shop');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $member_list = $model_member->getMemberList($condition, '*', null, $order, $limit);
        $this->createCsv($member_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($member_list) {
        $model_member = Model('member');
        $member_grade = $model_member->getMemberGradeArr();
        // 性别
        $sex_array = $this->get_sex();
        $data = array();
        foreach ($member_list as $value) {
            $param = array();
            $param['member_id'] = $value['member_id'];
            $param['member_name'] = $value['member_name'];
            $param['member_avatar'] = getMemberAvatarForID($value['member_id']);
            $param['member_email'] = $value['member_email'];
            $param['member_mobile'] = $value['member_mobile'];
            $param['member_sex'] = $sex_array[$value['member_sex']];
            $param['member_truename'] = $value['member_truename'];
            $param['member_birthday'] = $value['member_birthday'];
            $param['member_time'] = date('Y-m-d', $value['member_time']);
            $param['member_login_time'] = date('Y-m-d', $value['member_login_time']);
            $param['member_login_ip'] = $value['member_login_ip'];
            $param['member_points'] = $value['member_points'];
            $param['member_exppoints'] = $value['member_exppoints'];
            $param['member_grade'] = ($t = $model_member->getOneMemberGrade($value['member_exppoints'], false, $member_grade))?$t['level_name']:'';
            $param['available_predeposit'] = wtPriceFormat($value['available_predeposit']);
            $param['freeze_predeposit'] = wtPriceFormat($value['freeze_predeposit']);
            $param['available_rc_balance'] = wtPriceFormat($value['available_rc_balance']);
            $param['freeze_rc_balance'] = wtPriceFormat($value['freeze_rc_balance']);
            $param['inform_allow'] = $value['inform_allow'] ==  '1' ? '是' : '否';
            $param['is_buy'] = $value['is_buy'] ==  '1' ? '是' : '否';
            $param['is_allowtalk'] = $value['is_allowtalk'] ==  '1' ? '是' : '否';
            $param['member_state'] = $value['member_state'] ==  '1' ? '是' : '否';
            $data[$value['member_id']] = $param;
        }

        $header = array(
                'member_id' => '会员ID',
                'member_name' => '会员名称',
                'member_avatar' => '会员头像',
                'member_email' => '会员邮箱',
                'member_mobile' => '会员手机',
                'member_sex' => '会员性别',
                'member_truename' => '真实姓名',
                'member_birthday' => '出生日期',
                'member_time' => '注册时间',
                'member_login_time' => '最后登录时间',
                'member_login_ip' => '最后登录IP',
                'member_points' => '会员积分',
                'member_exppoints' => '会员经验',
                'member_grade' => '会员等级',
                'available_predeposit' => '可用预存款(元)',
                'freeze_predeposit' => '冻结预存款(元)',
                'available_rc_balance' => '可用充值卡(元)',
                'freeze_rc_balance' => '冻结充值卡(元)',
                'inform_allow' => '允许举报',
                'is_buy' => '允许购买',
                'is_allowtalk' => '允许咨询',
                'member_state' => '允许登录'
        );
       array_unshift($data, $header);
		$csv = new Csv();
	    $export_data = $csv->charset($data,CHARSET,'gbk');
	    $csv->filename = $csv->charset('member_list',CHARSET).$_GET['curpage'] . '-'.date('Y-m-d');
	    $csv->export($data);   
    }
	/**
	 * 添加余额控件
	 */
	public function predeposit_addWt(){
		if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["member_id"], "require"=>"true", "message"=>Language::get('admin_points_member_error_again')),
				array("input"=>$_POST["pointsnum"], "require"=>"true",'validator'=>'Compare','operator'=>' >= ','to'=>1,"message"=>Language::get('admin_points_points_min_error'))
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error,'','','error');
			}
			
			$money = abs(floatval($_POST['pointsnum']));
			$memo=trim($_POST['pointsdesc']);
		    if ($money <= 0) {
		       showMessage('输入的金额必需大于0','','html','error');
		    }
			//查询会员信息
			$obj_member = Model('member');
			$member_id = intval($_POST['member_id']);
			$member_info = $obj_member->getMemberInfo(array('member_id'=>$member_id));

			if (!is_array($member_info) || count($member_info)<=0){
				showMessage(Language::get('admin_points_userrecord_error'),urlAdminShop('member','predeposit_add'),'','error');
			}
			$available_predeposit=floatval($member_info['available_predeposit']);
			$freeze_predeposit=floatval($member_info['freeze_predeposit']);
			if ($_POST['operatetype'] == 2 && $money > $available_predeposit){
				showMessage(('预存款不足，会员当前预存款').$available_predeposit,urlAdminShop('member','predeposit_add'),'','error');
			}
			if ($_POST['operatetype'] == 3 && $money > $available_predeposit){
				showMessage(('可冻结预存款不足，会员当前预存款').$available_predeposit,urlAdminShop('member','predeposit_add'),'','error');
			}
			if ($_POST['operatetype'] == 4 && $money > $freeze_predeposit){
				showMessage(('可恢复冻结预存款不足，会员当前冻结预存款').$freeze_predeposit,urlAdminShop('member','predeposit_add'),'','error');
			}
			$model_pd = Model('predeposit');
			$order_sn = $model_pd->makeSn();
			$admininfo = $this->getAdminInfo();
			$log_msg = "操作会员[".$member_info['member_name']."]预存款，金额为".$money.",编号为".$order_sn;
			$admin_act="sys_add_money";
			switch ($_POST['operatetype'])
			{
				case 1:
					$admin_act="sys_add_money";
					$log_msg = "操作会员[".$member_info['member_name']."]预存款[增加]，金额为".$money.",编号为".$order_sn;
					break;  
				case 2:
					$admin_act="sys_del_money";
					$log_msg = "操作会员[".$member_info['member_name']."]预存款[减少]，金额为".$money.",编号为".$order_sn;
				    break;  
				case 3:
					$admin_act="sys_freeze_money";
					$log_msg = "操作会员[".$member_info['member_name']."]预存款[冻结]，金额为".$money.",编号为".$order_sn;
					break;  
				case 4:
					$admin_act="sys_unfreeze_money";
					$log_msg = "操作会员[".$member_info['member_name']."]预存款[解冻]，金额为".$money.",编号为".$order_sn;
					break;
				default:
					showMessage('操作失败',urlAdminShop('member','predeposit_add'));
					break;  
			}
			try {
				$model_pd->beginTransaction();
				//扣除冻结的预存款
				$data = array();
				$data['member_id'] = $member_info['member_id'];
				$data['member_name'] = $member_info['member_name'];
				$data['amount'] = $money;
				$data['order_sn'] = $order_sn;
				$data['admin_name'] = $admininfo['name'];
				$data['pdr_sn'] = $order_sn;
				$data['lg_desc'] = $memo;
				$model_pd->changePd($admin_act,$data);
				$model_pd->commit();
				$this->log($log_msg,1);
				showMessage('操作成功',urlAdminShop('member','predeposit_add'));
			} catch (Exception $e) {
				$model_pd->rollback();
				$this->log($log_msg,0);
				showMessage($e->getMessage(),urlAdminShop('member','predeposit_add'),'html','error');
			}
		}else{
		   //输出子菜单
          Tpl::output('top_link',$this->sublink($this->links,'predeposit_add'));
		  Tpl::setDirquna('shop');
		  Tpl::showpage('member.predeposit.add');
		}
	}

	//取得会员信息
	public function checkmemberWt(){
		$name = trim($_GET['name']);
		if (!$name){
			echo ''; die;
		}
		/**
		 * 转码
		 */
		if(strtoupper(CHARSET) == 'GBK'){
			$name = Language::getGBK($name);
		}
		$obj_member = Model('member');
		$member_info = $obj_member->getMemberInfo(array('member_name'=>$name));
		if (is_array($member_info) && count($member_info)>0){
			if(strtoupper(CHARSET) == 'GBK'){
				$member_info['member_name'] = Language::getUTF8($member_info['member_name']);
			}
			echo json_encode(array('id'=>$member_info['member_id'],'name'=>$member_info['member_name'],'available_predeposit'=>$member_info['available_predeposit'],'freeze_predeposit'=>$member_info['freeze_predeposit']));
		}else {
			echo ''; die;
		}
	}

	public function realverifyWt(){
		//输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'realverify'));
		Tpl::setDirquna('shop');
        Tpl::showpage('member.realverify');
		
	}
	
	public function get_rv_xmlWt(){
		$model_member_realverify = Model('member_realverify');
		
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = 'id desc';
       
        $page = $_POST['rp'];
        $member_list = $model_member_realverify->getList($condition, $page,'', $order);

        $data = array();
        $data['now_page'] = $model_member_realverify->shownowpage();
        $data['total_num'] = $model_member_realverify->gettotalnum();
        foreach ($member_list as $value) {
            $param = array();
            if(intval($value['state'])==0) {
                $operation = "<a class='btn orange' href=\"".urlAdminShop('member','realverify_detail',array('id'=>$value['id']))."\"><i class=\"fa fa-check-bbs\"></i>审核</a>";
            } else {
                $operation = "<a class='btn green' href=\"".urlAdminShop('member','realverify_detail',array('id'=>$value['id']))."\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            }
            $param['operation'] = $operation;
			$param['member_id'] = $value['member_id'];
            $param['truename'] = $value['truename'];
            $param['cardid'] = $value['cardid'];
            $param['state'] = $value['state'];
            $param['addtime_txt'] = ($value['addtime']==0)?'---':date('Y-m-d H:i:s',$value['addtime']);
			$param['verify_time'] = ($value['verify_time']==0)?'---':date('Y-m-d H:i:s',$value['verify_time']);
            $data['list'][$value['id']] = $param;
        
        }
        echo Tpl::flexigridXML($data);exit();
		
	}
	
	public function rverify_delWt(){
		if (!empty($_GET['id'])){
			$model_member_realverify = Model('member_realverify');
			$condition = array();
			$condition['id'] = intval($_GET['id']);
			$condition['state'] = 0;
			$model_member_realverify->del($condition);
			showMessage('删除成功',urlAdminShop('member','realverify'));
		}else {
			showMessage('删除失败',urlAdminShop('member','realverify'));
			
		}
		
	}
	
	public function realverify_detailWt(){
		$model_member_realverify = Model('member_realverify');
        $joinin_detail = $model_member_realverify->getInfo(array('id'=>$_GET['id']));
        $joinin_detail_title = '查看';
        if(intval($joinin_detail['state'])==0) {
            $joinin_detail_title = '审核';
        }
      
        Tpl::output('joinin_detail_title', $joinin_detail_title);
        Tpl::output('joinin_detail', $joinin_detail);
	
		Tpl::setDirquna('shop');
		Tpl::showpage('realverify.detail');
	}

    /**
     * 审核
     */
    public function real_verifyWt() {
        $model_member_realverify = Model('member_realverify');
        $joinin_detail = $model_member_realverify->getInfo(array('id'=>$_POST['id']));
		if(empty($joinin_detail)){
			 showMessage('信息不存在');
		}
		if($_POST['verify_type'] === 'pass'){
			$param = array();
			$param['state'] = 1;
			$param['verify_time'] = time();
			$ret = $model_member_realverify->edit(array('id'=>$_POST['id']),$param);
			if ($ret &&$param['state']===1) {
				Model('member')-> editMember(array('member_id'=>$joinin_detail['member_id']), array('is_realverify'=>1));
				//发送用户消息
				$param = array();
				$param['code'] = 'realname_chekc_success';
				$param['member_id'] = $joinin_detail['member_id'];
				$param['param'] = array();
				
				QueueClient::push('sendMemberMsg', $param);
			}
			showMessage('申请审核通过',urlAdminShop('member','realverify'));
		}else{
			$ret = $model_member_realverify->del(array('id'=>$_POST['id'],'state'=>0));
			Model('member')-> editMember(array('member_id'=>$joinin_detail['member_id']), array('is_realverify'=>0));
			//发送用户消息
				$param = array();
				$param['code'] = 'realname_chekc_error';
				$param['member_id'] = $joinin_detail['member_id'];
				$param['param'] = array();
				
				QueueClient::push('sendMemberMsg', $param);
			showMessage('操作成功',urlAdminShop('member','realverify'));
		}
    }	

}
