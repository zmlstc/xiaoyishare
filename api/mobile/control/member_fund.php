<?php
/**
 * 我的余额
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class member_fundControl extends mobileMemberControl {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 预存款日志列表
     */
    public function predepositlogWt() {
        $model_predeposit = Model('predeposit');
        $where = array();
        $where['lg_member_id'] = $this->member_info['member_id'];
        $where['lg_av_amount'] = array('neq', 0);
        $list = $model_predeposit->getPdLogList($where, 12, '*', 'lg_id desc');
        $page_count = $model_predeposit->gettotalpage();
        if ($list) {
            foreach ($list as $k => $v) {
                $v['lg_add_time_text'] = @date('Y-m-d H:i:s', $v['lg_add_time']);
                //$v['lg_av_amount'] = floatval($v['lg_av_amount']);
                $list[$k] = $v;
            }
        }
        output_data(array('list' => $list), mobile_page($page_count));
    }

    /**
     * 收入总数
     */
    public function tglistWt() {
        $lgtype = $_POST['type'];
        $model_predeposit = Model('predeposit');
        $where = array();
        $where['lg_member_id'] = $this->member_info['member_id'];
        $where['is_tg'] = 1;
        if ($_POST['daterange']) {
            $daterange = explode(',', $_POST['daterange']);
            $daterange[0] = $daterange[0] . ' 00:00:00';
            $daterange[1] = $daterange[1] . ' 23:59:59';
            foreach ($daterange as $key => $value) {
                $daterange[$key] = strtotime($value);
            }
            $where['lg_add_time'] = array('between time', [$daterange[0], $daterange[1]]);
            //output_data(array('order_list' => $daterange));
        }
        if ($lgtype == '1') {
            $where['lg_type'] = 'store_invite';
        } elseif ($lgtype == '2') {
            $where['lg_type'] = 'member_invite';
        }

        if (!empty($_POST['keyword']) && trim($_POST['keyword']) != '') {
            $where['lg_invite_member_name|lg_invite_store_name'] = array('like', '%' . trim($_POST['keyword']) . '%');
        }
        $amount_sum = $model_predeposit->getPdLogSum($where);
        $list = $model_predeposit->getPdLogList($where, 12, '*', 'lg_id desc');
        $page_count = $model_predeposit->gettotalpage();
        if ($list) {
            foreach ($list as $k => $v) {
                $v['lg_add_time_text'] = @date('m月d日', $v['lg_add_time']);
                if ($v['lg_type'] == 'member_invite') {
                    $memberInfo = Model('member')->getMemberInfoByID($v['lg_invite_member_id']);
                    $v['show_text'] = $memberInfo['nickname'];// $v['lg_invite_member_name'];
                } else if ($v['lg_type'] == 'store_invite') {
                    $v['show_text'] = $v['lg_invite_store_name'];
                }
                $list[$k] = $v;
            }
        }
        output_data(array('list' => $list, 'amount' => $amount_sum), mobile_page($page_count));
    }

    //今日收入
    public function tgtodayWt() {
        $lgtype = $_POST['type'];
        $model_predeposit = Model('predeposit');
        $where = array();
        $where['lg_member_id'] = $this->member_info['member_id'];
        $where['is_tg'] = 1;
        $where['lg_add_time'] = array('egt', strtotime(date('Y-m-d', time())));
        if ($lgtype == '1') {
            $where['lg_type'] = 'store_invite';
        } elseif ($lgtype == '2') {
            $where['lg_type'] = 'member_invite';
        }

        if (!empty($_POST['keyword']) && trim($_POST['keyword']) != '') {
            $where['lg_invite_member_name|lg_invite_store_name'] = array('like', '%' . trim($_POST['keyword']) . '%');
        }

        $amount_sum = $model_predeposit->getPdLogSum($where);
        if (!$amount_sum) {
            $amount_sum = 0;
        }
        $list = $model_predeposit->getPdLogList($where, 12, '*', 'lg_id desc');
        $page_count = $model_predeposit->gettotalpage();
        if ($list) {
            foreach ($list as $k => $v) {
                $v['lg_add_time_text'] = @date('m月d日', $v['lg_add_time']);
                if ($v['lg_type'] == 'member_invite') {
                    $memberInfo = Model('member')->getMemberInfoByID($v['lg_invite_member_id']);
                    $v['show_text'] = $memberInfo['nickname'];// $v['lg_invite_member_name'];
                } else if ($v['lg_type'] == 'store_invite') {
                    $v['show_text'] = $v['lg_invite_store_name'];
                }
                $list[$k] = $v;
            }
        }

        //获取昨日收入
        $beginYesterday = strtotime(date("Y-m-d", strtotime("-1 day")));

        $where['lg_add_time'] = array('between', [$beginYesterday, strtotime(date('Y-m-d', time()))]);
        $yesterday_amount_sum = $model_predeposit->getPdLogSum($where);
        if (!$yesterday_amount_sum) {
            $yesterday_amount_sum = 0;
        }

        //比较今天和昨天的收入
        if ($yesterday_amount_sum == 0) {
            $percent = $amount_sum;
        } else {
            $percent = ($amount_sum - $yesterday_amount_sum) / $yesterday_amount_sum;
        }

        if ($percent == 0) {
            $percent = 0;
        } else {
            //保留小数点后一位
            $percent = round($percent, 1) * 100;
        }

        output_data(array('list' => $list, 'amount' => $amount_sum, 'percent' => $percent), mobile_page($page_count));
    }


    public function phoneWt() {
        $memberInfo = Model('member')->getMemberInfoByID($this->member_info['member_id']);
        $m_state = $memberInfo['member_mobile_bind'] ? true : false;
        $data = array();
        $data['mobiletxt'] = $m_state ? encryptShow($memberInfo['member_mobile'], 4, 4) : '未验证';
        $data['member_mobile'] = $memberInfo['member_mobile'];
        output_data($data);
    }

    /**
     * 添加提现方式
     */
    public function txway_addWt() {

        $obj_validate = new Validate();
        $validate_arr[] = array("input" => $_POST["bill_user_name"], "require" => "true", "message" => '收款人姓名不能为空');
        $validate_arr[] = array("input" => $_POST["bill_type_code"], "require" => "true", "message" => '请选择账户类型');
        $validate_arr[] = array("input" => $_POST["bill_type_number"], "require" => "true", "message" => '收款账号不能为空');
        //$validate_arr[] = array("input"=>$_POST["bill_bank_name"], "require"=>"true","message"=>'请输入开户行');
        $obj_validate->validateparam = $validate_arr;
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error);
        }
        if ($_POST['bill_type_code'] == 'wxpay' && $_POST['openid'] == '') {
            output_error('微信账号参数错误！');
        }
        if ($_POST['bill_type_code'] != 'wxpay') {
            $_POST['openid'] = '';
        }

        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['type'] = 1;
        $where['bill_type_code'] = trim($_POST['bill_type_code']);
        $where['bill_type_number'] = trim($_POST['bill_type_number']);
        $tx = Model('txway')->getInfo($where);
        if (!empty($tx) && is_array($tx)) {
            output_error('该账号已存在,请换其他账号添加！');
        }

        $data = array();
        $data['member_id'] = $this->member_info['member_id'];
        $data['member_name'] = $this->member_info['member_name'];
        $data['bill_user_name'] = $_POST['bill_user_name'];
        $data['bill_type_code'] = $_POST['bill_type_code'];
        $data['bill_type_number'] = $_POST['bill_type_number'];
        $data['bill_openid'] = $_POST['openid'];
        //$data['add_time'] = TIMESTAMP;
        $data['type'] = 1;
        $insert = Model('txway')->add($data);

        if ($insert) {
            output_data(array('state' => 1));
        } else {
            output_error('添加失败');
        }

    }

    /**
     * 删除提现方式
     */
    public function txway_delWt() {
        if ($_POST['id'] == '' && intval($_POST['id']) < 1) {
            output_error('参数错误！');
        }

        $data = array();
        $data['member_id'] = $this->member_info['member_id'];
        $data['id'] = intval($_POST['id']);
        $data['type'] = 1;
        $insert = Model('txway')->del($data);

        if ($insert) {
            output_data(array('state' => 1));
        } else {
            output_error('删除失败');
        }

    }

    /**
     * 微信小程序获得openid
     */
    public function getwxinfoWt() {
        $handle_connect_api = Handle('connect_api');
        if (!empty($_POST['code'])) {
            $code = $_POST['code'];
            $client = 'wap';
            $user_info = $handle_connect_api->getWxMiniUserInfo($code);
            if (!empty($user_info['openid']) && $user_info['openid'] != '') {
                $openid = $user_info['openid'];
                $model_txway = Model('txway');
                $txwayInfo = $model_txway->getInfo(array('bill_openid' => $openid, 'type' => 1));

                if (!empty($txwayInfo)) {//||$txwayInfo['member_id']==$this->member_info['member_id']) {

                    output_error('当前微信号已存在', array('state' => 0));

                } else {
                    output_data(array('state' => 10, 'openid' => $user_info['openid']));
                }
            } else {
                output_error('获取信息失败1', array('state' => 0));
            }
        } else {
            output_error('获取信息失败2', array('state' => 0));
        }
    }


    /**
     * 提现方式列表
     */
    public function txwaylistWt() {
        $model_txway = Model('txway');
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['type'] = 1;

        $list = $model_txway->getList($where, 20, '*', 'id desc');
        $page_count = $model_txway->gettotalpage();
        if ($list) {
            foreach ($list as $k => $v) {
                $data = array();
                $data['id'] = $v['id'];
                //$data['member_id'] = $v['member_id'];
                $data['bill_user_name'] = $v['bill_user_name'];
                $data['bill_type_code'] = $v['bill_type_code'];
                //$data['bill_type_number'] = $v['bill_type_number'];
                $data['bill_type_number_txt'] = encryptShow($v['bill_type_number'], 4, 4);
                $data['is_default'] = $v['is_default'];
                $list[$k] = $data;
            }
        }
        output_data(array('list' => $list), mobile_page($page_count));
    }

    /**
     * 设置提现方式为默认1
     */
    public function isdefautWt() {

        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['type'] = 1;
        $insert = Model('txway')->edit(array('is_default' => 0), $where);
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['id'] = intval($_POST['id']);
        $where['type'] = 1;
        $insert = Model('txway')->edit(array('is_default' => 1), $where);
        if ($insert) {
            output_data(array('state' => 1));
        } else {
            output_error('设置失败');
        }

    }

    /**
     * 获取提现方式
     */
    public function gettxwayWt() {
        $data = array();
        $txinfo = array();
        $memberInfo = Model('member')->getMemberInfoByID($this->member_info['member_id']);
        $data['predepoit'] = $memberInfo['available_predeposit'];
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['type'] = 1;
        $where['is_default'] = 1;
        $tx = Model('txway')->getInfo($where);
        if (empty($tx)) {
            $where = array();
            $where['member_id'] = $this->member_info['member_id'];
            $where['type'] = 1;
            $tx = Model('txway')->getInfo($where);
        }
        if (!empty($tx) && is_array($tx)) {
            $txinfo['id'] = $tx['id'];
            $txinfo['bill_user_name'] = $tx['bill_user_name'];
            $txinfo['bill_type_code'] = $tx['bill_type_code'];
            $txinfo['paytxt'] = $tx['bill_type_code'] == 'wxpay' ? '微信支付账号' : '支付宝账号';
            //$txinfo['bill_type_number'] = $tx['bill_type_number'];
            $txinfo['bill_type_number_txt'] = encryptShow($tx['bill_type_number'], 4, 4);
            $data['notxway'] = 0;
            $data['txway'] = $txinfo;
        } else {
            $data['notxway'] = 1;
        }
        output_data($data);

    }

    /**
     * 申请提现
     */
    public function pd_cash_addWt() {
        $member_id = $this->member_info['member_id'];
        $obj_validate = new Validate();
        $pdc_amount = abs(floatval($_POST['pdc_amount']));
        $validate_arr[] = array("input" => $pdc_amount, "require" => "true", 'validator' => 'Compare', 'operator' => '>=', "to" => '0.01', "message" => '提现金额错误');
        $validate_arr[] = array("input" => $_POST["id"], "require" => "true", "message" => '参数错误！');
        $validate_arr[] = array("input" => $_POST["ppwd"], "require" => "true", "message" => '请输入支付密码！');
        $obj_validate->validateparam = $validate_arr;
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error);
        }
        $txwayInfo = Model('txway')->getInfo(array('member_id' => $member_id, 'id' => intval($_POST["id"])));
        if (empty($txwayInfo)) {
            output_error('提现参数错误！');
        }

        $model_pd = Model('predeposit');
        $model_member = Model('member');
        $member_info = $model_member->table('member')->where(array('member_id' => $member_id))->master(true)->lock(true)->find();//锁定当前会员记录
        //验证支付密码
        if (md5($_POST['ppwd']) != $member_info['member_paypwd']) {
            output_error('支付密码错误');
        }
        //验证金额是否足够
        if (floatval($member_info['available_predeposit']) < $pdc_amount) {
            output_error('提现金额不足');
        }
        try {
            $model_pd->beginTransaction();
            $pdc_sn = $model_pd->makeSn();
            $data = array();
            $data['pdc_sn'] = 'TX' . $pdc_sn;
            $data['pdc_member_id'] = $this->member_info['member_id'];
            $data['pdc_member_name'] = $this->member_info['member_name'];
            $data['pdc_amount'] = $pdc_amount;
            $data['pdc_bank_name'] = $txwayInfo['bill_type_code'] == 'wxpay' ? '微信支付账号' : '支付宝账号';//$txwayInfo['bill_bank_name'];
            $data['pdc_bank_no'] = $txwayInfo['bill_type_number'];
            $data['pdc_bank_user'] = $txwayInfo['bill_user_name'];
            $data['pdc_type_code'] = $txwayInfo['bill_type_code'];
            if ($txwayInfo['bill_type_code'] == 'wxpay') {
                $data['pdc_openid'] = $txwayInfo['bill_openid'];
            }
            $data['pdc_add_time'] = TIMESTAMP;
            $data['pdc_payment_state'] = 0;
            $insert = $model_pd->addPdCash($data);
            if (!$insert) {
                throw new Exception('写入数据失败');
            }
            //冻结可用预存款
            $data = array();
            $data['member_id'] = $member_info['member_id'];
            $data['member_name'] = $member_info['member_name'];
            $data['amount'] = $pdc_amount;
            $data['order_sn'] = $pdc_sn;
            $model_pd->changePd('cash_apply', $data);
            $model_pd->commit();

            //发送用户消息
            $param = array();
            $param['code'] = 'tx_apply_success';
            $param['member_id'] = $data['member_id'];
            $date = date('Y-m-d', time());
            $time = date('H:i:s', time());
            $set = Model('setting')->getListSetting();
            $phone = $set['wt_phone'];
            $param['param'] = array('name' => $data['member_name'], 'date' => $date, 'time' => $time, 'av_amount' => $pdc_amount, 'phone' => $phone);

            QueueClient::push('sendMemberMsg', $param);

            output_data(array('state' => 1, 'msg' => '提交成功'));
        } catch (Exception $e) {
            $model_pd->rollback();
            output_error($e->getMessage());
        }

    }

    /**
     * 提现记录
     */
    public function pdcashlistWt() {

        $where = array();
        if ($_POST['daterange']) {
            $daterange = explode(',', $_POST['daterange']);
            $daterange[0] = $daterange[0] . ' 00:00:00';
            $daterange[1] = $daterange[1] . ' 23:59:59';
            foreach ($daterange as $key => $value) {
                $daterange[$key] = strtotime($value);
            }
            $where['pdc_payment_time'] = array('between time', [$daterange[0], $daterange[1]]);
            //output_data(array('order_list' => $daterange));
        }
        // $where['pdc_member_id'] =  $this->member_info['member_id'];
        $model_pd = Model('predeposit');
        $list = $model_pd->getPdCashList($where, $this->page, '*', 'pdc_id desc');
        $page_count = $model_pd->gettotalpage();
        if ($list) {
            foreach ($list as $k => $v) {
                $v['pdc_add_time_text'] = @date('Y-m-d H:i:s', $v['pdc_add_time']);
                $v['pdc_payment_time_text'] = @date('Y-m-d H:i:s', $v['pdc_payment_time']);
                $v['pdc_payment_state_text'] = $v['pdc_payment_state'] == 1 ? '成功' : '未成功';
                $v['pdc_b_text'] = $v['pdc_type_code'] == 'alipay' ? '支付宝' : '微信';
                $list[$k] = $v;
            }
        }
        output_data(array('list' => $list), mobile_page($page_count));
    }

    /**验证提现支付宝账号是否存在**/
    public function checktxalipayWt() {
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['type'] = 1;
        $where['bill_type_code'] = trim($_POST['bill_type_code']);
        $where['bill_type_number'] = trim($_POST['bill_type_number']);
        $tx = Model('txway')->getInfo($where);
        if (!empty($tx) && is_array($tx)) {
            output_error('该账号已存在,请换其他账号添加！');
        } else {
            output_data(array('state' => 1));
        }


    }











    /**
     * 预存款日志列表
     */
    /*     public function predepositlogWt(){
            $model_predeposit = Model('predeposit');
            $where = array();
            $where['lg_member_id'] = $this->member_info['member_id'];
            $where['lg_av_amount'] = array('neq',0);
            $list = $model_predeposit->getPdLogList($where, $this->page, '*', 'lg_id desc');
            $page_count = $model_predeposit->gettotalpage();
            if ($list) {
                foreach($list as $k=>$v){
                    $v['lg_add_time_text'] = @date('Y-m-d H:i:s',$v['lg_add_time']);
                    $list[$k] = $v;
                }
            }
            output_data(array('list' => $list), mobile_page($page_count));
        } */
    /**
     * 充值卡余额变更日志
     */
    public function rcblogWt() {
        $model_rcb_log = Model('rcb_log');
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $where['available_amount'] = array('neq', 0);
        $log_list = $model_rcb_log->getRechargeCardBalanceLogList($where, $this->page, '', 'id desc');
        $page_count = $model_rcb_log->gettotalpage();
        if ($log_list) {
            foreach ($log_list as $k => $v) {
                $v['add_time_text'] = @date('Y-m-d H:i:s', $v['add_time']);
                $log_list[$k] = $v;
            }
        }
        output_data(array('log_list' => $log_list), mobile_page($page_count));
    }

    /**
     * 充值明细
     */
    public function pdrechargelistWt() {
        $where = array();
        $where['pdr_member_id'] = $this->member_info['member_id'];
        $model_pd = Model('predeposit');
        $list = $model_pd->getPdRechargeList($where, $this->page, '*', 'pdr_id desc');
        $page_count = $model_pd->gettotalpage();
        if ($list) {
            foreach ($list as $k => $v) {
                $v['pdr_add_time_text'] = @date('Y-m-d H:i:s', $v['pdr_add_time']);
                $v['pdr_payment_state_text'] = $v['pdr_payment_state'] == 1 ? '已支付' : '未支付';
                $list[$k] = $v;
            }
        }
        output_data(array('list' => $list), mobile_page($page_count));
    }

    /**
     * 充值卡充值
     */
    public function rechargecard_addWt() {
        $param = $_POST;
        $rc_sn = trim($param["rc_sn"]);
        if (!$rc_sn) {
            output_error('请输入平台充值卡号');
        }
        if (!Model('apivercode')->checkApiVercode($param["codekey"], $param['captcha'])) {
            output_error('验证码错误');
        }
        try {
            Model('predeposit')->addRechargeCard($rc_sn, array('member_id' => $this->member_info['member_id'], 'member_name' => $this->member_info['member_name']));
            output_data('1');
        } catch (Exception $e) {
            output_error($e->getMessage());
        }
    }

    /**
     * 预存款提现记录详细
     */
    public function pdcashinfoWt() {
        $param = $_GET;
        $pdc_id = intval($param["pdc_id"]);
        if ($pdc_id <= 0) {
            output_error('参数错误');
        }
        $where = array();
        $where['pdc_member_id'] = $this->member_info['member_id'];
        $where['pdc_id'] = $pdc_id;
        $info = Model('predeposit')->getPdCashInfo($where);
        if (!$info) {
            output_error('参数错误');
        }
        $info['pdc_add_time_text'] = $info['pdc_add_time'] ? @date('Y-m-d H:i:s', $info['pdc_add_time']) : '';
        $info['pdc_payment_time_text'] = $info['pdc_payment_time'] ? @date('Y-m-d H:i:s', $info['pdc_payment_time']) : '';
        $info['pdc_payment_state_text'] = $info['pdc_payment_state'] == 1 ? '已支付' : '未支付';
        output_data(array('info' => $info));
    }
}