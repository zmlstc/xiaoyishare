<?php
/**
 * 商家入驻
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh op wt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 *
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class store_joininControl extends mobileHomeControl
{

    private $joinin_detail = NULL;

    public function __construct()
    {
        parent::__construct();

    }

    public function indexWt()
    {
        Model('area')->getAllAreaForJS();
        echo time();
    }

    /********商家注册**开始***************/
    /**
     * 手机号检测
     *
     * @param
     * @returnstore_joinin
     */
    public function checkmobWt()
    {
        $model_join = Model('store_joinin');

        if (!preg_match('/^0?(13|15|17|18|19|14)[0-9]{9}$/i', $_POST['phone'])) {
            output_error('手机号码不正确！');
        }
        $check_mobile = $model_join->isExist(array('shop_phone' => $_POST['phone']));
        if ($check_mobile) {
            //echo 'false';
            output_error('手机号码已存在');
        } else {
            output_data('1');
        }
    }

    /**
     * 验证手机验证码和邀请码
     */
    public function check_sms_inviteWt()
    {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];
        $invcode = $_POST['invcode'];

        if (!preg_match('/^0?(13|15|17|18|19|14)[0-9]{9}$/i', $phone)) {
            output_error('手机号码不正确！');
        }
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        //$this->connect_output_data($state_data, 1);
        if ($state_data['state']) {
            //验证邀请码

            $invite_userid = getUserInviteCode(trim($invcode));
            if ($invite_userid == '' || intval($invite_userid) < 1) {
                output_error('邀请码不正确');
            }
            /* 	if($invite_userid==$this->member_info['member_id']){
                    output_error('不能自己邀请自己');
                } */

            $invite_member_info = Model('member')->getMemberInfo(array('member_id' => $invite_userid));
            if (empty($invite_member_info) || !is_array($invite_member_info) || $invite_member_info['is_realverify'] != 1) {
                output_error('邀请码不存在');
            }

            $param = array();
            //$param['member_name'] = $this->member_info['member_name'];
            //$param['member_id'] = $this->member_info['member_id'];
            $param['invite_bm'] = $invite_userid;
            $param['shop_phone'] = $_POST['phone'];
            $param['addtime'] = time();

            $model_store_joinin = Model('store_joinin');
            /*   $joinin_info = $model_store_joinin->getOne(array('member_id' => $this->member_info['member_id']));
              if(empty($joinin_info)) { */
            //$param['member_id'] = $this->member_info['member_id'];
            $ret = $model_store_joinin->save($param);
            /* } else {
                $model_store_joinin->modify($param, array('member_id'=>$this->member_info['member_id']));
            } */
            if ($ret) {
                $yz = $ret . '|' . md5($phone . $ret . 'FL#$X890DQ!');
                output_data(array('state' => 1, 'tk' => $yz));
            } else {
                output_error('验证失败！');
            }

        } else {
            output_error($state_data['msg']);
        }
    }

    public function getstoreclassWt()
    {
        $store_model = Model('store_class');
        $list = array();
        $root = $store_model->getStoreClassList(array('parent_id' => 0));
        if (!empty($root) && is_array($root)) {
            foreach ($root as $r) {
                $root2 = $store_model->getStoreClassList(array('parent_id' => $r['sc_id']));
                $_root2 = array();
                if (!empty($root2) && is_array($root2)) {
                    foreach ($root2 as $r2) {
                        $_root2[] = array('value' => $r2['sc_id'], 'name' => $r2['sc_name'], 'rate' => $r2['commis_rate']);
                    }
                }
                $list[] = array('value' => $r['sc_id'], 'name' => $r['sc_name'], 'children' => $_root2);
            }
        }
        output_data($list);

    }

    private function upload_image($file)
    {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH . DS . 'store_joinin' . DS;
        $upload->set('default_dir', $uploaddir);
        $upload->set('allow_type', array('jpg', 'jpeg', 'gif', 'png'));
        if (!empty($_FILES[$file]['name'])) {
            $result = $upload->upfile($file);
            if ($result) {
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return $pic_name;
    }

    public function image_uploadWt()
    {
        $img = $this->upload_image('pic');
        output_data(array('image_name' => $img, 'thumb_name' => UPLOAD_SITE_URL . DS . ATTACH_PATH . DS . 'store_joinin' . DS . $img));
    }

    public function step2Wt()
    {
        if (!empty($_POST)) {
            $model_store_joinin = Model('store_joinin');
            if (empty($_POST['tky']) || trim($_POST['tky']) == '') {
                output_error('参数错误');
            }
            $yzz = explode('|', trim($_POST['tky']));
            if (count($yzz) != 2) {
                output_error('参数错误');
            }
            $sjoin = $model_store_joinin->getOne(array('sj_id' => intval($yzz[0])));
            if (empty($sjoin)) {
                output_error('获取信息失败');
            }
            $_yx = md5(trim($sjoin['shop_phone']) . $sjoin['sj_id'] . 'FL#$X890DQ!');
            if ($_yx != $yzz[1]) {
                output_error('验证信息失败');
            }
            $sj_id = intval($yzz[0]);
            $param = array();
            //$param['member_name'] = $this->member_info['member_name'];
            $param['company_name'] = $_POST['company_name'];
            $param['company_province_id'] = intval($_POST['company_province_id']);
            $param['company_phone'] = $_POST['contacts_phone'];
            $param['contacts_phone'] = $_POST['contacts_phone'];
            $param['company_user_name'] = $_POST['company_user_name'];
            $param['scid1'] = intval($_POST['scid1']);
            $param['scid2'] = intval($_POST['scid2']);
            $param['scinfo'] = $_POST['scinfo'];
            $param['company_user_name_pic'] = $_POST['company_user_name_pic'];
            $param['company_user_name_pic2'] = $_POST['company_user_name_pic2'];
            $param['company_type'] = intval($_POST['company_type']);
            $param['company_city_id'] = intval($_POST['company_city_id']);
            $param['company_area_id'] = intval($_POST['company_area_id']);
            $param['organization_code_electronic'] = $_POST['organization_code_electronic'];
            $param['company_address'] = $_POST['company_address'];
            $param['business_licence_number'] = $_POST['business_licence_number'];

            $agent = Model('agent_area')->getAgentAreaInfo(array('area_id' => intval($_POST['company_area_id'])));
            if (empty($agent) || !is_array($agent)) {
                output_error('该地区暂未开通！');
            }

            $param['agent_id'] = $agent['agent_id'];

            $this->step2_save_valid($param);

            /* $joinin_info = $model_store_joinin->getOne(array('member_id' => $this->member_info['member_id']));
            if(empty($joinin_info)) {
                $param['member_id'] = $this->member_info['member_id'];
                $model_store_joinin->save($param);
            } else { */
            $ret = $model_store_joinin->modify($param, array('sj_id' => $sj_id));
            if ($ret) {
                $yz = $sj_id . '|' . md5(trim($sjoin['shop_phone']) . $sj_id . 'x!FL#$X890DQ!');
                output_data(array('state' => 1, 'tk' => $yz));
            }
            //}
        }
        output_error('数据验证失败！');
        exit;
    }

    private function step2_save_valid($param)
    {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input" => $param['company_name'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "公司名称不能为空且必须小于50个字"),
            array("input" => $param['company_address'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "公司地址不能为空!"),
            array("input" => $param['company_area_id'], "require" => "true", "validator" => "Number", "message" => "公司详细地址不能为空"),
            array("input" => $param['business_licence_number'], "require" => "true", "validator" => "Length", "min" => "18", "max" => "18", "message" => "营业执照号不能为空且必须18个字"),
            array("input" => $param['contacts_phone'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "20", "message" => "联系人电话不能为空"),
            array("input" => $param['scid2'], "require" => "true", "validator" => "Number", "message" => "请选择行业"),
            array("input" => $param['company_type'], "require" => "true", "validator" => "Number", "message" => "请选择企业类型"),
            array("input" => $param['company_user_name_pic'], "require" => "true", "message" => "法人身份证不能为空"),
            array("input" => $param['organization_code_electronic'], "require" => "true", "message" => "营业执照电子版不能为空"),

        );
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error, array('state' => 0));
        }
    }

    public function check_seller_nameWt()
    {
        $condition = array();
        $condition['seller_name'] = $_POST['seller_name'];

        $model_seller = Model('seller');
        $result = $model_seller->isSellerExist($condition);
        if (!$result) {
            $result = Model('store_joinin')->isExist($condition);
        }

        if ($result) {
            output_error('该用登陆名已存在,请换其他名字');
        } else {
            output_data('1');
        }
    }

    public function step4Wt()
    {
        if (empty($_POST['tky']) || trim($_POST['tky']) == '') {
            output_error('参数错误');
        }
        $yzz = explode('|', trim($_POST['tky']));
        if (count($yzz) != 2) {
            output_error('参数错误');
        }
        $sjoin = Model('store_joinin')->getOne(array('sj_id' => intval($yzz[0])));
        if (empty($sjoin)) {
            output_error('获取信息失败');
        }
        $_yx = md5(trim($sjoin['shop_phone']) . $sjoin['sj_id'] . 'x!FL#$X890DQ!');
        if ($_yx != $yzz[1]) {
            output_error('验证信息失败');
        }
        $sj_id = intval($yzz[0]);

        $model_store = Model('store');
        $store_name = $_POST['store_name'];
        $store_info = $model_store->getStoreInfo(array('store_name' => $store_name));
        if (!empty($store_info['store_name'])) {
            output_error('店铺名称已经存在', array('state' => 0));
        }
        $condition = array();
        $condition['seller_name'] = $_POST['seller_name'];
        $model_seller = Model('seller');
        $result = $model_seller->isSellerExist($condition);
        if (!$result) {
            $result = Model('store_joinin')->isExist($condition);
        }

        if ($result) {
            output_error('商家账号已经存在', array('state' => 0));
        }

        $param = array();
        $param['seller_name'] = $_POST['seller_name'];
        $param['store_name'] = $_POST['store_name'];
        $param['seller_pwd'] = md5(trim($_POST['seller_pwd']));
        $param['joinin_year'] = intval($_POST['joinin_year']);
        $param['joinin_state'] = STORE_JOIN_STATE_NEW;

        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input" => $param['store_name'], "require" => "true", "validator" => "Length", "min" => "4", "max" => "20", "message" => "店铺名称不能为空且必须小于20个字"),
            array("input" => $param['seller_pwd'], "require" => "true", "message" => "登陆密码不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error, array('state' => 0));
        }

        $model_store_joinin = Model('store_joinin');


        $ret = $model_store_joinin->modify($param, array('sj_id' => $sj_id));
        if ($ret) {
            output_data(1);
        }
        output_error('入驻验证失败！');

    }


    /*******商家注册**结束**************/


    private function check_joinin_state()
    {
        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id' => $this->member_info['member_id']));
        if (!empty($joinin_detail)) {
            $this->joinin_detail = $joinin_detail;
            switch (intval($joinin_detail['joinin_state'])) {
                case STORE_JOIN_STATE_NEW:

//==================start  ys   新增立即支付   ==========================
                    if (in_array($_GET['t'], array('pay_add'))) {
                        $this->pay_addWt();
                        break;
                    }
//==================end  ys   新增立即支付   ==========================
                    $info = $this->step4();
                    output_data(array('state' => 11, 'info' => $info, 'errinfo' => '入驻申请已经提交，请等待管理员审核'));
                    break;
                case STORE_JOIN_STATE_PAY:
                    output_data(array('state' => 12, 'errinfo' => '已经提交，请等待管理员核对后为您开通店铺'));
                    break;
                case STORE_JOIN_STATE_VERIFY_SUCCESS:
                    if (!in_array($_GET['t'], array('pay', 'pay_save'))) {
                        $info = $this->pay();
                        output_data(array('state' => 13, 'info' => $info, 'errinfo' => '审核通过,请上传支付凭证!'));

                    }

                    break;
                case STORE_JOIN_STATE_VERIFY_FAIL:
                    if (!in_array($_GET['t'], array('step1', 'step2', 'step3', 'step4', 'ajax_upload_image'))) {
                        output_data(array('state' => 0, 'errinfo' => '审核失败:' . $joinin_detail['joinin_message']));
                    }
                    break;
                case STORE_JOIN_STATE_PAY_FAIL:
                    if (!in_array($_GET['t'], array('pay', 'pay_save'))) {
                        output_data(array('state' => 1, 'errinfo' => '付款审核失败:' . $joinin_detail['joinin_message']));
                    }
                    break;
                case STORE_JOIN_STATE_FINAL:
                    output_data(array('state' => 20));
                    break;
            }
        }
    }


    public function step0Wt()
    {
        $model_document = Model('document');
        $document_info = $model_document->getOneByCode('open_store');

        output_data(array('agreement' => $document_info['doc_content']));
        exit;
    }

    public function step1Wt()
    {
        Tpl::output('step', '1');
        Tpl::output('sub_step', 'step1');
        Tpl::showpage('store_joinin_apply');
        exit;
    }


    public function step3Wt()
    {
        if (!empty($_POST)) {
            $param = array();
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];

            $param['bank_licence_electronic'] = $_POST['bank_licence_electronic1'];
            if (!empty($_POST['is_settlement_account'])) {
                $param['is_settlement_account'] = 1;
                $param['settlement_bank_account_name'] = $_POST['bank_account_name'];
                $param['settlement_bank_account_number'] = $_POST['bank_account_number'];

            } else {
                $param['is_settlement_account'] = 2;
                $param['settlement_bank_account_name'] = $_POST['settlement_bank_account_name'];
                $param['settlement_bank_account_number'] = $_POST['settlement_bank_account_number'];
                $param['settlement_bank_name'] = $_POST['settlement_bank_name'];
                $param['settlement_bank_code'] = $_POST['settlement_bank_code'];
                $param['settlement_bank_address'] = $_POST['settlement_bank_address'];

            }
            $this->step3_save_valid($param);
            $model_store_joinin = Model('store_joinin');

            $model_store_joinin->modify($param, array('member_id' => $this->member_info['member_id']));
            output_data(array('state' => '1'));
        }
        exit;
    }

    private function step3_save_valid($param)
    {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input" => $param['bank_account_name'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "银行开户名不能为空且必须小于50个字"),
            array("input" => $param['bank_address'], "require" => "true", "开户行所在地不能为空"),
            array("input" => $param['bank_licence_electronic'], "require" => "true", "开户银行许可证电子版不能为空"),
            array("input" => $param['settlement_bank_account_name'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "银行开户名不能为空且必须小于50个字"),
            array("input" => $param['settlement_bank_name'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "开户银行支行名称不能为空且必须小于50个字"),
            array("input" => $param['settlement_bank_address'], "require" => "true", "开户行所在地不能为空"),
            array("input" => $param['tax_registration_certificate'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "20", "message" => "税务登记证号不能为空且必须小于20个字"),
            array("input" => $param['taxpayer_id'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "20", "message" => "纳税人识别号"),
            array("input" => $param['tax_registration_certif_elc'], "require" => "true", "message" => "税务登记证号电子版不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error, array('state' => 0));
        }
    }

    public function step3_infoWt()
    {
        //店铺等级
        $grade_list = rkcache('store_grade', true);
        $gradelist = array();
        //附加功能
        if (!empty($grade_list) && is_array($grade_list)) {
            foreach ($grade_list as $key => $grade) {
                $sg_function = explode('|', $grade['sg_function']);
                if (!empty($sg_function[0]) && is_array($sg_function)) {
                    foreach ($sg_function as $key1 => $value) {
                        if ($value == 'editor_multimedia') {
                            $grade_list[$key]['function_str'] .= '富文本编辑器';
                        }
                    }
                } else {
                    $grade_list[$key]['function_str'] = '无';
                }
                $goods_limit = empty($grade['sg_goods_limit']) ? '不限' : $grade_list[$key]['sg_goods_limit'];
                $explain = '商品数：' . $goods_limit . ' 模板数：' . $grade_list[$key]['sg_template_number'] . ' 收费标准：' . $grade_list[$key]['sg_price'] . ' 元/年 附加功能：' . $grade_list[$key]['function_str'];
                $grade_list[$key]['show_info'] = $explain;

                $gradelist[] = $grade_list[$key];
            }
        }

        //店铺分类
        $model_store = Model('store_class');
        $store_class = $model_store->getStoreClassList(array(), '', false);
        output_data(array('store_class' => $store_class, 'grade_list' => $gradelist));
    }




//==================start  ys   新增立即支付   ==========================	

    /**
     *立即支付
     */
    public function pay_addWt()
    {

        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id' => $this->member_info['member_id']));
        $joinin_detail['store_class_ids'] = unserialize($joinin_detail['store_class_ids']);
        $joinin_detail['store_class_names'] = unserialize($joinin_detail['store_class_names']);
        $joinin_detail['store_class_commis_rates'] = explode(',', $joinin_detail['store_class_commis_rates']);
        $joinin_detail['sg_info'] = unserialize($joinin_detail['sg_info']);
        $pay_sn = $joinin_detail['pdr_sn'];
        redirect(SHOP_SITE_URL . '/index.php/buy/ruzh_pay?pay_sn=' . $pay_sn);


    }

//==================end  ys   新增立即支付   ==========================


    private function step4_save_valid($param)
    {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input" => $param['store_name'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "店铺名称不能为空且必须小于50个字"),
            array("input" => $param['sg_id'], "require" => "true", "message" => "店铺等级不能为空"),
            array("input" => $param['sc_id'], "require" => "true", "message" => "店铺分类不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error, array('state' => 0));
        }
    }

    public function pay()
    {
        if (!empty($this->joinin_detail['sg_info'])) {
            $store_grade_info = Model('store_grade')->getOneGrade($this->joinin_detail['sg_id']);
            $this->joinin_detail['sg_price'] = $store_grade_info['sg_price'];
        } else {
            $this->joinin_detail['sg_info'] = @unserialize($this->joinin_detail['sg_info']);
            if (is_array($this->joinin_detail['sg_info'])) {
                $this->joinin_detail['sg_price'] = $this->joinin_detail['sg_info']['sg_price'];
            }
        }

        return $this->joinin_detail;

        exit;
    }

    public function pay_saveWt()
    {
        $param = array();
        $param['paying_money_certificate'] = $_POST['paying_money_certificate'];
        $param['paying_money_certif_exp'] = $_POST['paying_money_certif_exp'];
        $param['joinin_state'] = STORE_JOIN_STATE_PAY;

        if (empty($param['paying_money_certificate'])) {
            output_error('请上传付款凭证');
        }

        $model_store_joinin = Model('store_joinin');
        $model_store_joinin->modify($param, array('member_id' => $this->member_info['member_id']));
        output_data(array('state' => 'ok'));

    }

    private function step4()
    {
        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id' => $this->member_info['member_id']));
        $joinin_detail['store_class_ids'] = unserialize($joinin_detail['store_class_ids']);
        $joinin_detail['store_class_names'] = unserialize($joinin_detail['store_class_names']);
        $joinin_detail['store_class_commis_rates'] = explode(',', $joinin_detail['store_class_commis_rates']);
        $joinin_detail['sg_info'] = unserialize($joinin_detail['sg_info']);
        return $joinin_detail;
    }

    private function show_join_message($message, $btn_next = FALSE, $step = '2')
    {
        Tpl::output('joinin_message', $message);
        Tpl::output('btn_next', $btn_next);
        Tpl::output('step', $step);
        Tpl::output('sub_step', 'step4');
        Tpl::showpage('store_joinin_apply');
        exit;
    }

    public function ajax_upload_imageWt()
    {

        $pic_name = '';
        $upload = new UploadFile();
        $file = current($_FILES);
        $uploaddir = ATTACH_PATH . DS . 'store_joinin' . DS;
        $upload->set('max_size', C('image_max_filesize'));
        $upload->set('default_dir', $uploaddir);
        $upload->set('allow_type', array('jpg', 'jpeg', 'gif', 'png'));
        if (!empty($file['tmp_name'])) {
            $result = $upload->upfile(key($_FILES));
            if ($result) {
                echo json_encode(array('state' => true, 'pic_name' => $upload->file_name, 'pic_url' => UPLOAD_SITE_URL . DS . ATTACH_PATH . DS . 'store_joinin' . DS . $upload->file_name));
            } else {
                echo json_encode(array('state' => false, 'message' => $upload->error));
            }
        }
    }

    /*     private function upload_image($file) {
            $pic_name = '';
            $upload = new UploadFile();
            $uploaddir = ATTACH_PATH.DS.'store_joinin'.DS;
            $upload->set('default_dir',$uploaddir);
            $upload->set('allow_type',array('jpg','jpeg','gif','png'));
            if (!empty($_FILES[$file]['name'])){
                $result = $upload->upfile($file);
                if ($result){
                    $pic_name = $upload->file_name;
                    $upload->file_name = '';
                }
            }
            return $pic_name;
        } */

    /**
     * 检查店铺名称是否存在
     *
     * @param
     * @return
     */
    public function checknameWt()
    {
        /**
         * 实例化卖家模型
         */
        $model_store = Model('store');
        $store_name = $_GET['store_name'];
        $store_info = $model_store->getStoreInfo(array('store_name' => $store_name));
        if (!empty($store_info['store_name']) && $store_info['member_id'] != $_SESSION['member_id']) {
            echo 'false';
        } else {
            echo 'true';
        }
    }


    /* 	public function image_uploadWt() {
            $img=$this->upload_image($_POST['name']);
            output_data(array('image_name' => $img,'thumb_name'=>UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'store_joinin'.DS.$img));
        } */
}
