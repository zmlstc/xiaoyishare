<?php
/**
 * 会员活动
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class member_actControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 购物车、直接购买第一步:选择收获地址和配置方式
     */
    public function act_joinWt() {
        $act_id = (int) $_POST['id'];
        $act_num = intval($_POST['num']);
		$member_id=$this->member_info['member_id'];
		if($act_id<1 || $act_num<=1){
			output_error('提交参数错误!');
		}

        //得到会员
        $model_member = Model('member');
        $memberinfo = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if($memberinfo['is_realverify']==1){
			$rv = Model('member_realverify')->getInfo(array('member_id'=>$member_id,'state'=>1));
			if(!empty($rv)){
				$member_idcard = $rv['cardid'];
				$sexint = (int)substr($member_idcard,16,1);
				
				$member_info['member_truename'] = $rv['truename'];
				$member_info['member_id'] = $memberinfo['member_id'];
				$member_info['member_name'] = $memberinfo['member_name'];
				$member_info['member_phone'] = $memberinfo['member_mobile'];
				$member_info['member_sex'] = ($sexint % 2 === 0) ? '女' : '男';
			}else{
				output_error('请先实名验证');
			}
		}else{
			output_error('请先实名验证!');
		}
		
		$activity   = Model('plat_act');
		$row    = $activity->getOneById($act_id);
		if (empty($row)) {
            output_error('活动参数错误');
        }
		if ($row['act_stime']>time()) {
            output_error('活动报名暂未开始');
        }
		if ($row['act_etime']<time()) {
            output_error('活动报名已结束');
        }
		if ($row['act_state']!=1) {
            output_error('活动暂停报名');
        }
		$model_order = Model('plat_act_orders');
		

		
		if($row['parent_id']>0){
			$where= array();
			$where['order_state']= ORDER_STATE_SUCCESS;
			$where['buyer_id']= $member_id;
			$ordernum = $model_order->getOrderCount($where);
			if($ordernum<1){
				output_error('请先报名主活动');
			}
				
		}
		
		$order = array();
		$pay_sn = $this->makePaySn($member_id);		
		
		$order['order_sn'] = $pay_sn;
		$order['pay_sn'] = $pay_sn;
		$order['parent_id'] = $row['parent_id'];
		$order['act_id'] = $act_id;
		$order['buyer_id'] = $member_id;
		$order['buyer_name'] = $this->member_info['member_name'];
		$order['add_time'] = TIMESTAMP;
		//$order['payment_code'] = $paycode;
		$order['order_state'] = ORDER_STATE_NEW; 
		$order['act_num'] = $act_num;
		$order['amount'] = $row['act_price'];
		$order['order_amount'] = floatval($row['act_price']*$act_num);
		$order['points'] = $row['act_points'];
		$order['order_points'] = intval($row['act_points']*$act_num);

		$order_id = $model_order->addOrder($order);
		if (!$order_id) {
			 output_error('订单保存失败[未生成订单数据]');
		}
		
		$result = array();
		$result['pay_sn'] =  $pay_sn;
		$result['amount'] =  $order['order_amount'];
		$result['order_id'] =  $order_id;
		output_data($result);
		
		exit;


    }
	
	    /**
     * 实物订单支付(新接口)
     */
    public function payWt() {
        $pay_sn = $_POST['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            output_error('该订单不存在');
        }

        //查询支付单信息
        $model_order= Model('plat_act_orders');
    
        //取订单列表
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_state'] = ORDER_STATE_NEW;
        
        $pay_info = $model_order->getOrderInfo($condition);
        if(empty($pay_info)){
            output_error('该订单不存在');
        }
		$model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if(!$member_info['is_buy']) output_error('您没有消费支付的权限,如有疑问请联系客服人员');
		
		$memberInfo = array();
		$memberInfo['member_id'] = $member_info['member_id'];
		$memberInfo['member_name'] = $member_info['member_name'];
		$memberInfo['available_predeposit'] = $member_info['available_predeposit'];
		$memberInfo['pp_state'] = $member_info['member_paypwd'] ? 1 : 0;
		
		
        $pay['cost_total'] = wtPriceFormat($pay_info['order_amount']);
        output_data(array('pay_info'=>$pay,'member_info' => $memberInfo));
    }
	
	
	
	
    /**
     * 支付扣费
     *
     */
    public function buy_step2Wt() {
        $param = array();
        $param['paycode'] = $_POST['paycode'];
		$ppwd = $_POST['ppwd'];
		
		$pay_sn = $_POST['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            output_error('该订单不存在');
        }

        //查询支付单信息
        $model_order= Model('plat_act_orders');
    
        //取订单列表
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_state'] = ORDER_STATE_NEW;
        
        $pay_info = $model_order->getOrderInfo($condition);
        if(empty($pay_info)){
            output_error('该订单不存在!');
        }

        $param['order_from'] = 2;
        $logic_buy = Handle('buy');
		if(!empty($param['paycode']) && $param['paycode']=='pdpay'){
			$param['pdpay'] = 'pdpay';
		}

		
		if(!in_array($param['paycode'],array('alipay','wxpay','wxpay_jsapi','pdpay','wxminipay')))
		{
			output_error('请选择正确的支付方式');
		}
		if($param['paycode']=='pdpay' &&(empty($ppwd)||$ppwd==''))
		{
			output_error('请输入支付密码！');
		}
       
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
		if($member_info['lock_ppwd_num']>=5&&$member_info['lock_ppwd_time']>=time()){
			output_error('账户已锁定,请在1小时后再试！');
		}
		if($param['paycode']=='pdpay' && $member_info['member_paypwd']!=md5($ppwd))
		{
			$where = array();
			$where['member_id'] = $this->member_info['member_id'];
			$where['addtime'] = array('gt',time()-30*60);
			$error_num =Model()->table('member_ppw_log')->where($where)->count();
				
			$update = array();
            $update['lock_ppwd_num'] = array('exp', "lock_ppwd_num + 1");
			if($error_num==0){
				$update['lock_ppwd_num'] = 1;
			}
            $update['lock_ppwd_time'] = time()+60*60;
			$updateSuccess = Model('member')->editMember(array('member_id' => $this->member_info['member_id']), $update);
			$insert = array();
			$insert['member_id'] = $this->member_info['member_id'];
			$insert['addtime'] = time();
			$insert['is_error'] = 1;
			$rs = Model()->table('member_ppw_log')->insert($insert);
			output_error('支付密码不正确！还剩'.(4-intval($member_info['lock_ppwd_num'])).'次数机会');
		}else{
			$update = array();
            $update['lock_ppwd_num'] = 0;
            $update['lock_ppwd_time'] = 0;
			$updateSuccess = Model('member')->editMember(array('member_id' => $this->member_info['member_id']), $update);
			
		}

        $result = $this->_pdPay($pay_info,$member_info);
        if(!$result['state']) {
            output_error($result['msg']);
        }
       // $order_info = $result['data']['order']; 
		
		//支付
		if($param['paycode']!='pdpay'){			
			if($param['paycode']=='wxpay_jsapi'){
				output_data(array('pay_sn' => $pay_info['pay_sn'],'payment_code'=>$pay_info['payment_code']));
			}else{
				$retInfo = $this->_api_pay($pay_info);
				//echo json_encode($result['data']);exit;
				output_data(array('pay_sn' => $pay_info['pay_sn'],'payment_code'=>$pay_info['payment_code'],'payinfo'=>$retInfo));
			}
		}else{
			output_data(array('state' =>1,'msg'=>'支付成功！'));
		}
    }
	 
    /**
     * 预存款支付,依次循环每个订单
     * 如果预存款足够就单独支付了该订单，如果不足就暂时冻结，等API支付成功了再彻底扣除
     */
    public function _pdPay($order_info, $buyer_info) {
        $member_id = $buyer_info['member_id'];
        $member_name = $buyer_info['member_name'];
    
        $available_pd_amount = floatval(wtPriceFormat($buyer_info['available_predeposit']));
        if ($available_pd_amount <= 0) {
			 throw new Exception('账户余额不足本次支付');
		}
   
        $model_order = Model('plat_act_orders');
        $model_pd = Model('predeposit');
    

		$order_amount = floatval($order_info['order_amount']);
		
		$data_pd = array();
		$data_pd['member_id'] = $member_id;
		$data_pd['member_name'] = $member_name;
		$data_pd['amount'] = $order_amount;
		$data_pd['order_sn'] = $order_info['order_sn'];

		if ($available_pd_amount >= $order_amount) {
			//预存款立即支付，订单支付完成
			$model_pd->changePd('order_pay',$data_pd);
		   

			//记录订单日志(已付款)
		   /*  $data = array();
			$data['order_id'] = $order_info['order_id'];
			$data['log_role'] = 'buyer';
			$data['log_msg'] = '支付订单';
			$data['log_orderstate'] = ORDER_STATE_PAY;
			$insert = $model_order->addOrderLog($data);
			if (!$insert) {
				throw new Exception('记录订单预存款支付日志出现错误');
			} */
		

			//订单状态 置为已支付
			$data_order = array();
			$data_order['order_state'] = ORDER_STATE_PAY;
			$data_order['payment_time'] = TIMESTAMP;
			$data_order['payment_code'] = 'predeposit';
			
			$data_order['pd_amount'] = $order_amount;
			
			
			$result = $model_order->editOrder($data_order,array('order_id'=>$order_info['order_id']));
			if (!$result) {
				throw new Exception('订单更新失败');
			}
	
			
			
			//支付成功发送买家消息
			$param = array();
			$param['code'] = 'order_payment_success';
			$param['member_id'] = $order_info['buyer_id'];
			$param['param'] = array(
				'store_name' => $order_info['store_name'],
				'money' => $order_info['order_amount'],
					'order_sn' => $order_info['order_sn'],
					//'order_url' => urlShop('member_order', 'show_order', array('order_id' => $order_info['order_id'])),
					'url_wx'  => WAP_SITE_URL.'/html/member/order_detail.html?order_id='.$order_info['order_id']
			);
			QueueClient::push('sendMemberMsg', $param);
			
			
			
		} else {
			
			 throw new Exception('账户余额不足本次支付!');
			
	   
		}
        
        return $order_info;
    }
	
	private function _api_pay($order_info) {
		
		$model_mb_payment = Model('mb_payment');
		$condition = array();
		$condition['payment_code'] = $order_info['payment_code'];
		$mb_payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
		if(!$mb_payment_info) {
			output_error('支付方式未开启');
		}
			
        $inc_file = BASE_PATH.DS.'api'.DS.'payment'.DS.$order_info['payment_code'].DS.$order_info['payment_code'].'.php';
        if(!is_file($inc_file)){
            output_error('支付接口不存在');
        }
        require($inc_file);
        $param = $mb_payment_info['payment_config'];
			
		$payInfo= '';
		if ($order_info['payment_code'] == 'wxminipay') {
            $param['orderSn'] = $order_info['pay_sn'].''.mt_rand(1000,9999);
            $param['orderFee'] = (int) (wtPriceFormat(100 * $order_info['order_amount']));
            $param['orderInfo'] = $order_info['pay_sn'] . '订单';
            $param['orderAttach'] = 'r';//($order_info['order_type'] == 'real_order' ? 'r' : 'v');
			$param['js_code'] = $_POST['wxcode'];
            $api = new wxminipay();
            $api->setConfigs($param);
            try {
                $payInfo= $api->paymentHtml($this);
            } catch (Exception $ex) {
				output_error('支付接口请求异常！'.json_encode($param));
               
            }
            
                    
			return $payInfo;
			exit;
        }else if($order_info['payment_code']=='alipay'){
			$param['order_sn'] = $order_info['pay_sn'];
			$param['order_amount'] = $order_info['order_amount'];
			$param['order_type'] = 'r';
			$payment_api = new alipay();
			$return = $payment_api->submit($param);
			return $return;
			exit;
		}else if ($order_info['payment_code'] == 'wxpay_jsapi') {
            $param['orderSn'] = $order_info['pay_sn'].''.mt_rand(1000,9999);
            $param['orderFee'] = (int) (wtPriceFormat(100 * $order_info['order_amount']));
            $param['orderInfo'] = $order_info['pay_sn'] . '订单';
            $param['orderAttach'] = 'r';
			$param['js_code'] = $_POST['wxcode'];
            $api = new wxpay_jsapi();
            $api->setConfigs($param);
            try {
                $payInfo= $api->paymentHtml($this);
            } catch (Exception $ex) {
			Log::record('wxpay_jsapi支付接口请求异常！'.json_encode($param));
                output_error('支付接口请求异常！'.json_encode($param));
            }
			return $payInfo;
            exit;
        }

    }
	
	/*wx_jsapi支付*/
	public function pay_wxjsapiWt() {
		$paysn= trim($_POST['paysn']);
		$wxcode= trim($_POST['wxcode']);
		$condition = array();
		$condition['pay_sn'] = $paysn;
		$condition['buyer_id'] = $this->member_info['member_id'];
		$condition['order_state'] = array('in',array(ORDER_STATE_NEW));
		$orderinfo = Model('store_order')->getOrderInfo($condition);
		if(empty($orderinfo)){
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
		$order_info['discount_money'] = floatval($orderinfo['goods_amount']-$orderinfo['order_amount']);
		
		// 店铺头像
        $order_info['store_avatar'] = $store_online_info['store_avatar']
            ? UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store_online_info['store_avatar']
            : UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');
		
		output_data(array('pay_sn' => $result['data']['pay_sn'],'payment_code'=>$orderinfo['payment_code'],'payinfo'=>$retInfo,'orderinfo'=>$order_info));
		
		
		
		output_data(array('pay_sn' => $result['data']['pay_sn'],'payment_code'=>$orderinfo['payment_code'],'payinfo'=>$retInfo));
		
		
	}
	

    /**
     * 验证密码
     */
    public function check_passwordWt() {
        if(empty($_POST['password'])) {
            output_error('参数错误');
        }

        $model_member = Model('member');

        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if($member_info['member_paypwd'] == md5($_POST['password'])) {
            output_data('1');
        } else {
            output_error('密码错误');
        }
    }




    /**
     * AJAX验证支付密码
     */
    public function check_pd_pwdWt(){
        if (empty($_POST['password'])) {
            output_error('支付密码格式不正确');
        }
        $buyer_info = Model('member')->getMemberInfoByID($this->member_info['member_id'],'member_paypwd');
        if ($buyer_info['member_paypwd'] != '') {
            if ($buyer_info['member_paypwd'] === md5($_POST['password'])) {
                output_data('1');
            }
        }
        output_error('支付密码验证失败');
    }
	public function makePaySn($member_id) {
        return mt_rand(10,99)
              . sprintf('%010d',time() - 946656000)
              . sprintf('%03d', (float) microtime() * 1000)
              . sprintf('%03d', (int) $member_id % 1000);
    }

}
