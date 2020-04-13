<?php
/**
 * 我的订单
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class member_orderControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 订单列表
     */
    public function orderlistWt() {

        $model_order = Model('store_order');

        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        //$condition['order_type'] = array('in',array(1,3,4));
        if ($_POST['daterange']) {
            $daterange = explode(',', $_POST['daterange']);
            $daterange[0] = $daterange[0] . ' 00:00:00';
            $daterange[1] = $daterange[1] . ' 23:59:59';
            foreach ($daterange as $key => $value) {
                $daterange[$key] = strtotime($value);
            }
            $condition['payment_time'] = array('between time', [$daterange[0], $daterange[1]]);
            //output_data(array('order_list' => $daterange));
        }
        $condition['order_state'] = ORDER_STATE_PAY;

        if (preg_match('/^\d{10,20}$/', $_POST['order_key'])) {
            $condition['order_sn'] = $_POST['order_key'];
        }

        $order_list_array = $model_order->getNormalOrderList($condition, 10, '*', 'order_id desc', '', array('store'));


        $order_list = array();
        foreach ($order_list_array as $value) {
            $data = array();
            $data['store_name'] = $value['store_name'];
            $data['store_id'] = $value['store_id'];
            $data['order_amount'] = $value['order_amount'];
            $data['order_id'] = $value['order_id'];
            $data['payment_name'] = $value['payment_name'];
            $data['order_sn'] = $value['order_sn'];
            $data['pay_comment'] = $value['pay_comment'];
            // 店铺头像
            $data['store_avatar'] = $value['extend_store']['store_avatar']
                ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $value['extend_store']['store_avatar']
                : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');
            //$data['avatar'] = $value['extend_store']['store_avatar'];
            $data['map_address'] = $value['extend_store']['map_address'];;
            $data['date'] = date('m-d', $value['payment_time']);
            $data['time'] = date('H:i:s', $value['payment_time']);
            $order_list[] = $data;
        }

        $page_count = $model_order->gettotalpage();

        output_data(array('order_list' => $order_list, 'id' => $condition['buyer_id']), mobile_page($page_count));
    }

    /**
     * 取消订单
     */
    public function orderdeleteWt() {
        $model_order = Model('store_order');
        $handle_order = Handle('store_order');
        $order_id = intval($_POST['orderid']);

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        //$condition['order_type'] = array('in',array(1,3,4));//拼团
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('delete', $order_info);
        if (!$if_allow) {
            output_error('操作失败');
        }

        $result = $handle_order->changeOrderStateRecycle($order_info, 'buyer', 'delete');
        if (!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }


    private function _getOrderIdByKeyword($keyword) {
        $goods_list = Model('order')->getOrderGoodsList(array('goods_name' => array('like', '%' . $keyword . '%')), 'order_id', 100, null, '', null, 'order_id');
        return array_keys($goods_list);
    }

    /**
     * 取消订单
     */
    public function order_cancelWt() {
        $model_order = Model('order');
        $handle_order = Handle('order');
        $order_id = intval($_POST['order_id']);

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = array('in', array(1, 3, 4));//拼团
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }
        if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
            $_hour = ceil(($order_info['api_pay_time'] + 86400 - TIMESTAMP) / 3600);
            output_error('该订单曾尝试使用第三方支付平台支付，须在' . $_hour . '小时以后才可取消');
        }
        $result = $handle_order->changeOrderStateCancel($order_info, 'buyer', $this->member_info['member_name'], '其它原因');
        if (!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }


    /**
     * 订单确认收货
     */
    public function order_receiveWt() {
        $model_order = Model('order');
        $handle_order = Handle('order');
        $order_id = intval($_POST['order_id']);

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = array('in', array(1, 3, 4));//拼团
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('receive', $order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }

        $result = $handle_order->changeOrderStateReceive($order_info, 'buyer', $this->member_info['member_name'], '签收了货物');
        if (!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }

    /**
     * 物流跟踪
     */
    public function search_deliverWt() {
        $order_id = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }

        $model_order = Model('order');
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $model_order->getOrderInfo($condition, array('order_common', 'order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'], array(ORDER_STATE_SEND, ORDER_STATE_SUCCESS))) {
            output_error('订单不存在');
        }

        $express = rkcache('express', true);
        $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
        $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
        $deliver_info = $this->_get_express($e_code, $order_info['shipping_code']);
        output_data(array('express_name' => $e_name, 'shipping_code' => $order_info['shipping_code'], 'deliver_info' => $deliver_info));
    }

    /**
     * 取得当前的物流最新信息
     */
    public function get_current_deliverWt() {
        $order_id = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }

        $model_order = Model('order');
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $model_order->getOrderInfo($condition, array('order_common', 'order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'], array(ORDER_STATE_SEND, ORDER_STATE_SUCCESS))) {
            output_error('订单不存在');
        }

        $express = rkcache('express', true);
        $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
        $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
        $content = Model('express')->get_express($e_code, $order_info['shipping_code']);
        if (empty($content)) {
            output_error('物流信息查询失败');
        } else {
            foreach ($content as $k => $v) {
                if ($v['time'] == '') continue;
                output_data(array('deliver_info' => $content[0]));
            }
            output_error('物流信息查询失败');
        }
    }

    /**
     * 从第三方取快递信息
     *
     */
    public function _get_express($e_code, $shipping_code) {

        $content = Model('express')->get_express($e_code, $shipping_code);
        if (empty($content)) {
            output_error('物流信息查询失败');
        }
        $output = array();
        foreach ($content as $k => $v) {
            if ($v['time'] == '') continue;
            $output[] = $v['time'] . '&nbsp;&nbsp;' . $v['context'];
        }

        return $output;
    }

    public function order_infoWt() {
        $handle_order = handle('order');
        $result = $handle_order->getMemberOrderInfo($_GET['order_id'], $this->member_info['member_id']);
        if (!$result['state']) {
            output_error($result['msg']);
        }
        $data = array();
        $data['order_id'] = $result['data']['order_info']['order_id'];
        $data['order_sn'] = $result['data']['order_info']['order_sn'];
        $data['store_id'] = $result['data']['order_info']['store_id'];
        $data['store_name'] = $result['data']['order_info']['store_name'];
        $data['add_time'] = date('Y-m-d H:i:s', $result['data']['order_info']['add_time']);
        $data['payment_time'] = $result['data']['order_info']['payment_time'] ? date('Y-m-d H:i:s', $result['data']['order_info']['payment_time']) : '';
        $data['shipping_time'] = $result['data']['order_info']['extend_order_common']['shipping_time'] ? date('Y-m-d H:i:s', $result['data']['order_info']['extend_order_common']['shipping_time']) : '';
        $data['finnshed_time'] = $result['data']['order_info']['finnshed_time'] ? date('Y-m-d H:i:s', $result['data']['order_info']['finnshed_time']) : '';
        $data['order_amount'] = wtPriceFormat($result['data']['order_info']['order_amount']);
        $data['shipping_fee'] = wtPriceFormat($result['data']['order_info']['shipping_fee']);
        $data['real_pay_amount'] = wtPriceFormat($result['data']['order_info']['order_amount']);
//         $data['evaluation_state'] = $result['data']['order_info']['evaluation_state'];
//         $data['evaluation_again_state'] = $result['data']['order_info']['evaluation_again_state'];
//         $data['refund_state'] = $result['data']['order_info']['refund_state'];
        $data['state_desc'] = $result['data']['order_info']['state_desc'];
        $data['payment_name'] = $result['data']['order_info']['payment_name'];
        $data['order_message'] = $result['data']['order_info']['extend_order_common']['order_message'];
        $data['reciver_phone'] = $result['data']['order_info']['buyer_phone'];
        $data['reciver_name'] = $result['data']['order_info']['extend_order_common']['reciver_name'];
        $data['reciver_addr'] = $result['data']['order_info']['extend_order_common']['reciver_info']['address'];
        $data['store_member_id'] = $result['data']['order_info']['extend_store']['member_id'];
        //shopwt 添加QQ IM
        $data['store_qq'] = $result['data']['order_info']['extend_store']['store_qq'];
        $data['node_chat'] = C('node_chat');
        $data['store_phone'] = $result['data']['order_info']['extend_store']['store_phone'];
        $data['order_tips'] = $result['data']['order_info']['order_state'] == ORDER_STATE_NEW ? '请于' . ORDER_AUTO_CANCEL_TIME . '小时内完成付款，逾期未付订单自动关闭' : '';
        $_tmp = $result['data']['order_info']['extend_order_common']['invoice_info'];
        $_invonce = '';
        if (is_array($_tmp) && count($_tmp) > 0) {
            foreach ($_tmp as $_k => $_v) {
                $_invonce .= $_k . '：' . $_v . ' ';
            }
        }
        $_tmp = $result['data']['order_info']['extend_order_common']['sale_info'];
        $data['sale'] = array();
        if (!empty($_tmp)) {
            $pinfo = unserialize($_tmp);
            if (is_array($pinfo) && $pinfo) {
                foreach ($pinfo as $pk => $pv) {
                    if (!is_array($pv) || !is_string($pv[1]) || is_array($pv[1])) {
                        $pinfo = array();
                        break;
                    }
                    $pinfo[$pk][1] = strip_tags($pv[1]);
                }
                $data['sale'] = $pinfo;
            }
        }

        $data['invoice'] = rtrim($_invonce);
        $data['if_deliver'] = $result['data']['order_info']['if_deliver'];
        $data['if_buyer_cancel'] = $result['data']['order_info']['if_buyer_cancel'];
        $data['if_refund_cancel'] = $result['data']['order_info']['if_refund_cancel'];
        $data['if_receive'] = $result['data']['order_info']['if_receive'];
        $data['if_evaluation'] = $result['data']['order_info']['if_evaluation'];
        $data['if_lock'] = $result['data']['order_info']['if_lock'];
        $order_type = $result['data']['order_info']['order_type'];
        $data['order_type'] = $order_type;
        if ($order_type == 4) {//拼团订单
            $model_pingou = Model('p_pingou');
            $_info = $model_pingou->getOrderInfo(array('order_id' => $data['order_id']));
            $data['pingou_info'] = $_info;
        }
        $data['goods_list'] = array();
        foreach ($result['data']['order_info']['goods_list'] as $_k => $_v) {
            $data['goods_list'][$_k]['rec_id'] = $_v['rec_id'];
            $data['goods_list'][$_k]['goods_id'] = $_v['goods_id'];
            $data['goods_list'][$_k]['goods_name'] = $_v['goods_name'];
            $data['goods_list'][$_k]['goods_price'] = wtPriceFormat($_v['goods_price']);
            $data['goods_list'][$_k]['goods_num'] = $_v['goods_num'];
            $data['goods_list'][$_k]['goods_spec'] = $_v['goods_spec'];
            $data['goods_list'][$_k]['image_url'] = $_v['image_240_url'];
            $data['goods_list'][$_k]['refund'] = $_v['refund'];
        }
        $data['zengpin_list'] = array();
        foreach ($result['data']['order_info']['zengpin_list'] as $_k => $_v) {
            $data['zengpin_list'][$_k]['goods_name'] = $_v['goods_name'];
            $data['zengpin_list'][$_k]['goods_num'] = $_v['goods_num'];
        }

        $ownShopIds = Model('store')->getOwnShopIds();
        $data['ownshop'] = in_array($data['store_id'], $ownShopIds);

        output_data(array('order_info' => $data));
    }

}
