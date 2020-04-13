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

class member_evaluateControl extends mobileMemberControl
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexWt()
    {
        $this->listWt();
    }


    /**
     * 评价列表
     */
    public function listWt()
    {
        $model_evaluate = Model('evaluate_store');

        $condition = array();
        $condition['member_del'] = 0;
        $condition['seval_memberid'] = $this->member_info['member_id'];
        $goodsevallist = $model_evaluate->getEvaluateStoreList($condition, 10);

        $page_count = $model_evaluate->gettotalpage();
        $storeids = array();
        if (!empty($goodsevallist) && is_array($goodsevallist)) {
            foreach ($goodsevallist as $key => $val) {
                $storeids[] = $val['seval_storeid'];

            }

        }
        if (!empty($storeids) && is_array($storeids)) {
            $storelist = Model('store')->getStoreMemberIDList($storeids, '*');
        }
        $eval_list = array();
        if (!empty($goodsevallist) && is_array($goodsevallist)) {
            foreach ($goodsevallist as $key => $val) {
                $data = array();
                $data['seval_id'] = $val['seval_id'];
                $data['store_name'] = $storelist[$val['seval_storeid']]['store_name'];
                $data['store_id'] = $val['store_id'];
                $data['seval_scores'] = $val['seval_scores'];
                $data['seval_content'] = $val['seval_content'];
                $data['seval_explain'] = $val['seval_explain'] === null ? '' : $val['seval_explain'];
                // 店铺头像
                $data['store_avatar'] = $storelist[$val['seval_storeid']]['store_avatar']
                    ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $storelist[$val['seval_storeid']]['store_avatar'] : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');
                $data['date_txt'] = date('m月d日', $val['seval_addtime']);
                $data['time_txt'] = date('H:i', $val['seval_addtime']);

                // 评价晒图
                $geval_image_240 = array();
                $geval_image_1024 = array();
                if (!empty($val['seval_image'])) {
                    $image_array = explode(',', $val['seval_image']);
                    foreach ($image_array as $value) {
                        $geval_image_240[] = snsThumb($value, 240);
                        $geval_image_1024[] = snsThumb($value, 1024);
                    }
                }
                $data['geval_image_240'] = $geval_image_240;
                $data['geval_image_1024'] = $geval_image_1024;

                $eval_list[] = $data;
            }

        }


        output_data(array('eval_list' => $eval_list), mobile_page($page_count));
    }


    /**
     * 评价总数
     */
    public function listnumWt()
    {
        $model_evaluate = Model('evaluate_store');

        $condition = array();
        $condition['member_del'] = 0;
        $condition['seval_memberid'] = $this->member_info['member_id'];
        $goodsevallist = $model_evaluate->getEvaluateStoreList($condition, 10);

        $pinglunnum = sizeof($model_evaluate->getEvaluateStoreList($condition));

        $model_order = Model('store_order');

        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['evaluation_state'] = 0;
        //$condition['order_type'] = array('in',array(1,3,4));

        $condition['order_state'] = ORDER_STATE_PAY;

        if (preg_match('/^\d{10,20}$/', $_POST['order_key'])) {
            $condition['order_sn'] = $_POST['order_key'];
        }

        $order_list_num = sizeof($model_order->getNormalOrderList($condition, '', '*', 'order_id desc', '', array('store')));

        $allnum = $order_list_num;


        output_data(array('allnum' => $allnum), mobile_page($page_count));
    }


    /**
     * 订单列表
     */
    public function orderlistWt()
    {

        $model_order = Model('store_order');

        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['evaluation_state'] = 0;
        //$condition['order_type'] = array('in',array(1,3,4));

        $condition['order_state'] = ORDER_STATE_PAY;

        if (preg_match('/^\d{10,20}$/', $_POST['order_key'])) {
            $condition['order_sn'] = $_POST['order_key'];
        }

        $order_list_array = $model_order->getNormalOrderList($condition, 10, '*', 'order_id desc', '', array('store'));

        $order_list = array();
        foreach ($order_list_array as $value) {
            $data = array();
            $data['store_name'] = $value['store_name'];
            $data['order_amount'] = $value['order_amount'];
            $data['order_id'] = $value['order_id'];
            $data['payment_name'] = $value['payment_name'];
            $data['order_sn'] = $value['order_sn'];
            $scores = Model('evaluate_store')->getEvaluateStoreInfoByStoreID($value['store_id']);
            $data['seval_scores'] = $scores['seval_scores'];
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

        output_data(array('order_list' => $order_list), mobile_page($page_count));
    }

    /**
     * 评论
     */
    public function pinlunWt()
    {
        $order_id = intval($_POST['order_id']);
        $return = Handle('evaluate')->validation($order_id, $this->member_info['member_id']);
        if (!$return['state']) {
            output_error($return['msg']);
        }
        extract($return['data']);
        $store = array();
        $store['store_id'] = $store_info['store_id'];
        $store['store_name'] = $store_info['store_name'];

        output_data(array('store_info' => $store));
    }

    /**
     * 评论保存
     */
    public function saveWt()
    {
        $order_id = intval($_POST['order_id']);
        $return = Handle('evaluate')->validation($order_id, $this->member_info['member_id']);
        if (!$return['state']) {
            output_error($return['msg']);
        }
        extract($return['data']);
        $return = Handle('evaluate')->save($_POST, $order_info, $store_info, $this->member_info['member_id'], $this->member_info['member_name']);

        if (!$return['state']) {
            output_data($return['msg']);
        } else {
            output_data('1');
        }
    }

    /**
     *  删除评论
     */
    public function eval_deleteWt()
    {
        $eval_id = intval($_POST['eval_id']);
        if ($eval_id <= 0) {
            output_error('参数错误！');
        }

        $condition = array();
        $condition['seval_id'] = $eval_id;
        $condition['seval_memberid'] = $this->member_info['member_id'];

        //更新订单删除状态
        $update = Model()->table('evaluate_store')->where($condition)->update(array('member_del' => 1));

        if (!$update) {
            output_error('操作失败');
        } else {
            output_data('1');
        }
    }

    /**
     * 追评
     */
    public function againWt()
    {
        $order_id = intval($_GET['order_id']);
        $return = Handle('evaluate')->validationAgain($order_id, $this->member_info['member_id']);
        if (!$return['state']) {
            output_error($return['msg']);
        }
        extract($return['data']);
        $store = array();
        $store['store_id'] = $store_info['store_id'];
        $store['store_name'] = $store_info['store_name'];
        $store['is_own_shop'] = $store_info['is_own_shop'];

        output_data(array('store_info' => $store, 'evaluate_goods' => $evaluate_goods));
    }

    /**
     * 追加评价保存
     */
    public function save_againWt()
    {
        $order_id = intval($_POST['order_id']);
        $return = Handle('evaluate')->validationAgain($order_id, $this->member_info['member_id']);
        if (!$return['state']) {
            output_error($return['msg']);
        }
        extract($return['data']);

        $return = Handle('evaluate')->saveAgain($_POST, $order_info, $evaluate_goods);
        if (!$return['state']) {
            output_data($return['msg']);
        } else {
            output_data('1');
        }
    }


}
