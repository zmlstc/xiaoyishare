<?php
/**
 * 店铺管理界面 V6.5
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class storeControl extends SystemControl{
    const EXPORT_SIZE = 1000;

    private $_links = array(
        array('url'=>'store/store','text'=>'管理'),
        array('url'=>'store/store_joinin','text'=>'开店申请')
    );

    public function __construct(){
        parent::__construct();
        Language::read('store,store_grade');
		
    }

    public function indexWt() {
        $this->storeWt();
    }

    /**
     * 店铺
     */
    public function storeWt(){


        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'store'));
		Tpl::setDirquna('agents');

        Tpl::showpage('store.index');
    }



    /**
     * 输出XML数据
     */
    public function get_xmlWt() {
        $model_store = Model('store');
		$admininfo = $this->getAdminInfo();
       
        $condition = array();
		$condition['agent_id'] = $admininfo['id'];
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_GET['seller_name'] != '') {
            $condition['seller_name'] = array('like', '%' . $_GET['seller_name'] . '%');
        }
       
        if ($_GET['store_state'] != '') {
            $condition['store_state'] = $_GET['store_state'];
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('store_id','store_name','seller_name','store_time','store_end_time','store_state','sc_id');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
                $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        //店铺列表
        $store_list = $model_store->getStoreList($condition, $page, $order);



        //店铺分类
/*         $model_store_class = Model('store_class');
        $class_list = $model_store_class->getStoreClassList(array(),'',false);
        $class_array = array();
        if (!empty($class_list)) {
            foreach ($class_list as $v) {
                $class_array[$v['sc_id']] = $v['sc_name'];
            }
        } */

        $data = array();
        $data['now_page'] = $model_store->shownowpage();
        $data['total_num'] = $model_store->gettotalnum();
        foreach ($store_list as $value) {
            $param = array();
            //$store_state = $this->getStoreState($value);
            $operation = "<a class='btn green' href='".urlAgentAgents('store','store_joinin_detail',array('sj_id'=>$value['join_id']))."'><i class='fa fa-list-alt'></i>查看</a>";
           $operation .= "<a class='btn green' href='".urlAgentAgents('store','store_edit',array('store_id'=>$value['store_id']))."'><i class='fa fa-list-alt'></i>编辑状态</a>";
           
            $param['operation'] = $operation;
            $param['store_id'] = $value['store_id'];
            $store_name = "<a class='open' href='#'>";
          
            $store_name .= $value['store_name'] . "<i class='fa fa-external-link ' title='新窗口打开'></i></a>";
            $param['store_name'] = $store_name;
           
            $param['seller_name'] = $value['seller_name'];
            $param['store_avatar'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getStoreLogo($value['store_avatar']).">\")'><i class='fa fa-picture-o'></i></a>";
            //$param['store_label'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getStoreLogo($value['store_label'], 'store_logo').">\")'><i class='fa fa-picture-o'></i></a>";
           
            //$param['store_time'] = date('Y-m-d', $value['store_time']);
            //$param['store_end_time'] = $value['store_end_time']?date('Y-m-d', $value['store_end_time']):L('no_limit');
            $param['store_state'] = $value['store_state']?L('open'):L('close');
            $param['sc_id'] = $value['scinfo'];
            $param['area_info'] = $value['area_info'];
            $param['store_address'] = $value['store_address'];
           
            $param['store_phone'] = $value['store_phone'];
            $data['list'][$value['store_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }
    /**
     * 店铺编辑
     */
    public function store_editWt(){
        $lang = Language::getLangContent();
		$admininfo = $this->getAdminInfo();

        $model_store = Model('store');
        //保存
        if (chksubmit()){
        
            $update_array = array();
            $update_array['store_state'] = intval($_POST['store_state']);
            
            $result = $model_store->editStore($update_array, array('store_id' => $_POST['store_id'],'agent_id'=>$admininfo['id']));
            if ($result){
                $url = array(
                array(
                'url'=>urlAgentAgents('store','store'),
                'msg'=>$lang['back_store_list'],
                ),
                );
                $this->log(L('wt_edit,store').'['.$_POST['store_name'].']',1);
                showMessage($lang['wt_common_save_succ'],$url);
            }else {
                $this->log(L('wt_edit,store').'['.$_POST['store_name'].']',1);
                showMessage($lang['wt_common_save_fail']);
            }
        }
        //取店铺信息
        $store_array = $model_store->getStoreInfoByID($_GET['store_id']);
        if (empty($store_array)||$store_array['agent_id']!=$admininfo['id']){
            showMessage($lang['store_no_exist']);
        }
       
        Tpl::output('store_array',$store_array);

		Tpl::setDirquna('agents');
        Tpl::showpage('store.edit');
    }




    /**
     * csv导出
     */
    public function export_csvWt() {
        $model_store = Model('store');
		
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['agent_id'] = $admininfo['id'];
        $limit = false;
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['store_id'] = array('in', $id_array);
        }
        if ($_GET['store_name'] != '') {
            $condition['store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if ($_GET['member_name'] != '') {
            $condition['member_name'] = array('like', '%' . $_GET['member_name'] . '%');
        }
        if ($_GET['seller_name'] != '') {
            $condition['seller_name'] = array('like', '%' . $_GET['seller_name'] . '%');
        }
        if ($_GET['grade_id'] != '') {
            $condition['grade_id'] = $_GET['grade_id'];
        }
        if ($_GET['store_state'] != '') {
            $condition['store_state'] = $_GET['store_state'];
        }
        if ($_REQUEST['query'] != '') {
            $condition[$_REQUEST['qtype']] = array('like', '%' . $_REQUEST['query'] . '%');
        }
        $order = '';
        $param = array('store_id','store_name','member_name','seller_name','store_time','store_end_time','store_state','grade_id','sc_id');
        if (in_array($_REQUEST['sortname'], $param) && in_array($_REQUEST['sortorder'], array('asc', 'desc'))) {
            $order = $_REQUEST['sortname'] . ' ' . $_REQUEST['sortorder'];
        }
        if (!is_numeric($_GET['curpage'])){
            $count = $model_store->getStoreCount($condition);
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $array = array();
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','store/index');
				Tpl::setDirquna('agents');
                Tpl::showpage('export.excel');
                exit();
            }
        } else {
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = $limit1 .','. $limit2;
        }

        $store_list = $model_store->getStoreList($condition, null, 'store_id desc', '*', $limit);
        $this->createCsv($store_list);
    }
    /**
     * 生成csv文件
     */
    private function createCsv($store_list) {

        //店铺分类
        $model_store_class = Model('store_class');
        $class_list = $model_store_class->getStoreClassList(array(),'',false);
        $class_array = array();
        if (!empty($class_list)) {
            foreach ($class_list as $v) {
                $class_array[$v['sc_id']] = $v['sc_name'];
            }
        }

        $data = array();
        foreach ($store_list as $value) {
            $param = array();
            $param['store_id'] = $value['store_id'];
            $param['store_name'] = $value['store_name'];
            $param['seller_name'] = $value['seller_name'];
            $param['store_avatar'] = getStoreLogo($value['store_avatar']);
            $param['store_label'] = getStoreLogo($value['store_label'], 'store_logo');
            $param['store_time'] = date('Y-m-d', $value['store_time']);
            $param['store_end_time'] = $value['store_end_time']?date('Y-m-d', $value['store_end_time']):L('no_limit');
            $param['store_state'] = $value['store_state']?L('open'):L('close');
            $param['sc_id'] = $class_array[$value['sc_id']];
            $param['area_info'] = $value['area_info'];
            $param['store_address'] = $value['store_address'];
            $param['store_phone'] = $value['store_phone'];
            $data[$value['store_id']] = $param;
        }

        $header = array(
                'store_id' => '店铺ID',
                'store_name' => '店铺名称',
                'seller_name' => '商家账号',
                'store_avatar' => '店铺头像',
                'store_label' => '店铺LOGO',
                'store_time' => '开店时间',
                'store_end_time' => '到期时间',
                'store_state' => '当前状态',
                'sc_id' => '店铺分类',
                'area_info' => '所在地区',
                'store_address' => '详细地址',
                'store_phone' => '商家电话'
        );
        array_unshift($data, $header);
		$csv = new Csv();
	    $export_data = $csv->charset($data,CHARSET,'gbk');
	    $csv->filename = $csv->charset('store_list',CHARSET).$_GET['curpage'] . '-'.date('Y-m-d');
	    $csv->export($data);	
    }

    /**
     * 获得店铺状态
     *  open\正常
     *  close\关闭
     *  expire\即将到期
     *  expired\过期
     */
    private function getStoreState($store_info) {
        $result = 'open';
      /*   if (intval($store_info['store_state']) === 1) {
            $store_end_time = intval($store_info['store_end_time']);
            if ($store_end_time > 0) {
                if ($store_end_time < TIMESTAMP) {
                    $result = 'expired';
                } elseif (($store_end_time - 864000) < TIMESTAMP) {
                    //距离到期10天
                    $result = 'expire';
                }
            }
        } else {
            $result = 'close';
        } */
        return $result;
    }


    /**
     * 编辑保存注册信息
     */
/*     public function edit_save_joininWt() {
        if (chksubmit()) {
            $member_id = $_POST['member_id'];
            if ($member_id <= 0) {
                showMessage(L('param_error'));
            }
            $param = array();
            $param['company_name'] = $_POST['company_name'];
            $province_id = intval($_POST['province_id']);
            if ($province_id > 0) {
                $param['company_province_id'] = $province_id;
            }
            $param['company_address'] = $_POST['company_address'];
            $param['company_address_detail'] = $_POST['company_address_detail'];
            $param['company_phone'] = $_POST['company_phone'];
            $param['company_employee_count'] = intval($_POST['company_employee_count']);
            $param['company_registered_capital'] = intval($_POST['company_registered_capital']);
            $param['contacts_name'] = $_POST['contacts_name'];
            $param['contacts_phone'] = $_POST['contacts_phone'];
            $param['contacts_email'] = $_POST['contacts_email'];
            $param['business_licence_number'] = $_POST['business_licence_number'];
            $param['business_licence_address'] = $_POST['business_licence_address'];
            $param['business_licence_start'] = $_POST['business_licence_start'];
            $param['business_licence_end'] = $_POST['business_licence_end'];
            $param['business_sphere'] = $_POST['business_sphere'];
            if ($_FILES['business_licence_number_elc']['name'] != '') {
                $param['business_licence_number_elc'] = $this->upload_image('business_licence_number_elc');
            }
            $param['organization_code'] = $_POST['organization_code'];
            if ($_FILES['organization_code_electronic']['name'] != '') {
                $param['organization_code_electronic'] = $this->upload_image('organization_code_electronic');
            }
            if ($_FILES['general_taxpayer']['name'] != '') {
                $param['general_taxpayer'] = $this->upload_image('general_taxpayer');
            }
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];
            $param['bank_name'] = $_POST['bank_name'];
            $param['bank_code'] = $_POST['bank_code'];
            $param['bank_address'] = $_POST['bank_address'];
            if ($_FILES['bank_licence_electronic']['name'] != '') {
                $param['bank_licence_electronic'] = $this->upload_image('bank_licence_electronic');
            }
            $param['settlement_bank_account_name'] = $_POST['settlement_bank_account_name'];
            $param['settlement_bank_account_number'] = $_POST['settlement_bank_account_number'];
            $param['settlement_bank_name'] = $_POST['settlement_bank_name'];
            $param['settlement_bank_code'] = $_POST['settlement_bank_code'];
            $param['settlement_bank_address'] = $_POST['settlement_bank_address'];
            $param['tax_registration_certificate'] = $_POST['tax_registration_certificate'];
            $param['taxpayer_id'] = $_POST['taxpayer_id'];
            if ($_FILES['tax_registration_certif_elc']['name'] != '') {
                $param['tax_registration_certif_elc'] = $this->upload_image('tax_registration_certif_elc');
            }
            $result = Model('store_joinin')->editStoreJoinin(array('member_id' => $member_id), $param);
            if ($result) {
                showMessage(L('wt_common_op_succ'), 'index.php?w=store&t=store');
            } else {
                showMessage(L('wt_common_op_fail'));
            }
        }
    }
 */


    public function wtshop_addWt()
    {
       /*  if (chksubmit())
        {
            $memberName = $_POST['member_name'];
            $memberPasswd = (string) $_POST['member_passwd'];

            if (strlen($memberName) < 3 || strlen($memberName) > 15
                || strlen($_POST['seller_name']) < 3 || strlen($_POST['seller_name']) > 15)
                showMessage('账号名称必须是3~15位', '', 'html', 'error');

            if (strlen($memberPasswd) < 6)
                showMessage('登录密码不能短于6位', '', 'html', 'error');

            if (!$this->checkMemberName($memberName))
                showMessage('店主账号已被占用', '', 'html', 'error');

            if (!$this->checkSellerName($_POST['seller_name']))
                showMessage('店主卖家账号名称已被其它店铺占用', '', 'html', 'error');

            try
            {
                $memberId = model('member')->addMember(array(
                    'member_name' => $memberName,
                    'member_passwd' => $memberPasswd,
                    'member_email' => '',
                ));
            }
            catch (Exception $ex)
            {
                showMessage('店主账号新增失败', '', 'html', 'error');
            }

            $storeModel = model('store');

            $saveArray = array();
            $saveArray['store_name'] = $_POST['store_name'];
            $saveArray['member_id'] = $memberId;
            $saveArray['member_name'] = $memberName;
            $saveArray['seller_name'] = $_POST['seller_name'];
            $saveArray['bind_all_gc'] = 1;
            $saveArray['store_state'] = 1;
            $saveArray['store_time'] = time();
            $saveArray['is_own_shop'] = 0;

            $storeId = $storeModel->addStore($saveArray);

            model('seller')->addSeller(array(
                'seller_name' => $_POST['seller_name'],
                'member_id' => $memberId,
                'store_id' => $storeId,
                'seller_group_id' => 0,
                'is_admin' => 1,
            ));
			model('store_joinin')->save(array(
                'seller_name' => $_POST['seller_name'],
				'store_name'  => $_POST['store_name'],
				'member_name' => $memberName,
                'member_id' => $memberId,
				'joinin_state' => 40,
				'company_province_id' => 0,
				'sc_bail' => 0,
				'joinin_year' => 1,
            ));

            // 添加相册默认
            $album_model = Model('album');
            $album_arr = array();
            $album_arr['aclass_name'] = '默认相册';
            $album_arr['store_id'] = $storeId;
            $album_arr['aclass_des'] = '';
            $album_arr['aclass_sort'] = '255';
            $album_arr['aclass_cover'] = '';
            $album_arr['upload_time'] = time();
            $album_arr['is_default'] = '1';
            $album_model->addClass($album_arr);

            //插入店铺扩展表
            $model = Model();
            $model->table('store_extend')->insert(array('store_id'=>$storeId));

            // 删除自营店id缓存
            Model('store')->dropCachedOwnShopIds();

            $this->log("新增外驻店铺: {$saveArray['store_name']}");
            showMessage('操作成功','index.php?w=store&t=store');
            return;
        }
		Tpl::setDirquna('agents');

        Tpl::showpage('store.add'); */
    }
	 public function check_seller_nameWt()
    {
        echo json_encode($this->checkSellerName($_GET['seller_name'], $_GET['id']));
        exit;
    }

    private function checkSellerName($sellerName, $storeId = 0)
    {
        // 判断store_joinin是否存在记录
        $count = (int) Model('store_joinin')->getStoreJoininCount(array(
            'seller_name' => $sellerName,
        ));
        if ($count > 0)
            return false;

        $seller = Model('seller')->getSellerInfo(array(
            'seller_name' => $sellerName,
        ));

        if (empty($seller))
            return true;

        if (!$storeId)
            return false;

        if ($storeId == $seller['store_id'] && $seller['seller_group_id'] == 0 && $seller['is_admin'] == 1)
            return true;

        return false;
    }

    public function check_member_nameWt()
    {
        echo json_encode($this->checkMemberName($_GET['member_name']));
        exit;
    }

    private function checkMemberName($memberName)
    {
        // 判断store_joinin是否存在记录
        $count = (int) Model('store_joinin')->getStoreJoininCount(array(
            'member_name' => $memberName,
        ));
        if ($count > 0)
            return false;

        return ! Model('member')->getMemberCount(array(
            'member_name' => $memberName,
        ));
    }
    /**
     * 店铺 待审核列表
     */
    public function store_joininWt(){

        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->_links,'store_joinin'));
		Tpl::setDirquna('agents');

        Tpl::showpage('store_joinin');
    }

    /**
     * 输出XML数据
     */
    public function get_joinin_xmlWt() {
        $model_store_joinin = Model('store_joinin');
        // 设置页码参数名称
        
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['agent_id'] = $admininfo['id'];
        $condition['joinin_state'] = array('gt',0);
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('sg_id', 'paying_amount','shop_phone', 'joinin_state', 'joinin_year', 'contacts_name', 'contacts_phone'
                ,'contacts_email', 'company_name', 'company_province_id', 'company_phone'
        );
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        //店铺列表
        $store_list = $model_store_joinin->getList($condition, $page, $order);

        // 开店状态
        $joinin_state_array = $this->get_store_joinin_state();

        $data = array();
        $data['now_page'] = $model_store_joinin->shownowpage();
        $data['total_num'] = $model_store_joinin->gettotalnum();
        foreach ($store_list as $value) {
            $param = array();
            
			if(in_array(intval($value['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
                $operation = "<a class='btn orange' href=\"".urlAgentAgents('store','store_joinin_detail',array('sj_id'=>$value['sj_id']))."\"><i class=\"fa fa-check-bbs\"></i>审核</a>";
            } else {
                $operation = "<a class='btn green' href=\"".urlAgentAgents('store','store_joinin_detail',array('sj_id'=>$value['sj_id']))."\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            }
            $param['operation'] = $operation;
			$param['shop_phone'] = $value['shop_phone'];
            $param['joinin_state'] = $joinin_state_array[$value['joinin_state']];
            $param['company_user_name'] = $value['company_user_name'];
            $param['contacts_phone'] = $value['contacts_phone'];
            $param['company_name'] = $value['company_name'];
            $param['company_province_id'] = $value['company_address'] . ' ' . $value['company_address_detail'];
            $param['company_phone'] = $value['company_phone'];
            $param['addtime_txt'] = ($value['addtime']==0)?'---':date('Y-m-d H:i:s',$value['addtime']);
            $data['list'][$value['sj_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 审核
     */
    public function store_joinin_verifyWt() {
        $model_store_joinin = Model('store_joinin');
		$admininfo = $this->getAdminInfo();
        $joinin_detail = $model_store_joinin->getOne(array('sj_id'=>$_POST['sj_id'],'agent_id'=>$admininfo['id']));

        switch (intval($joinin_detail['joinin_state'])) {
            case STORE_JOIN_STATE_NEW:
                $this->store_joinin_verify_pass($joinin_detail);
                break;
            case STORE_JOIN_STATE_PAY:
                $this->store_joinin_verify_open($joinin_detail,$joinin_detail['paying_amount']);
                break;
            default:
                showMessage('参数错误','');
                break;
        }
    }

    private function store_joinin_verify_pass($joinin_detail) {
        $param = array();
        $param['joinin_state'] = $_POST['verify_type'] === 'pass' ? STORE_JOIN_STATE_VERIFY_SUCCESS : STORE_JOIN_STATE_VERIFY_FAIL;
        $param['joinin_message'] = $_POST['joinin_message'];
        $param['paying_amount'] = 0;
        $model_store_joinin = Model('store_joinin');
        $model_store_joinin->modify($param, array('sj_id'=>$_POST['sj_id']));
        if ($param['paying_amount'] > 0) {
            showMessage('店铺入驻申请审核完成',urlAgentAgents('store','store_joinin'));
        } else {
            //如果开店支付费用为零，则审核通过后直接开通，无需再上传付款凭证
            $this->store_joinin_verify_open($joinin_detail,$param['paying_amount']);
        }
    }

    private function store_joinin_verify_open($joinin_detail,$paying_amount = 0) {
        $model_store_joinin = Model('store_joinin');
        $model_store    = Model('store');
        $model_seller = Model('seller');

        //验证商家账号是否已经存在
        if($model_seller->isSellerExist(array('seller_name' => $joinin_detail['seller_name']))) {
            showMessage('商家账号已存在','');
        }

        $param = array();
        $param['joinin_state'] = $_POST['verify_type'] === 'pass' ? STORE_JOIN_STATE_FINAL : ($paying_amount > 0 ? STORE_JOIN_STATE_PAY_FAIL : STORE_JOIN_STATE_VERIFY_FAIL);
        $param['joinin_message'] = $_POST['joinin_message'];
        $model_store_joinin->modify($param, array('sj_id'=>$_POST['sj_id']));
        if($_POST['verify_type'] === 'pass') {
            //开店
            $shop_array     = array();
            /* $shop_array['member_id']    = $joinin_detail['member_id'];
            $shop_array['member_name']  = $joinin_detail['member_name']; */
            $shop_array['seller_name'] = $joinin_detail['seller_name'];
            $shop_array['grade_id']     = $joinin_detail['sg_id'];
            $shop_array['store_name']   = $joinin_detail['store_name'];
            $shop_array['sc_id']        = $joinin_detail['sc_id'];
            $shop_array['store_company_name'] = $joinin_detail['company_name'];
            $shop_array['province_id']  = $joinin_detail['company_province_id'];
            $shop_array['area_info']    = $joinin_detail['company_address'];
            $shop_array['store_address']= $joinin_detail['company_address_detail'];
			$shop_array['is_person']= $joinin_detail['is_person'];
            $shop_array['store_zip']    = '';
            $shop_array['store_zy']     = '';
            $shop_array['store_state']  = 1;
            $shop_array['store_time']   = time();
            $shop_array['store_end_time'] = strtotime(date('Y-m-d 23:59:59', strtotime('+1 day'))." +".intval($joinin_detail['joinin_year'])." year");
            
            $shop_array['invite_bm']     = $joinin_detail['invite_bm'];
            $shop_array['scid1']     = $joinin_detail['scid1'];
            $shop_array['scid2']     = $joinin_detail['scid2'];
            $shop_array['scinfo']     = $joinin_detail['scinfo'];
            $shop_array['city_id']     = $joinin_detail['company_city_id'];
            $shop_array['area_id']     = $joinin_detail['company_area_id'];
            $shop_array['join_id']     = $joinin_detail['sj_id'];
            $shop_array['agent_id']     = $joinin_detail['agent_id'];
			
			$store_id = $model_store->addStore($shop_array);

            if($store_id) {
                //写入商家账号
                $seller_array = array();
                $seller_array['seller_name'] = $joinin_detail['seller_name'];
                $seller_array['seller_pwd'] = $joinin_detail['seller_pwd'];
                $seller_array['seller_group_id'] = 0;
                $seller_array['store_id'] = $store_id;
                $seller_array['is_admin'] = 1;
                $state = $model_seller->addSeller($seller_array);
            }

            if($state) {
                // 添加相册默认
                $album_model = Model('album');
                $album_arr = array();
                $album_arr['aclass_name'] = Language::get('store_save_defaultalbumclass_name');
                $album_arr['store_id'] = $store_id;
                $album_arr['aclass_des'] = '';
                $album_arr['aclass_sort'] = '255';
                $album_arr['aclass_cover'] = '';
                $album_arr['upload_time'] = time();
                $album_arr['is_default'] = '1';
                $album_model->addClass($album_arr);

                $model = Model();
                //插入店铺扩展表
                $model->table('store_extend')->insert(array('store_id'=>$store_id));
                $msg = Language::get('store_save_create_success');

              
                showMessage('店铺开店成功',urlAgentAgents('store','store_joinin'));
            } else {
                showMessage('店铺开店失败',urlAgentAgents('store','store_joinin'));
            }
        } else {
            showMessage('店铺开店拒绝',urlAgentAgents('store','store_joinin'));
        }
    }
	
	

    private function getClassApplyState() {
        return array('0' => '审核中', '1' => '已审核', '2' => '自营店');
    }


    

    private function get_store_joinin_state() {
        $joinin_state_array = array(
            STORE_JOIN_STATE_NEW => '新申请',
            STORE_JOIN_STATE_PAY => '已付款',
            STORE_JOIN_STATE_VERIFY_SUCCESS => '待付款',
            STORE_JOIN_STATE_VERIFY_FAIL => '审核失败',
            STORE_JOIN_STATE_PAY_FAIL => '付款审核失败',
            STORE_JOIN_STATE_FINAL => '开店成功',
        );
        return $joinin_state_array;
    }

    /**
     * 店铺续签申请列表
     */
    public function reopen_listWt(){
        Tpl::output('top_link',$this->sublink($this->_links,'reopen_list'));
		Tpl::setDirquna('agents');
        Tpl::showpage('store_reopen.list');
    }

    /**
     * 输出XML数据
     */
    public function get_reopen_xmlWt() {
        $model_store_reopen = Model('store_reopen');
        // 设置页码参数名称
        
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['agent_id'] = $admininfo['id'];
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('re_id', 're_grade_id', 're_grade_price', 're_year', 're_pay_amount', 're_store_id', 're_store_name', 're_state'
                , 're_create_time', 're_start_time', 're_end_time', 're_pay_cert', 're_pay_cert_explain');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }

        $page = $_POST['rp'];

        //店铺列表
        $reopen_list = $model_store_reopen->getStoreReopenList($condition, $page, $order);

        // 续签状态
        $reopen_state_array = $this->getReopenState();

        $data = array();
        $data['now_page'] = $model_store_reopen->shownowpage();
        $data['total_num'] = $model_store_reopen->gettotalnum();
        foreach ($reopen_list as $value) {
            $param = array();
            $operation = '';
            if($value['re_state'] == 1) {
                $operation .= "<a class='btn orange' href=\"javascript:void(0);\" onclick=\"reopen_check('" . $value['re_id'] . "')\"><i class=\"fa fa-check-bbs-o\"></i>审核</a>";
            }
            if ($value['re_state'] != 2) {
                $operation .= "<a class='btn green' href=\"javascript:void(0);\" onclick=\"reopen_del('" . $value['re_id'] . "', '" . $value['re_store_id'] . "')\"><i class=\"fa fa-list-alt\"></i>删除</a>";
            }
            if ($value['re_state'] == 2) {
                $operation .= "<span>--</span>";
            }
            $param['operation'] = $operation;
            $param['re_id'] = $value['re_id'];
            $param['re_grade_id'] = $value['re_grade_name'];
            $param['re_grade_price'] = wtPriceFormat($value['re_grade_price']);
            $param['re_year'] = $value['re_year'];
            $param['re_pay_amount'] = wtPriceFormat($value['re_pay_amount']);
            $param['re_store_id'] = $value['re_store_id'];
            $param['re_store_name'] = $value['re_store_name'];
            $param['re_state'] = $reopen_state_array[$value['re_state']];
            $param['re_create_time'] = date('Y-m-d', $value['re_create_time']);
            $param['re_pay_cert'] = "<a href='".getStoreJoininImageUrl($value['re_pay_cert'])."' target=\"blank\" class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=".getStoreJoininImageUrl($value['re_pay_cert']).">\")'><i class='fa fa-picture-o'></i></a>";
            $param['re_pay_cert_explain'] = $value['re_pay_cert_explain'];
            $param['re_start_time'] = $value['re_start_time'] != '' ? date('Y-m-d', $value['re_start_time']) : '';
            $param['re_end_time'] = $value['re_end_time'] != '' ? date('Y-m-d', $value['re_end_time']) : '';
            $data['list'][$value['re_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    private function getReopenState() {
        return array('0' => '待付款', '1' => '待审核', '2' => '通过审核');
    }


    /**
     * 审核详细页
     */
    public function store_joinin_detailWt(){
        $model_store_joinin = Model('store_joinin');
		
		$admininfo = $this->getAdminInfo();
        $joinin_detail = $model_store_joinin->getOne(array('sj_id'=>$_GET['sj_id']));
        $joinin_detail_title = '查看';
        if(in_array(intval($joinin_detail['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
            $joinin_detail_title = '审核';
        }
        if (!empty($joinin_detail['sg_info'])) {
            $store_grade_info = Model('store_grade')->getOneGrade($joinin_detail['sg_id']);
            $joinin_detail['sg_price'] = $store_grade_info['sg_price'];
        } else {
            $joinin_detail['sg_info'] = @unserialize($joinin_detail['sg_info']);
            if (is_array($joinin_detail['sg_info'])) {
                $joinin_detail['sg_price'] = $joinin_detail['sg_info']['sg_price'];
            }
        }
        Tpl::output('joinin_detail_title', $joinin_detail_title);
        Tpl::output('joinin_detail', $joinin_detail);
	
		Tpl::setDirquna('agents');
		
   
		Tpl::showpage('store_joinin.detail');
		
    }

   


    /**
     * 验证店铺名称是否存在
     */
    public function ckeck_store_nameWt() {
        /**
         * 实例化商家模型
         */
        $where = array();
        $where['store_name'] = $_GET['store_name'];
        $where['store_id'] = array('neq', $_GET['store_id']);
        $store_info = Model('store')->getStoreInfo($where);
        if(!empty($store_info['store_name'])) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
	

}
