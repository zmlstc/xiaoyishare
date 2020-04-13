<?php
/**
 * 购买
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class member_buyControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 店铺信息
     */
    public function store_infoWt() {
        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }

        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        // 店铺头像
        $store_info['store_avatar'] = $store_online_info['store_avatar']
            ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['store_avatar']
            : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');
        output_data($store_info);
    }

    /**
     * 购物车、直接购买第一步:选择收获地址和配置方式
     */
    public function buy_step1Wt() {
        $store_id = (int)$_REQUEST['store_id'];
        $cost_total = floatval($_POST['total']);
        if ($store_id < 1 || $cost_total <= 0) {
            output_error('提交参数错误!');
        }

        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }
        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];

        //$logic_buy = logic('buy');

        //得到会员等级
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if (!$member_info['is_buy']) output_error('您没有消费支付的权限,如有疑问请联系客服人员');

        $memberInfo = array();
        $memberInfo['member_id'] = $member_info['member_id'];
        $memberInfo['member_name'] = $member_info['member_name'];
        $memberInfo['available_predeposit'] = $member_info['available_predeposit'];
        $memberInfo['pp_state'] = $member_info['member_paypwd'] ? 1 : 0;


        $result = array();
        $model_voucher = Model('voucher');
        if (C('voucher_allow')) {
            $condition = array();
            $condition['voucher_store_id'] = $store_id;
            $condition['voucher_owner_id'] = $this->member_info['member_id'];
            $voucher_list = $model_voucher->getCurrentAvailableVoucher($condition, $cost_total, 'voucher_limit desc');
        }


        $voucherlist = array();
        if (!empty($voucher_list) && is_array($voucher_list)) {
            foreach ($voucher_list as $k => $val) {
                $voucherlist[] = $val;
            }

        }
        $result['voucher_list'] = $voucherlist;


        $result['store_info'] = $store_info;
        $result['member_info'] = $memberInfo;
        $result['cost_total'] = $cost_total;
        output_data($result);

        exit;


    }

    /**
     * 购物车、直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
     *
     */
    public function buy_step2Wt() {
        $param = array();
        $param['store_id'] = $_POST['store_id'];
        $param['total'] = floatval($_POST['total']);
        $param['vt_id'] = $_POST['vt_id'];
        $param['paycode'] = $_POST['paycode'];
        $param['pay_comment'] = trim($_POST['pay_comment']);
        $ppwd = $_POST['ppwd'];

        //$param['rpt'] = $_POST['rpt'];

        //处理代金券
        /*    $voucher = array();
           $post_voucher = explode(',', $_POST['voucher']);
           if(!empty($post_voucher)) {
               foreach ($post_voucher as $value) {
                   list($voucher_t_id, $store_id, $voucher_price) = explode('|', $value);
                   $voucher[$store_id] = $value;
               }
           }
           $param['voucher'] = $voucher; */

        /*  $_POST['pay_message'] = trim($_POST['pay_message'],',');
         $_POST['pay_message'] = explode(',',$_POST['pay_message']);
         $param['pay_message'] = array();
         if (is_array($_POST['pay_message']) && $_POST['pay_message']) {
             foreach ($_POST['pay_message'] as $v) {
                 if (strpos($v, '|') !== false) {
                     $v = explode('|', $v);
                     $param['pay_message'][$v[0]] = $v[1];
                 }
             }
         } */
        /*   $param['paycode'] = $_POST['paycode'];
          $param['rcb_pay'] = $_POST['rcb_pay'];
          $param['password'] = $_POST['password']; */
        //$param['fcode'] = $_POST['fcode'];
        $param['order_from'] = 2;
        $logic_buy = Handle('buy');
        if (!empty($param['paycode']) && $param['paycode'] == 'pdpay') {
            $param['pdpay'] = 'pdpay';
        }


        if (!in_array($param['paycode'], array('alipay', 'wxpay', 'wxpay_jsapi', 'pdpay', 'wxminipay'))) {
            output_error('请选择正确的支付方式');
        }
        if ($param['paycode'] == 'pdpay' && (empty($ppwd) || $ppwd == '')) {
            output_error('请输入支付密码！');
        }

        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if ($member_info['lock_ppwd_num'] >= 5 && $member_info['lock_ppwd_time'] >= time()) {
            output_error('账户已锁定,请在1小时后再试！');
        }
        if ($param['paycode'] == 'pdpay' && $member_info['member_paypwd'] != md5($ppwd)) {
            $where = array();
            $where['member_id'] = $this->member_info['member_id'];
            $where['addtime'] = array('gt', time() - 30 * 60);
            $error_num = Model()->table('member_ppw_log')->where($where)->count();

            $update = array();
            $update['lock_ppwd_num'] = array('exp', "lock_ppwd_num + 1");
            if ($error_num == 0) {
                $update['lock_ppwd_num'] = 1;
            }
            $update['lock_ppwd_time'] = time() + 60 * 60;
            $updateSuccess = Model('member')->editMember(array('member_id' => $this->member_info['member_id']), $update);
            $insert = array();
            $insert['member_id'] = $this->member_info['member_id'];
            $insert['addtime'] = time();
            $insert['is_error'] = 1;

            $rs = Model()->table('member_ppw_log')->insert($insert);
            output_error('支付密码不正确！还剩' . (4 - intval($member_info['lock_ppwd_num'])) . '次数机会');
        } else {
            $update = array();
            $update['lock_ppwd_num'] = 0;
            $update['lock_ppwd_time'] = 0;
            $updateSuccess = Model('member')->editMember(array('member_id' => $this->member_info['member_id']), $update);

        }

        $result = $logic_buy->nBuyStep2($param, $this->member_info['member_id'], $this->member_info['member_name'], $_POST['store_id']);
        if (!$result['state']) {
            output_error($result['msg']);
        }
        $order_info = $result['data']['order'];

        //支付
        if ($param['paycode'] != 'pdpay') {
            if ($param['paycode'] == 'wxpay_jsapi') {
                output_data(array('pay_sn' => $result['data']['pay_sn'], 'payment_code' => $order_info['payment_code']));
            } else {
                $retInfo = $this->_api_pay($order_info);
                //echo json_encode($result['data']);exit;
                output_data(array('pay_sn' => $result['data']['pay_sn'], 'payment_code' => $order_info['payment_code'], 'payinfo' => $retInfo));
            }
        } else {
            output_data(array('state' => 1, 'msg' => '支付成功！'));
        }
    }

    private function _api_pay($order_info) {

        $model_mb_payment = Model('mb_payment');
        $condition = array();
        $condition['payment_code'] = $order_info['payment_code'];
        $mb_payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
        if (!$mb_payment_info) {
            output_error('支付方式未开启');
        }

        $inc_file = BASE_PATH . DS . 'api' . DS . 'payment' . DS . $order_info['payment_code'] . DS . $order_info['payment_code'] . '.php';
        if (!is_file($inc_file)) {
            output_error('支付接口不存在');
        }
        require($inc_file);
        $param = $mb_payment_info['payment_config'];

        $payInfo = '';
        if ($order_info['payment_code'] == 'wxminipay') {
            $param['orderSn'] = $order_info['pay_sn'] . '' . mt_rand(1000, 9999);
            $param['orderFee'] = (int)(wtPriceFormat(100 * $order_info['order_amount']));
            $param['orderInfo'] = $order_info['pay_sn'] . '订单';
            $param['orderAttach'] = 'r';//($order_info['order_type'] == 'real_order' ? 'r' : 'v');
            $param['js_code'] = $_POST['wxcode'];
            $api = new wxminipay();
            $api->setConfigs($param);
            try {
                $payInfo = $api->paymentHtml($this);
            } catch (Exception $ex) {
                output_error('支付接口请求异常！' . json_encode($param));

            }


            return $payInfo;
            exit;
        } else if ($order_info['payment_code'] == 'alipay') {
            $param['order_sn'] = $order_info['pay_sn'];
            $param['order_amount'] = $order_info['order_amount'];
            $param['order_type'] = 'r';
            $payment_api = new alipay();
            $return = $payment_api->submit($param);
            return $return;
            exit;
        } else if ($order_info['payment_code'] == 'wxpay_jsapi') {
            $param['orderSn'] = $order_info['pay_sn'] . '' . mt_rand(1000, 9999);
            $param['orderFee'] = (int)(wtPriceFormat(100 * $order_info['order_amount']));
            $param['orderInfo'] = $order_info['pay_sn'] . '订单';
            $param['orderAttach'] = 'r';
            $param['js_code'] = $_POST['wxcode'];
            $api = new wxpay_jsapi();
            $api->setConfigs($param);
            try {
                $payInfo = $api->paymentHtml($this);
            } catch (Exception $ex) {
                Log::record('wxpay_jsapi支付接口请求异常！' . json_encode($param));
                output_error('支付接口请求异常！' . json_encode($param));
            }
            return $payInfo;
            exit;
        }

    }

    /*wx_jsapi支付*/
    public function pay_wxjsapiWt() {
        $paysn = trim($_POST['paysn']);
        $wxcode = trim($_POST['wxcode']);
        $condition = array();
        $condition['pay_sn'] = $paysn;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_state'] = array('in', array(ORDER_STATE_NEW));
        $orderinfo = Model('store_order')->getOrderInfo($condition);
        if (empty($orderinfo)) {
            output_error('该支付单不存在');
        }


        $retInfo = $this->_api_pay($orderinfo);

        $store_online_info = Model('store')->getStoreOnlineInfoByID($orderinfo['store_id']);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }

        $order_info = array();
        $order_info['store_id'] = $orderinfo['store_id'];
        $order_info['store_name'] = $orderinfo['store_name'];
        $order_info['order_amount'] = $orderinfo['order_amount'];
        $order_info['discount_money'] = floatval($orderinfo['goods_amount'] - $orderinfo['order_amount']);

        // 店铺头像
        $order_info['store_avatar'] = $store_online_info['store_avatar']
            ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['store_avatar']
            : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');

        output_data(array('pay_sn' => $result['data']['pay_sn'], 'payment_code' => $orderinfo['payment_code'], 'payinfo' => $retInfo, 'orderinfo' => $order_info));


        output_data(array('pay_sn' => $result['data']['pay_sn'], 'payment_code' => $orderinfo['payment_code'], 'payinfo' => $retInfo));


    }


    /**
     * 验证密码
     */
    public function check_passwordWt() {
        if (empty($_POST['password'])) {
            output_error('参数错误');
        }

        $model_member = Model('member');

        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if ($member_info['member_paypwd'] == md5($_POST['password'])) {
            output_data('1');
        } else {
            output_error('密码错误');
        }
    }

    /**
     * 更换收货地址
     */
    public function change_addressWt() {
        $handle_buy = Handle('buy');
        if (empty($_POST['city_id'])) {
            $_POST['city_id'] = $_POST['area_id'];
        }

        $data = $handle_buy->changeAddr($_POST['freight_hash'], $_POST['city_id'], $_POST['area_id'], $this->member_info['member_id']);
        if (!empty($data) && $data['state'] == 'success') {
            output_data($data);
        } else {
            output_error('地址修改失败');
        }
    }

    /**
     * 实物订单支付(新接口)
     */
    public function payWt() {
        $pay_sn = $_POST['pay_sn'];
        if (!preg_match('/^\d{18}$/', $pay_sn)) {
            output_error('该订单不存在');
        }

        //查询支付单信息
        $model_order = Model('order');
        $pay_info = $model_order->getOrderPayInfo(array('pay_sn' => $pay_sn, 'buyer_id' => $this->member_info['member_id']), true);
        if (empty($pay_info)) {
            output_error('该订单不存在');
        }

        //取子订单列表
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        $condition['order_state'] = array('in', array(ORDER_STATE_NEW, ORDER_STATE_PAY));
        $order_list = $model_order->getOrderList($condition, '', '*', '', '', array(), true);
        if (empty($order_list)) {
            output_error('未找到需要支付的订单');
        }

        //定义输出数组
        $pay = array();
        //支付提示主信息
        //订单总支付金额(不包含货到付款)
        $pay['pay_amount'] = 0;
        //充值卡支付金额(之前支付中止，余额被锁定)
        $pay['payed_rcb_amount'] = 0;
        //预存款支付金额(之前支付中止，余额被锁定)
        $pay['payed_pd_amount'] = 0;
        //还需在线支付金额(之前支付中止，余额被锁定)
        $pay['pay_diff_amount'] = 0;
        //账户可用金额
        $pay['member_available_pd'] = 0;
        $pay['member_available_rcb'] = 0;

        $handle_order = Handle('order');

        //计算相关支付金额
        foreach ($order_list as $key => $order_info) {
            if (!in_array($order_info['payment_code'], array('offline', 'chain'))) {
                if ($order_info['order_state'] == ORDER_STATE_NEW) {
                    $pay['payed_rcb_amount'] += $order_info['rcb_amount'];
                    $pay['payed_pd_amount'] += $order_info['pd_amount'];
                    $pay['pay_diff_amount'] += $order_info['order_amount'] - $order_info['rcb_amount'] - $order_info['pd_amount'] - $order_info['points_money'];
                }
            }
        }
        if ($order_info['chain_id'] && $order_info['payment_code'] == 'chain') {
            $order_list[0]['order_remind'] = '下单成功，请在' . CHAIN_ORDER_PAYPUT_DAY . '日内前往门店提货，逾期订单将自动取消。';
            $flag_chain = 1;
        }

        //如果线上线下支付金额都为0，转到支付成功页
        if (empty($pay['pay_diff_amount'])) {
            //处理预订单重复支付问题
            output_error('订单重复支付');
        }

        $payment_list = Model('mb_payment')->getMbPaymentOpenList();
        if (!empty($payment_list)) {
            foreach ($payment_list as $k => $value) {
                if ($value['payment_code'] == 'wxpay') {
                    unset($payment_list[$k]);
                    continue;
                }
                unset($payment_list[$k]['payment_id']);
                unset($payment_list[$k]['payment_config']);
                unset($payment_list[$k]['payment_state']);
                unset($payment_list[$k]['payment_state_text']);
            }
        }
        //显示预存款、支付密码、充值卡
        $pay['member_available_pd'] = $this->member_info['available_predeposit'];
        $pay['member_available_rcb'] = $this->member_info['available_rc_balance'];
        $pay['member_paypwd'] = $this->member_info['member_paypwd'] ? true : false;
        $pay['pay_sn'] = $pay_sn;
        $pay['payed_amount'] = wtPriceFormat($pay['payed_rcb_amount'] + $pay['payed_pd_amount']);
        unset($pay['payed_pd_amount']);
        unset($pay['payed_rcb_amount']);
        $pay['pay_amount'] = wtPriceFormat($pay['pay_diff_amount']);
        unset($pay['pay_diff_amount']);
        $pay['member_available_pd'] = wtPriceFormat($pay['member_available_pd']);
        $pay['member_available_rcb'] = wtPriceFormat($pay['member_available_rcb']);
        $pay['payment_list'] = $payment_list ? array_values($payment_list) : array();
        output_data(array('pay_info' => $pay));
    }

    /**
     * AJAX验证支付密码
     */
    public function check_pd_pwdWt() {
        if (empty($_POST['password'])) {
            output_error('支付密码格式不正确');
        }
        $buyer_info = Model('member')->getMemberInfoByID($this->member_info['member_id'], 'member_paypwd');
        if ($buyer_info['member_paypwd'] != '') {
            if ($buyer_info['member_paypwd'] === md5($_POST['password'])) {
                output_data('1');
            }
        }
        output_error('支付密码验证失败');
    }

    /**
     * F码验证
     */
    public function check_fcodeWt() {
        $goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0) {
            output_error('商品ID格式不正确');
        }
        if ($_POST['fcode'] == '') {
            output_error('F码格式不正确');
        }
        $result = handle('buy')->checkFcode($goods_id, $_POST['fcode']);
        if ($result['state']) {
            output_data('1');
        } else {
            output_error('F码验证失败');
        }
    }
}
