<?php
/**
 * 店铺订单行为
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class store_orderHandle {

    /**
     * 取消订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @param boolean $if_update_account 是否变更账户金额
     * @param array $cancel_condition 订单更新条件,目前只传入订单状态，防止并发下状态已经改变
     * @return array
     */
    public function changeOrderStateCancel($order_info, $role, $user = '', $msg = '', $if_update_account = true, $cancel_condition = array()) {
        try {
            $model_order = Model('order');
            $model_order->beginTransaction();
            $order_id = $order_info['order_id'];
            $_info = $model_order->table('orders')->where(array('order_id'=> $order_id))->master(true)->lock(true)->find();
            if ($_info['order_state'] == ORDER_STATE_CANCEL) {
                throw new Exception('参数错误');
            }

            //库存销量变更
            $goods_list = $model_order->getOrderGoodsList(array('order_id'=>$order_id));
			if(empty($goods_list)){
				 return callback(true,'订单商品不存在');
			}
            $data = array();
            $goods_sale = array();
            foreach ($goods_list as $goods) {
                $data[$goods['goods_id']] = $goods['goods_num'];
                $goods_sale[$goods['goods_id']] = $goods['goods_commonid'];
            }
            $result = Handle('queue')->cancelOrderUpdateStorage($data, $goods_sale);
            if (!$result['state']) {
                throw new Exception('还原库存失败');
            }
            if ($order_info['chain_id']) {
                $result = Handle('queue')->cancelOrderUpdateChainStorage($data,$order_info['chain_id']);
                if (!$result['state']) {
                    throw new Exception('还原门店库存失败');
                }
            }

            if ($if_update_account) {
                $model_pd = Model('predeposit');
                //解冻充值卡
                $rcb_amount = floatval($order_info['rcb_amount']);
                if ($rcb_amount > 0) {
                    $data_pd = array();
                    $data_pd['member_id'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $rcb_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $model_pd->changeRcb('order_cancel',$data_pd);
                }

                //解冻预存款
                $pd_amount = floatval($order_info['pd_amount']);
                if ($pd_amount > 0) {
                    $data_pd = array();
                    $data_pd['member_id'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $pd_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $model_pd->changePd('order_cancel',$data_pd);
                }
            }

            //更新订单信息
            $update_order = array('order_state'=>ORDER_STATE_CANCEL);
            $cancel_condition['order_id'] = $order_id;
            $cancel_condition['order_state'] = array('neq', ORDER_STATE_CANCEL);
            $update = $model_order->editOrder($update_order,$cancel_condition);
            if (!$update) {
                throw new Exception('保存失败');
            }

            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_msg'] = '取消了订单';
            $data['log_user'] = $user;
            if ($msg) {
                $data['log_msg'] .= ' ( '.$msg.' )';
            }
            $data['log_orderstate'] = ORDER_STATE_CANCEL;
            $model_order->addOrderLog($data);
            $model_order->commit();

            Model('voucher')->returnVoucher($order_info['order_id']);

            return callback(true,'操作成功');

        } catch (Exception $e) {
            $model_order->rollback();
            return callback(false,'操作失败');
        }
    }

    /**
     * 收货
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system,chain 分别代表买家、商家、管理员、系统、门店
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @return array
     */
    public function changeOrderStateReceive($order_info, $role, $user = '', $msg = '') {
        try {

            $order_id = $order_info['order_id'];
            $model_order = Model('order');

            //更新订单状态
            $update_order = array();
            $update_order['finnshed_time'] = TIMESTAMP;
            $update_order['order_state'] = ORDER_STATE_SUCCESS;
            $update = $model_order->editOrder($update_order,array('order_id'=>$order_id));
            if (!$update) {
                throw new Exception('保存失败');
            }

            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_msg'] = $msg;
            $data['log_user'] = $user;
            $data['log_orderstate'] = ORDER_STATE_SUCCESS;
            $model_order->addOrderLog($data);

            if ($order_info['buyer_id'] > 0 && $order_info['order_amount'] > 0) {
                //添加会员积分
                if (C('points_isuse') == 1){
                    Model('points')->savePointsLog('order',array('pl_memberid'=>$order_info['buyer_id'],'pl_membername'=>$order_info['buyer_name'],'orderprice'=>$order_info['order_amount'],'order_sn'=>$order_info['order_sn'],'order_id'=>$order_info['order_id']),true);
                }
                //添加会员经验值
                Model('exppoints')->saveExppointsLog('order',array('exp_memberid'=>$order_info['buyer_id'],'exp_membername'=>$order_info['buyer_name'],'orderprice'=>$order_info['order_amount'],'order_sn'=>$order_info['order_sn'],'order_id'=>$order_info['order_id']),true);
		// 邀请人获得返利积分
		$this->addInviteRate($order_info);
		
		//邀请人获得返利积分
			/* $model_member = Model('member');
			$inviter_id = $model_member->table('member')->getfby_member_id($order_info['buyer_id'],'inviter_id');
			$inviter_name = $model_member->table('member')->getfby_member_id($inviter_id,'member_name');
			$rebate_amount = ceil(0.01 * $order_info['order_amount'] * $GLOBALS['setting_config']['points_rebate']);
			$desc = '被邀请人['.$order_info['buyer_name'].']消费';
			Model('points')->savePointsLog('rebate',array('pl_memberid'=>$inviter_id,'pl_membername'=>$inviter_name,'rebate_amount'=>$rebate_amount,'pl_desc'=>$desc),true);
		*/
            }

            return callback(true,'操作成功');
        } catch (Exception $e) {
            return callback(false,'操作失败');
        }
    }

    /**
     * 更改运费
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param float $price 运费
     * @return array
     */
    public function changeOrderShipPrice($order_info, $role, $user = '', $price) {
        try {

            $order_id = $order_info['order_id'];
            $model_order = Model('order');

            $data = array();
            $data['shipping_fee'] = abs(floatval($price));
            $data['order_amount'] = array('exp','goods_amount+'.$data['shipping_fee']);
            $update = $model_order->editOrder($data,array('order_id'=>$order_id));
            if (!$update) {
                throw new Exception('保存失败');
            }
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_user'] = $user;
            $data['log_msg'] = '修改了运费'.'( '.$price.' )';;
            $data['log_orderstate'] = $order_info['payment_code'] == 'offline' ? ORDER_STATE_PAY : ORDER_STATE_NEW;
            $model_order->addOrderLog($data);
            return callback(true,'操作成功');
        } catch (Exception $e) {
            return callback(false,'操作失败');
        }
    }
    /**
     * 更改价格
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param float $price 价格
     * @return array
     */
    public function changeOrderSpayPrice($order_info, $role, $user = '', $price) {
        try {

            $order_id = $order_info['order_id'];
            $model_order = Model('order');

            $data = array();
            $data['goods_amount'] = abs(floatval($price));
            $data['order_amount'] = array('exp','shipping_fee+'.$data['goods_amount']);
            $update = $model_order->editOrder($data,array('order_id'=>$order_id));
            if (!$update) {
                throw new Exception('保存失败');
            }
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_user'] = $user;
            $data['log_msg'] = '修改了价格'.'( '.$price.' )';;
            $data['log_orderstate'] = $order_info['payment_code'] == 'offline' ? ORDER_STATE_PAY : ORDER_STATE_NEW;
            $model_order->addOrderLog($data);
            return callback(true,'操作成功');
        } catch (Exception $e) {
            return callback(false,'操作失败');
        }
    }
    /**
     * 回收站操作（放入回收站、还原、永久删除）
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $state_type 操作类型
     * @return array
     */
    public function changeOrderStateRecycle($order_info, $role, $state_type) {
        $order_id = $order_info['order_id'];
        $model_order = Model('store_order');
        //更新订单删除状态
        $state = str_replace(array('delete','drop','restore'), array(ORDER_DEL_STATE_DELETE,ORDER_DEL_STATE_DROP,ORDER_DEL_STATE_DEFAULT), $state_type);
        $update = $model_order->editOrder(array('delete_state'=>$state),array('order_id'=>$order_id));
        if (!$update) {
            return callback(false,'操作失败!');
        } else {
            return callback(true,'操作成功');
        }
    }

    /**
     * 发货
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @return array
     */
    public function changeOrderSend($order_info, $role, $user = '', $post = array()) {
        $order_id = $order_info['order_id'];
        $model_order = Model('order');
        try {
            $model_order->beginTransaction();
            $data = array();
            if (!empty($post['reciver_name'])) {
                $data['reciver_name'] = $post['reciver_name'];
            }
            if (!empty($post['reciver_info'])) {
                $data['reciver_info'] = $post['reciver_info'];
            }
            $data['deliver_explain'] = $post['deliver_explain'];
            $data['daddress_id'] = intval($post['daddress_id']);
            $data['shipping_express_id'] = intval($post['shipping_express_id']);
            $data['shipping_time'] = TIMESTAMP;

            $condition = array();
            $condition['order_id'] = $order_id;
            $condition['store_id'] = $order_info['store_id'];
            $update = $model_order->editOrderCommon($data,$condition);
            if (!$update) {
                throw new Exception('操作失败');
            }

            $data = array();
            $data['shipping_code']  = $post['shipping_code'];
            $data['order_state'] = ORDER_STATE_SEND;
            $data['delay_time'] = TIMESTAMP;
            $update = $model_order->editOrder($data,$condition);
            if (!$update) {
                throw new Exception('操作失败');
            }
            $model_order->commit();
        } catch (Exception $e) {
            $model_order->rollback();
            return callback(false,$e->getMessage());
        }
      
		//微信通知信息
		$wx_msg = array();
		$wx_msg['order_sn'] = $order_info['order_sn'];
		$wx_msg['e_name'] = '';
		$wx_msg['shipping_code'] = $post['shipping_code'];
		
		if ($post['shipping_express_id'] ) {           
            $express_info = Model('express')->getExpressInfo(intval($post['shipping_express_id']));
			$wx_msg['e_name'] = $express_info['e_name'];
        }
		
        //更新表发货信息
        if ($post['shipping_express_id'] && $order_info['extend_order_common']['reciver_info']['dlyp']) {
            $data = array();
            $data['shipping_code'] = $post['shipping_code'];
            $data['order_sn'] = $order_info['order_sn'];
            $express_info = Model('express')->getExpressInfo(intval($post['shipping_express_id']));
            $data['express_code'] = $express_info['e_code'];
            $data['express_name'] = $express_info['e_name'];
			
            Model('delivery_order')->editDeliveryOrder($data,array('order_id' => $order_info['order_id']));
        }

        //添加订单日志
        $data = array();
        $data['order_id'] = $order_id;
        $data['log_role'] = 'seller';
        $data['log_user'] = $user;
        $data['log_msg'] = '发出货物(编辑信息)';
        $data['log_orderstate'] = ORDER_STATE_SEND;
        $model_order->addOrderLog($data);

        // 发送买家消息
        $param = array();
        $param['code'] = 'order_deliver_success';
        $param['member_id'] = $order_info['buyer_id'];
        $param['param'] = array(
            'order_sn' => $order_info['order_sn'],
            'order_url' => urlShop('member_order', 'show_order', array('order_id' => $order_id))
        );
		
		$param['param']['mp_array'] = Handle('wx_api')->getTemplateData($param['code'], $wx_msg);
		$param['param']['mp_array']['url'] = WAP_SITE_URL.'/html/member/order_detail.html?order_id='.$order_info['order_id'];
        QueueClient::push('sendMemberMsg', $param);

        return callback(true,'操作成功');
    }

    /**
     * 收到货款
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @return array
     */
    public function changeOrderReceivePay($order_list, $role, $user = '', $post = array()) {
        $model_order = Model('store_order');
		Log::record('===rec_pay=='.json_encode($order_list));
        try {
            $model_order->beginTransaction();
			$order_info = $model_order->getOrderInfo(array('order_id'=>$order_list[0]['order_id']));
			if (!empty($order_info)&&is_array($order_info)) {
                if ($order_info['order_state'] > 10) {
                    return callback(true,'操作成功');
                }
            }else{
				return callback(true,'操作成功');
			}
           /*  $pay_info = $model_order->getOrderPayInfo(array('pay_sn'=>$order_list[0]['pay_sn']));
            if ($pay_info) {
                if ($pay_info['api_pay_state'] == 1) {
                    return callback(true,'操作成功');
                }
                $pay_info = $model_order->getOrderPayInfo(array('pay_id'=>$pay_info['pay_id']), true,true);
                if ($pay_info['api_pay_state'] == 1) {
                    return callback(true,'操作成功');
                }
            } */
            $model_pd = Model('predeposit');
            //foreach($order_list as $order_info) 
			//$order_info = $order_list[0];
			$order_amount = floatval($order_info['order_amount']);
			
				Log::record('===rec_pay=22='.json_encode($order_info));
                $order_id = $order_info['order_id'];
                //if (!in_array($order_info['order_state'],array(ORDER_STATE_NEW))) continue;
                //下单，支付被冻结的充值卡
       /*          $rcb_amount = floatval($order_info['rcb_amount']);
                if ($rcb_amount > 0) {
                    $data_pd = array();
                    $data_pd['member_id'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $rcb_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $model_pd->changeRcb('order_comb_pay',$data_pd);
                }

                //下单，支付被冻结的预存款
                $pd_amount = floatval($order_info['pd_amount']);
                if ($pd_amount > 0) {
                    $data_pd = array();
                    $data_pd['member_id'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $pd_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $model_pd->changePd('order_comb_pay',$data_pd);
                } */

                //更新订单相关扩展信息
              /*   $result = $this->_changeOrderReceivePayExtend($order_info,$post);
                if (!$result['state']) {
                    throw new Exception($result['msg']);
                } */

                //添加订单日志
                $data = array();
                $data['order_id'] = $order_id;
                $data['log_role'] = $role;
                $data['log_user'] = $user;
                $data['log_msg'] = '收到货款(外部交易号:'.$post['trade_no'].')';
                $data['log_orderstate'] = ORDER_STATE_PAY;
                $insert = $model_order->addOrderLog($data);
                if (!$insert) {
                    throw new Exception('操作失败');
                }
				
				//----------------------------------------------------
				
				$storeinfo = Model('store')->getStoreOnlineInfoByID($order_info['store_id']);
				if (!empty($storeinfo)) {
					$store_invite_id = $storeinfo['invite_bm'];
					$sc_id = $storeinfo['scid2'];
				}
				$invite_info = array();
				//$commis_rate = 0;
				$seller_money = 0;//商家金额
				$sys_commis_momey = 0;//平台金额
				$member_invite_momey = 0;//邀请会员获得金额
				$store_invite_momey = 0;//邀请店铺获得金额
				$cost_momey = 0;//消费者返利金额
				$cost_points = 0; //消费返利转成积分
				$agent_momey = 0;//代理获得金额
				$commis_momey = 0;//四家分佣总金额
				$gc_info = Model('store_class')->getStoreClassInfo(array('sc_id'=>$sc_id));
				if (!empty($gc_info)) {
					$commis_rate = floatval($gc_info['commis_rate']);
					if($storeinfo['store_rate']>0){
						$commis_rate = floatval($storeinfo['store_rate']);
					}
					$agentInfo = Model('agent')->getAgentInfoByID($storeinfo['agent_id']);
					$agent_class = Model('agent_store_class')->getInfo(array('agent_id'=>$storeinfo['agent_id'],'sc_id'=>$sc_id));
					/* if(!empty($agent_class)&& $agent_class['agent_rate']>$gc_info['commis_rate']){
						$commis_rate = floatval($agent_class['agent_rate']);
					} */
					if(!empty($agent_class)&& $agent_class['agent_rate']>$commis_rate){
						$commis_rate = floatval($agent_class['agent_rate']);
					}
					
					
					//$commis_rate = $gc_info['commis_rate'];
					$commis_rate_invstore = floatval($gc_info['commis_rate_invstore']);//推广返利店铺
					$commis_rate_invmember = floatval($gc_info['commis_rate_invmember']);//推广返利会员
					$commis_rate_system = floatval($gc_info['commis_rate_system']);//代理
					$commis_rate_points = floatval($gc_info['commis_rate_points']);//积分
					$commis_rate_sys = floatval($agentInfo['agent_rate']);//平台金额
					
					
					$invite_info['commis_rate']=$commis_rate;
					$invite_info['commis_rate_invstore']=$commis_rate_invstore;
					$invite_info['commis_rate_invmember']=$commis_rate_invmember;
					$invite_info['commis_rate_system']=$commis_rate_system;
					$invite_info['commis_rate_points']=$commis_rate_points;
					$invite_info['commis_rate_sys']=$commis_rate_sys;
					
					$sys_commis_momey = number_format($commis_rate_sys*$order_amount*0.01, 3);
					$sys_commis_momey = ($order_amount<$sys_commis_momey)? $order_amount:$sys_commis_momey;
					
					$seller_money = floatval($order_amount-$sys_commis_momey);
					
					$commis_momey = number_format($commis_rate*$order_amount*0.01, 3);
					$commis_momey = ($seller_money<$commis_momey)? $seller_money:$commis_momey;
					
					$seller_money = floatval($seller_money-$commis_momey);
					
					/*****4家分开始****/
					$member_invite_momey = number_format($commis_rate_invmember*$commis_momey*0.01, 3);
					$member_invite_momey = ($commis_momey<$member_invite_momey)? $commis_momey:$member_invite_momey;
					$_temp_momey = $commis_momey-$member_invite_momey;
					
					$store_invite_momey = number_format($commis_rate_invstore*$commis_momey*0.01, 3);
					$store_invite_momey = ($_temp_momey<$store_invite_momey)? $_temp_momey:$store_invite_momey;
					$_temp_momey = $_temp_momey-$store_invite_momey;
					
					$cost_momey = number_format($commis_rate_points*$commis_momey*0.01, 3);
					$cost_momey = ($_temp_momey<$cost_momey)? $_temp_momey:$cost_momey;
					$_temp_momey = $_temp_momey-$cost_momey;
					
					$agent_momey = ($_temp_momey<0)? 0:$_temp_momey;
					
					
					$cost_points_bl = intval(C('cost_points_bl')); 
					
					$cost_points =  intval(round($cost_momey*$cost_points_bl,1)); 
					//不足1积分设置为1
					if($cost_points<1){$cost_points=1;}
					
					/*****4家分结束****/
				
				}
				
				//---------------------------------------------------------
				
				

                //更新订单状态
                $update_order = array();
                $update_order['order_state'] = ORDER_STATE_PAY;
                $update_order['payment_time'] = ($post['payment_time'] ? strtotime($post['payment_time']) : TIMESTAMP);
                $update_order['payment_code'] = $post['payment_code'];
                if ($post['trade_no'] != '') {
                    $update_order['trade_no'] = $post['trade_no'];
                }
				$update_order['invite_info'] = json_encode($invite_info);
                $update_order['seller_money'] = $seller_money;
                $update_order['commis_rate'] = $commis_rate;
                $update_order['sys_commis_momey'] = $sys_commis_momey+$cost_momey;
                $update_order['member_invite_momey'] = $member_invite_momey;
                $update_order['store_invite_momey'] = $store_invite_momey;
                $update_order['cost_momey'] = $cost_momey;
                $update_order['cost_points'] = $cost_points;
                $update_order['agent_commis_momey'] = $agent_momey;
                $update_order['agent_id'] = $storeinfo['agent_id'];
				
				
                $condition = array();
                $condition['order_id'] = $order_info['order_id'];
                $condition['order_state'] = ORDER_STATE_NEW;
                $update = $model_order->editOrder($update_order,$condition);
                if (!$update) {
                    throw new Exception('操作失败');
                }
            

            //更新支付单状态
            $data = array();
            $data['api_pay_state'] = 1;
            $update = $model_order->editOrderPay($data,array('pay_sn'=>$order_info['pay_sn']));
            if (!$update) {
                throw new Exception('更新支付单状态失败');
            }
			$model_pd = Model('predeposit');
			   //店铺结算 变更会员预存款
				if($seller_money>0){
				   $data = array();
				   $data['msg']="";
				   if($sys_commis_momey>0)
				   {
						$data['msg']=$sys_commis_momey;
				   }
				   $data['store_id'] = $storeinfo['store_id'];
				   $data['store_name'] = $storeinfo['store_name'];
				   $data['amount'] = $seller_money;
				   $data['pdr_sn'] = $order_info['order_sn'];
				   $data['paytype'] = $order_info['payment_code'];
				   Model('store_predeposit')->changePd('seller_money',$data);
				 }
			   
				
			   if($member_invite_momey>0){
				   //邀请会员消费
				   $member_invite_info = Model('member')->getMemberInfoByID($buyer_info['inviter_id']);
				   if(!empty($member_invite_info) && is_array($member_invite_info)){
					   $data = array();
					   $data['member_id'] = $member_invite_info['member_id'];
					   $data['member_name'] = $member_invite_info['member_name'];
					   $data['amount'] = $member_invite_momey;
					   $data['order_sn'] = $order_info['order_sn'];
					   $data['invite_member_id'] = $order_info['buyer_id'];
					   $data['invite_member_name'] = $order_info['buyer_name'];
					   $model_pd->changePd('member_invite',$data);
				   }
			   }
				
			   if($store_invite_momey>0){
				   //邀请店铺消费
				   $store_invite_info = Model('member')->getMemberInfoByID($store_invite_id);
				   if(!empty($store_invite_info) && is_array($store_invite_info)){
					   $data = array();
					   $data['member_id'] = $store_invite_info['member_id'];
					   $data['member_name'] = $store_invite_info['member_name'];
					   $data['amount'] = $store_invite_momey;
					   $data['order_sn'] = $order_info['order_sn'];
					   $data['invite_store_id'] = $order_info['store_id'];
					   $data['invite_store_name'] = $order_info['store_name'];
					   $model_pd->changePd('store_invite',$data);
				   }
			   }
				
			     if($cost_points>0){
				   //消费获得积分
				    Model('points')->savePointsLog('order',array('pl_memberid'=>$order_info['buyer_id'],'pl_membername'=>$order_info['buyer_name'],'pl_points'=>$cost_points,'order_sn'=>$order_info['order_sn'],'order_id'=>$order_info['order_id']));
				   
			   }
			   
			  // 发送商家提醒
				$param = array();
				$param['code'] = 'new_order';
				$param['store_id'] = $order_info['store_id'];
				$param['param'] = array(
						'order_sn' => $order_info['order_sn']
				);
				QueueClient::push('sendStoreMsg', $param);
				
				//支付成功发送买家消息
				$param = array();
				$param['code'] = 'order_payment_success';
				$param['member_id'] = $order_info['buyer_id'];
				$param['param'] = array(
                    'store_name' => $order_info['store_name'],
                    'money' => $order_info['order_amount'],
						'order_sn' => $order_info['order_sn'],
						'order_url' => urlShop('member_order', 'show_order', array('order_id' => $order_info['order_id'])),
						'url_wx'  => WAP_SITE_URL.'/html/member/order_detail.html?order_id='.$order_info['order_id']
				);
				QueueClient::push('sendMemberMsg', $param);
				
				//发送消息（会员邀请人）
				$param = array();
				$param['code'] = 'inviter_member_consume';
				$param['member_id'] = $buyer_info['inviter_id'];
				$param['param'] = array(
						'nickname' => $buyer_info['nickname']
				);
				QueueClient::push('sendMemberMsg', $param);
				
				//发送消息（店铺邀请人）
				$param = array();
				$param['code'] = 'inviter_member_sell';
				$param['member_id'] = $store_invite_id;
				$param['param'] = array(
						'store_name' => $storeinfo['store_name']
				);
				QueueClient::push('sendMemberMsg', $param); 
			   
				

            $model_order->commit();
        } catch (Exception $e) {
            $model_order->rollback();
            return callback(false,$e->getMessage());
        }

        //foreach($order_list as $order_info) {
			//防止重复发送消息 
		/* 	if ($order_info['order_state'] != ORDER_STATE_NEW) continue;
            $order_id = $order_info['order_id'];
            //支付成功发送买家消息
            $param = array();
            $param['code'] = 'order_payment_success';
            $param['member_id'] = $order_info['buyer_id'];
            $param['param'] = array(
                    'order_sn' => $order_info['order_sn'],
                    'order_url' => urlShop('member_order', 'show_order', array('order_id' => $order_info['order_id']))
            );				
			$param['param']['mp_array'] = Handle('wx_api')->getTemplateData($param['code'], $order_info);
			$param['param']['mp_array']['url'] = WAP_SITE_URL.'/html/member/order_detail.html?order_id='.$order_info['order_id'];
            QueueClient::push('sendMemberMsg', $param); */

            //非预定订单下单或预定订单全部付款完成
           /*  if ($order_info['order_type'] != 2 || $order_info['if_send_store_msg_pay_success']) {
                //支付成功发送店铺消息
                $param = array();
                $param['code'] = 'new_order';
                $param['store_id'] = $order_info['store_id'];
                $param['param'] = array(
                        'order_sn' => $order_info['order_sn']
                );
                QueueClient::push('sendStoreMsg', $param);
                //门店自提发送提货码
                if ($order_info['order_type'] == 3) {
                    $_code = rand(100000,999999);
                    $result = $model_order->editOrder(array('chain_code'=>$_code),array('order_id'=>$order_info['order_id']));
                    if (!$result) {
                        throw new Exception('订单更新失败');
                    }
                    $param = array();
                    $param['chain_code'] = $_code;
                    $param['order_sn'] = $order_info['order_sn'];
                    $param['buyer_phone'] = $order_info['buyer_phone'];
                    QueueClient::push('sendChainCode', $param);
                }
            } */
        //}

        return callback(true,'操作成功');
    }

    /**
     * 更新订单相关扩展信息
     * @param unknown $order_info
     * @return unknown
     */
    private function _changeOrderReceivePayExtend($order_info, $post) {
        //预定订单收款
        if ($order_info['order_type'] == 2) {
            $result = Handle('order_book')->changeBookOrderReceivePay($order_info, $post);
        }
        if ($order_info['order_type'] == 4) {//拼团订单
            $model_pingou = Model('p_pingou');
            $model_pingou->payOrder($order_info);
        }
        return callback(true);
    }

    /**
     * 买家订单详细
     */
    public function getMemberOrderInfo($order_id,$member_id) {
        $order_id = intval($order_id);
        $member_id = intval($member_id);
        if ($order_id <= 0) {
            return callback(false,'订单不存在');
        }

        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $member_id;
        $order_info = $model_order->getOrderInfo($condition,array('order_goods','order_common','store'));
        if (empty($order_info) || $order_info['delete_state'] == ORDER_DEL_STATE_DROP) {
            return callback(false,'订单不存在');
        }

        $model_refund_return = Model('refund_return');
        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $model_refund_return->getGoodsRefundList($order_list,1);//订单商品的退款退货显示
        $order_info = $order_list[$order_id];
        $refund_all = $order_info['refund_list'][0];
        if (!empty($refund_all) && $refund_all['seller_state'] < 3) {//订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
        } else {
            $refund_all = array();
        }

        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock',$order_info);

        //显示取消订单
        $order_info['if_buyer_cancel'] = $model_order->getOrderOperateState('buyer_cancel',$order_info);

        //显示退款取消订单
        $order_info['if_refund_cancel'] = $model_order->getOrderOperateState('refund_cancel',$order_info);

        //显示投诉
        $order_info['if_complain'] = $model_order->getOrderOperateState('complain',$order_info);

        //显示收货
        $order_info['if_receive'] = $model_order->getOrderOperateState('receive',$order_info);

        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info);

        //显示评价
        $order_info['if_evaluation'] = $model_order->getOrderOperateState('evaluation',$order_info);

        //显示分享
        $order_info['if_share'] = $model_order->getOrderOperateState('share',$order_info);

        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_TIME * 3600;
        }

        //显示快递信息
        if ($order_info['shipping_code'] != '') {
            $express = rkcache('express',true);
            $order_info['express_info']['e_code'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
            $order_info['express_info']['e_name'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
            $order_info['express_info']['e_url'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_url'];
        }

        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATE_SEND) {
            $order_info['order_confirm_day'] = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 24 * 3600;
        }

        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $order_info['close_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id']),'log_id desc');
        }
        //查询消费者保障服务
        if (C('contract_allow') == 1) {
            $contract_item = Model('contract')->getContractItemByCache();
        }
        foreach ($order_info['extend_order_goods'] as $value) {
            $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
            $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
            $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
            $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
            $value['refund'] = $value['refund'] ? 1 : 0;
            //处理消费者保障服务
            if (trim($value['goods_contractid']) && $contract_item) {
                $goods_contractid_arr = explode(',',$value['goods_contractid']);
                foreach ((array)$goods_contractid_arr as $gcti_v) {
                    $value['contractlist'][] = $contract_item[$gcti_v];
                }
            }
            if ($value['goods_type'] == 5) {
                $order_info['zengpin_list'][] = $value;
            } else {
                $order_info['goods_list'][] = $value;
            }
        }

        if (empty($order_info['zengpin_list'])) {
            $order_info['zengpin_list'] = array();
            $order_info['goods_count'] = count($order_info['goods_list']);
        } else {
            $order_info['goods_count'] = count($order_info['goods_list']) + 1;
        }

        //取得其它订单类型的信息
        $model_order->getOrderExtendInfo($order_info);

        //卖家发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
        } else {
            $daddress_info = array();
        }
        return callback(true,'',array('order_info'=>$order_info,'refund_all'=>$refund_all,'daddress_info'=>$daddress_info));
    }
	
	/**
     * shopwt 5.2 邀请 添加相关积分
     * @param unknown $order_info
     * @return unknown
     */
	public function addInviteRate($order_info){
		if(!$GLOBALS['setting_config']['invite_points_isuse']) return ;
		$model_member = Model('member');
		$model_points = Model('points');
		$invite_info=Model('member')->table('member')->where(array('member_id'=>$order_info['buyer_id']))->find();
		//取得一级推荐会员
		$invite_one_id = $invite_info['invite_one'];
		$invite_one_name = $model_member->table('member')->getfby_member_id($invite_one_id,'member_name');
		//取得二级推荐会员
		$invite_two_id = $invite_info['invite_two'];
		$invite_two_name = $model_member->table('member')->getfby_member_id($invite_two_id,'member_name');
		//取得三级推荐会员
		$invite_three_id = $invite_info['invite_three'];
		$invite_three_name = $model_member->table('member')->getfby_member_id($invite_three_id,'member_name');
		
		
		$rebate_amount1 = ceil(0.01 * $order_info['order_amount'] * $GLOBALS['setting_config']['invite_points_one']);
		$rebate_amount2 = ceil(0.01 * $order_info['order_amount'] * $GLOBALS['setting_config']['invite_points_two']);
		$rebate_amount3 = ceil(0.01 * $order_info['order_amount'] * $GLOBALS['setting_config']['invite_points_three']);
		$desc = '被邀请人['.$order_info['buyer_name'].']消费';
		$model_points->savePointsLog('rebate',array('pl_memberid'=>$invite_one_id,'pl_membername'=>$invite_one_name,'rebate_amount'=>$rebate_amount1,'inviter_memberid'=>$order_info['buyer_id'],'pl_desc'=>$desc),true);
		$model_points->savePointsLog('rebate',array('pl_memberid'=>$invite_two_id,'pl_membername'=>$invite_two_name,'rebate_amount'=>$rebate_amount2,'inviter_memberid'=>$order_info['buyer_id'],'pl_desc'=>$desc),true);
		$model_points->savePointsLog('rebate',array('pl_memberid'=>$invite_three_id,'pl_membername'=>$invite_three_name,'rebate_amount'=>$rebate_amount3,'inviter_memberid'=>$order_info['buyer_id'],'pl_desc'=>$desc),true);

		
		
			/* $model_order = Model('order');
			$invite_info=Model('member')->table('member')->where(array('member_id'=>$order_info['buyer_id']))->find();
			$invite_money=0;
			//取得拥金金额
			 $field = 'SUM(goods_num*invite_rates) as commis_amount';
			 $order_goods_condition['order_id'] = $order_info['order_id'];
		     $order_goods_condition['buyer_id'] = $order_info['buyer_id'];
             $order_goods_content = $model_order->getOrderGoodsInfo($order_goods_condition,$field);
             $commis_rate_totals_array[] = $order_goods_content['commis_amount'];
			 $commis_amount_sum=floatval(array_sum($commis_rate_totals_array)); 
			  
			 if($commis_amount_sum>0) {
				  $invite_money=$commis_amount_sum;
				  $im_arr=explode('.',$commis_amount_sum * $GLOBALS['setting_config']['wt_invite2']*0.01);
				  $invite_money2 = $im_arr[0].'.'.substr($im_arr[1],0,2);
				  $im_arr=explode('.',$commis_amount_sum * $GLOBALS['setting_config']['wt_invite3']*0.01);
				  $invite_money3 = $im_arr[0].'.'.substr($im_arr[1],0,2);
			 }
			//检测是否货到付款方式
			$is_offline=($order_info['payment_code']=="offline");
			$model_member = Model('member');
			//取得一级推荐会员
			$invite_one_id = $invite_info['invite_one'];
			//$model_member->table('member')->getfby_member_id($invite_info['invite_one'],'invite_one');
			$invite_one_name = $model_member->table('member')->getfby_member_id($invite_one_id,'member_name');
			//取得二级推荐会员
			$invite_two_id = $invite_info['invite_two'];
			//$model_member->table('member')->getfby_member_id($invite_info['member_id'],'invite_two');
			$invite_two_name = $model_member->table('member')->getfby_member_id($invite_two_id,'member_name');
			//取得三级推荐会员
			$invite_three_id = $invite_info['invite_three'];
			//$model_member->table('member')->getfby_member_id($invite_info['member_id'],'invite_three');
			$invite_three_name = $model_member->table('member')->getfby_member_id($invite_three_id,'member_name');
			
		     if($invite_money>0&&$is_offline==false){
		     	
				$all_invite_money=floatval($invite_money)+floatval($invite_money2)+floatval($invite_money3);
				$storeinfo=Model('store')->getStoreInfoByID($order_info['store_id']);
				if(empty($storeinfo)) return;
			    $insert_data = array();
				$insert_data['cost_store_id'] = $order_info['store_id'];
				$insert_data['cost_state'] =0;
				$insert_data['cost_time']  = time();
				$insert_data['cost_seller_id'] = $storeinfo['member_id'];
				$insert_data['cost_price'] = $all_invite_money;
				$insert_data['cost_remark'] = '订单：'.$order_info['order_sn'].'分销提成费用';
			    $rt=Model('store_cost')->addStoreCost($insert_data);
				if($rt){
					//变更会员预存款
				   $model_pd = Model('predeposit');
				   if($invite_one_id!=0){
				       $data = array();
					   $data['invite_member_id'] = $order_info['buyer_id'];
				       $data['member_id'] = $invite_one_id;
				       $data['member_name'] = $invite_one_name;
				       $data['amount'] = $invite_money;
				       $data['order_sn'] = $order_info['order_sn'];
				       $model_pd->changePd('order_invite',$data);
				   }
				   
				   if($invite_two_id!=0){
				       $data_pd = array();
					   $data_pd['invite_member_id'] = $order_info['buyer_id'];
				       $data_pd['member_id'] = $invite_two_id;
				       $data_pd['member_name'] = $invite_two_name;
				       $data_pd['amount'] = $invite_money2;
				       $data_pd['order_sn'] = $order_info['order_sn'];
				       $model_pd->changePd('order_invite',$data_pd);
				   }
				   
				   if($invite_three_id!=0){
				       $datas = array();
					   $datas['invite_member_id'] = $order_info['buyer_id'];
				       $datas['member_id'] = $invite_three_id;
				       $datas['member_name'] = $invite_three_name;
				       $datas['amount'] = $invite_money3;
				       $datas['order_sn'] = $order_info['order_sn'];
				       $model_pd->changePd('order_invite',$datas);
				   }
				}	 
			 } */
	}
	
}