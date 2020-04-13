<?php
/**
 * 会员评价
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class update_evaluateControl extends mobileHomeControl {

    public function __construct() {
        parent::__construct();
    }

    public function indexWt() {
        $this->updateWt();
    }


    //自动评价48小时内未评价的订单
    public function updateWt() {
        //两天前的时间戳
        $time = time() - 86400 * 2;
        $model_order = Model('store_order');
        //评价操作数据类
        $model_evaluate_store = Model('evaluate_store');

        //取得已支付状态下，48小时未评论的订单
        $order_condition = array();
        $order_condition['order_state'] = '20';
        $order_condition['evaluation_state'] = '0';
        $order_condition['payment_time'] = array('lt', $time);

        //获取到符合条件的数组
        $to_update_list = $model_order->getOrderList($order_condition);


        //开始评价
        foreach ($to_update_list as $value) {
            $evaluate_store_info = array();
            $evaluate_store_info['seval_orderid'] = $value['order_id'];
            $evaluate_store_info['seval_orderno'] = $value['order_sn'];
            $evaluate_store_info['seval_addtime'] = time();
            $evaluate_store_info['seval_storeid'] = $value['store_id'];
            $evaluate_store_info['seval_storename'] = $value['store_name'];
            $evaluate_store_info['seval_memberid'] = $value['buyer_id'];
            $evaluate_store_info['seval_membername'] = $value['buyer_name'];
            $evaluate_store_info['seval_desccredit'] = 5; //$store_desccredit;
            $evaluate_store_info['seval_servicecredit'] = 5;// $store_servicecredit;
            $evaluate_store_info['seval_deliverycredit'] = 5;//$store_deliverycredit;

            $evaluate_store_info['seval_scores'] = 5;
            $evaluate_store_info['seval_content'] = '买家48小时内未操作，系统自动好评';
            $evaluate_store_info['seval_isanonymous'] = 1;

//            $evaluate_store_info['seval_image'] = $geval_image;
            $evaluate_store_info['seval_content_again'] = '';
            $evaluate_store_info['seval_image_again'] = '';
            $evaluate_store_info['seval_explain_again'] = '';

            //print_r($evaluate_store_info);

            $model_evaluate_store->addEvaluateStore($evaluate_store_info);

            //更新订单信息并记录订单日志
            $state = $model_order->editOrder(array('evaluation_state' => 1), array('order_id' => $value['order_id']));
            if ($state) {
                //$model_order->editOrderCommon(array('evaluation_time'=>TIMESTAMP), array('order_id' => $order_info['order_id']));
                $data = array();
                $data['order_id'] = $value['order_id'];
                $data['log_role'] = 'buyer';
                $data['log_msg'] = L('order_log_eval');
                $model_order->addOrderLog($data);
            }
        }

    }
}
