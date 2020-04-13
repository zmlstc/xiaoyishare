<?php
/**
 * 商家入驻
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权

 */



defined('ShopWT') or exit('Access Denied By ShopWT');


class seller_applyControl extends mobileHomeControl
{

    private $member_info = array();

    private $joinin_detail = null;

    public function __construct()
    {
        parent::__construct();

        if (!in_array($_REQUEST['client'], $this->client_type_array)) {
            output_error('非法提交');
        }

        $key = $_POST['key'];
        if (empty($key)) {
            $key = $_GET['key'];
        }
        if ($key) {
            $model_mb_user_token = Model('mb_user_token');
            $mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);

            $model_member = Model('member');
            $this->member_info = $model_member->getMemberInfoByID($mb_user_token_info['member_id']);
        }
    }

    public function indexWt()
    {
        if (in_array($_REQUEST['client'], $this->client_type_array)) {
            $code_info = C('store_joinin_pic');
            $info['pic'] = array();
            if (!empty($code_info)) {
                $info = unserialize($code_info);
            }
            $model_help = Model('help');
            //入驻指南
            $condition['type_id'] = '1';
            //显示4个
            $help_list = $model_help->getHelpList($condition, '', 4);

            foreach ($info['pic'] as $key => $value) {
                $info['pic'][$key] = UPLOAD_SITE_URL . DS . ATTACH_COMMON . DS . $value;
            }

            //是否是商家
            $model_seller = Model('seller');
            $seller_info = $model_seller->getSellerInfo(array('member_id' => $this->member_info['member_id']));

            //检查状态
            $join_state = $this->check_joinin_state();

            $result['info'] = $info;
            $result['help_list'] = $help_list;
            $result['join_state'] = $join_state;

            output_data(array('result' => $result));
        } else {
            output_error('非法提交');
        }
    }

    public function do_applyWt()
    {
        $step = isset($_POST['step']) ? intval($_POST['step']) : false;
        if ($step) {
            switch ($step) {
                case '4':
                    $this->do_apply_4($_POST);
                    break;
                
                case '5':
                    $this->do_apply_5($_POST);
                    break;
            }
        } else {
            $model_document = Model('document');
            $document_info = $model_document->getOneByCode('open_store');

            //店铺等级
            $grade_list = rkcache('store_grade', true);
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
                }
            }
            sort($grade_list);

            //店铺分类
            $model_store = Model('store_class');
            $store_class = $model_store->getStoreClassList(array(), '', false);

            //检查状态
            $join_state = $this->check_joinin_state();
            if (!$join_state) {
                $join_state = 0;
            }

            $result = array();
            $result['agreement'] = $document_info['doc_content'];
            $result['grade_list'] = $grade_list;
            $result['store_class'] = $store_class;
            $result['join_state'] = $join_state;

            output_data($result);
        }
    }

    /**
     * 获取商品分类
     * @return json
     */
    public function get_goods_categoryWt()
    {
        $gc_id = isset($_POST['gc_id']) ? intval($_POST['gc_id']) : 0;
        $gc = Model('goods_class');
        $gc_list = $gc->getGoodsClassListByParentId($gc_id);
        
        output_data(array('gc_list' => $gc_list));
    }

    public function ajax_upload_imageWt()
    {
        $pic_name = '';
        $upload = new UploadFile();
        $file = current($_FILES);
        $uploaddir = ATTACH_PATH . DS . 'store_joinin' . DS;
        $upload->set('max_size', C('image_max_filesize'));
        $upload->set('default_dir', $uploaddir);
        $upload->set('allow_type', array('jpg','jpeg','gif','png'));
        if (!empty($file['tmp_name'])) {
            $result = $upload->upfile(key($_FILES));
            if ($result) {
                echo json_encode(array('state'=>true,'pic_name'=>$upload->file_name,'pic_url'=>UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'store_joinin'.DS.$upload->file_name));
            } else {
                echo json_encode(array('state'=>false,'message'=>$upload->error));
            }
        }
    }

    private function check_joinin_state()
    {
        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id'=>$this->member_info['member_id']));
        if (!empty($joinin_detail)) {
            $this->joinin_detail = $joinin_detail;
            switch (intval($joinin_detail['joinin_state'])) {
                case STORE_JOIN_STATE_NEW:
                    $join_state['step'] = 4;
                    $join_state['msg'] = '入驻申请已经提交，请等待管理员审核';
                    break;
                case STORE_JOIN_STATE_PAY:
                    $join_state['step'] = 6;
                    $join_state['msg'] = '完成付款，请等待管理员核对后为您开通店铺';
                    break;
                case STORE_JOIN_STATE_VERIFY_SUCCESS:
                    $join_state['step'] = 5;
                    $join_state['msg'] = '申请已确认，请支付入驻费用';
                    break;
                case STORE_JOIN_STATE_VERIFY_FAIL:
                    $join_state['step'] = 6;
                    $join_state['msg'] = '审核失败:' . $joinin_detail['joinin_message'];
                    break;
                case STORE_JOIN_STATE_PAY_FAIL:
                    $join_state['step'] = 5;
                    $join_state['msg'] = '付款审核失败:' . $joinin_detail['joinin_message'];
                    break;
                case STORE_JOIN_STATE_FINAL:
                    $join_state['step'] = 100;
                    $join_state['msg'] = '开店成功';
                    break;
            }
        }

        return $join_state;
    }

    //第四步
    private function do_apply_4($post)
    {
        $store_class_ids = array();
        $store_class_names = array();
        if (!empty($_POST['store_class_ids'])) {
            foreach ($_POST['store_class_ids'] as $value) {
                $store_class_ids[] = $value;
            }
        }
        if (!empty($_POST['store_class_names'])) {
            foreach ($_POST['store_class_names'] as $value) {
                $store_class_names[] = $value;
            }
        }
        //取最小级分类最新分佣比例
        $sc_ids = array();
        foreach ($store_class_ids as $v) {
            $v = explode(',', trim($v, ','));
            if (!empty($v) && is_array($v)) {
                $sc_ids[] = end($v);
            }
        }
        if (!empty($sc_ids)) {
            $store_class_commis_rates = array();
            $goods_class_list = Model('goods_class')->getGoodsClassListByIds($sc_ids);
            if (!empty($goods_class_list) && is_array($goods_class_list)) {
                $sc_ids = array();
                foreach ($goods_class_list as $v) {
                    $store_class_commis_rates[] = $v['commis_rate'];
                }
            }
        }
        $param = array();
        $param['seller_name'] = $_POST['seller_name'];
        $param['store_name'] = $_POST['store_name'];
        $param['store_class_ids'] = serialize($store_class_ids);
        $param['store_class_names'] = serialize($store_class_names);
        $param['joinin_year'] = intval($_POST['joinin_year']);
        $param['joinin_state'] = STORE_JOIN_STATE_NEW;
        $param['store_class_commis_rates'] = implode(',', $store_class_commis_rates);

        //取店铺等级信息
        $grade_list = rkcache('store_grade', true);
        if (!empty($grade_list[$_POST['sg_id']])) {
            $param['sg_id'] = $_POST['sg_id'];
            $param['sg_name'] = $grade_list[$_POST['sg_id']]['sg_name'];
            $param['sg_info'] = serialize(array('sg_price' => $grade_list[$_POST['sg_id']]['sg_price']));
        }

        //取最新店铺分类信息
        $store_class_info = Model('store_class')->getStoreClassInfo(array('sc_id'=>intval($_POST['sc_id'])));
        if ($store_class_info) {
            $param['sc_id'] = $store_class_info['sc_id'];
            $param['sc_name'] = $store_class_info['sc_name'];
            $param['sc_bail'] = $store_class_info['sc_bail'];
        }

        //店铺应付款
        $param['paying_amount'] = floatval($grade_list[$_POST['sg_id']]['sg_price'])*$param['joinin_year']+floatval($param['sc_bail']);
        
        $param['member_id'] = $this->member_info['member_id'];
        $param['member_name'] = $this->member_info['member_name'];
        $param['company_name'] = trim($_POST['company_name']);
        $param['company_province_id'] = intval($_POST['company_province_id']);
        $param['company_address'] = $_POST['company_address'] ? trim($_POST['company_address']) : trim($_POST['company_address_detail']);
        $param['company_address_detail'] = trim($_POST['company_address_detail']);
        $param['company_phone'] = trim($_POST['company_phone']);
        $param['company_employee_count'] = intval($_POST['company_employee_count']);
        $param['company_registered_capital'] = trim($_POST['company_registered_capital']);
        $param['contacts_name'] = trim($_POST['contacts_name']);
        $param['contacts_phone'] = trim($_POST['contacts_phone']);
        $param['contacts_email'] = trim($_POST['contacts_email']);
        $param['business_licence_number'] = trim($_POST['business_licence_number']);
        $param['bank_name'] = trim($_POST['bank_name']);
        $param['bank_account_name'] = trim($_POST['bank_account_name']);
        $param['bank_account_number'] = trim($_POST['bank_account_number']);

        $this->step4_save_valid($param);

        $model_store_joinin = Model('store_joinin');
        $store_joinin_info = $model_store_joinin->getOne(array('member_id' => $this->member_info['member_id']));
        if (!$store_joinin_info) {
            $model_store_joinin->save($param);
        } else {
            $model_store_joinin->modify($param, array('member_id'=> $this->member_info['member_id']));
        }

        $joinin_state = $this->check_joinin_state();

        output_data(array('result' => 'ok', 'next_step' => $joinin_state['step'] + 1, 'next_tips' => $joinin_state['msg']));
    }
    //验证第四步
    private function step4_save_valid($param)
    {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['store_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"店铺名称不能为空且必须小于50个字"),
            array("input"=>$param['sg_id'], "require"=>"true","message"=>"店铺等级不能为空"),
            array("input"=>$param['sc_id'], "require"=>"true","message"=>"店铺分类不能为空"),
            array("input"=>$param['company_address'], "require"=>"true","message"=>"公司地址不能为空，您也可以填写个人地址"),
        );
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error);
        }
    }

    //第五步
    public function do_apply_5()
    {
        $param = array();
        $param['paying_money_certificate'] = $_POST['paying_money_certificate_val'];
        $param['paying_money_certif_exp'] = $_POST['paying_money_certif_exp'];
        $param['joinin_state'] = STORE_JOIN_STATE_PAY;

        if (empty($param['paying_money_certificate'])) {
            showMessage('请上传付款凭证', '', '', 'error');
        }

        $model_store_joinin = Model('store_joinin');
        $model_store_joinin->modify($param, array('member_id'=>$this->member_info['member_id']));

        output_data(array('result' => 'ok', 'next_step' => 6, 'next_tips' => '已经提交，请等待管理员核对后为您开通店铺'));
    }
}
