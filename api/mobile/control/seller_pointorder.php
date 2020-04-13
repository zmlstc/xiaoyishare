<?php
/**
 * 积分兑换信
 *
 *
 * @好商城 (c) 2015-2018 33HAO Inc. (http://www.33hao.com)
 * @license    http://www.33 hao.c om
 * @link       交流群号：138182377
 * @since      好商城提供技术支持 授权请购买正版
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class seller_pointorderControl extends mobileSellerControl {
    public function __construct() {
        parent::__construct();

        //判断系统是否开启积分和积分兑换功能
        /* 		if (C('points_isuse') != 1 || C('pointprod_isuse') != 1){
                    output_error('未开启积分兑换功能!');die();
                } */

    }

    public function indexWt() {
        $this->orderlistWt();
    }

    /**
     * 兑换信息列表
     */
    public function orderlistWt() {
        //兑换信息列表
        $where = array();
        $where['store_id'] = $this->store_info['store_id'];;
        $where['point_orderstate'] = 50;

        if (!empty($_POST['keyword']) && trim($_POST['keyword']) != '') {
            $keyword = trim($_POST['keyword']);
            $where['point_buyername'] = array('like', '%' . $keyword . '%');
        }
        if (!empty($_POST['date']) && trim($_POST['date']) != '' && $_POST['stype'] == 1) {
            $date = trim($_POST['date']);
            $today_start = strtotime(trim($date) . ' 00:00:00');
            $today_end = strtotime(trim($date) . ' 23:59:59');
            $where['point_addtime'] = array('between', array($today_start, $today_end));
        }
        if (!empty($_POST['sdate']) && trim($_POST['sdate']) != '' && $_POST['stype'] == 2 && !empty($_POST['edate']) && trim($_POST['edate']) != '') {
            $sdate = trim($_POST['sdate']);
            $edate = trim($_POST['edate']);
            $today_start = strtotime(trim($sdate) . ' 00:00:00');
            $today_end = strtotime(trim($edate) . ' 23:59:59');
            $where['point_addtime'] = array('between', array($today_start, $today_end));
        }


        $model_pointorder = Model('pointorder');
        $order_list = $model_pointorder->getPointOrderList($where, '*', 12, 0, 'point_orderid desc');
        $page_count = $model_pointorder->gettotalpage();
        $order_idarr = array();
        $order_listnew = array();
        if (is_array($order_list) && count($order_list) > 0) {
            foreach ($order_list as $k => $v) {
                $data = array();
                $data['point_orderid'] = $v['point_orderid'];
                $data['point_ordersn'] = $v['point_ordersn'];
                $data['point_addtime'] = date('H:i:s', $v['point_addtime']);
                $data['point_adddate'] = date('m月d日', $v['point_addtime']);
                $data['point_finnshedtime'] = date('H:i:s', $v['point_finnshedtime']);
                $data['point_finnsheddate'] = date('m月d日', $v['point_finnshedtime']);
                $data['point_allpoint'] = $v['point_allpoint'];
                $data['point_orderstate'] = $v['point_orderstate'];
                $data['store_id'] = $v['store_id'];
                $memberInfo = Model('member')->getMemberInfoByID($v['point_buyerid']);
                $data['point_buyername'] = $memberInfo['nickname'];
                $order_listnew[$v['point_orderid']] = $data;

                $order_idarr[] = $v['point_orderid'];
            }
        }
        $order_listnew1 = array();
        //查询兑换商品
        if (is_array($order_idarr) && count($order_idarr) > 0) {
            $prod_list = $model_pointorder->getPointOrderGoodsList(array('point_orderid' => array('in', $order_idarr)));
            if (is_array($prod_list) && count($prod_list) > 0) {
                foreach ($prod_list as $v) {
                    if (isset($order_listnew[$v['point_orderid']])) {
                        $v['endtime'] = date('Y年m月d日', $v['endtime']);
                        // $order_listnew[$v['point_orderid']]['prodlist'][] = $v;

                        $order_listnew[$v['point_orderid']]['point_recid'] = $v['point_recid'];
                        $order_listnew[$v['point_orderid']]['point_goodsid'] = $v['point_goodsid'];
                        $order_listnew[$v['point_orderid']]['point_goodsname'] = $v['point_goodsname'];
                        $order_listnew[$v['point_orderid']]['point_goodspoints'] = $v['point_goodspoints'];
                        $order_listnew[$v['point_orderid']]['point_goodsnum'] = $v['point_goodsnum'];
                        //$order_listnew[$v['point_orderid']]['point_goodsimage'] = pointprodThumb($v['point_goodsimage'], 'mid');
                        $order_listnew[$v['point_orderid']]['endtime'] = $v['endtime'];
                        //$order_listnew[$v['point_orderid']]['checkcode'] = $v['checkcode'];

                        $order_listnew[$v['point_orderid']]['point_goodsimage'] = $v['point_goodsimage'];


                    }
                }

                foreach ($order_listnew as $k => $v) {
                    $order_listnew1[] = $v;
                }
            }
        }
        output_data(array('list' => $order_listnew1), mobile_page($page_count));

    }


    /**
     * 兑换信息详细
     */
    public function order_infoWt() {
        $str = trim($_POST['str']);
        $pm = explode("-", $str);
        if (count($pm) != 2) {
            output_error('参数错误!');
        }
        $point_ordersn = $pm[0];
        $member_id = $pm[1];

        $store_id = $this->store_info['store_id'];

        $model_pointorder = Model('pointorder');
        //查询兑换订单信息
        $where = array();
        $where['point_ordersn'] = $point_ordersn;
        $where['point_buyerid'] = $member_id;
        $where['store_id'] = $store_id;
        $where['point_orderstate'] = 20;//

        $order_info = $model_pointorder->getPointOrderInfo($where);
        if (!$order_info) {
            output_error('订单不存在!');
            die();
        }
        $pinfo = array();
        if ($order_info['point_addtime']) {
            $pinfo['point_addtime'] = date('Y-m-d H:i:s', $order_info['point_addtime']);
        } else {
            $pinfo['point_addtime'] = '';
        }
        $memberInfo = Model('member')->getMemberInfoByID($order_info['point_buyerid']);
        $storeInfo = Model('store')->getStoreInfoByID($order_info['store_id']);
        //兑换商品信息
        $p_list = $model_pointorder->getPointOrderGoodsInfo(array('point_orderid' => $order_info['point_orderid']));
        //echo json_encode($order_info);
        $pinfo['point_goodspoints'] = $p_list['point_goodspoints'];
        $pinfo['point_goodsname'] = $p_list['point_goodsname'];
        $pinfo['nickname'] = $memberInfo['nickname'];
        $pinfo['store_name'] = $storeInfo['store_name'];
        $pinfo['endtime'] = date('Y/m/d', $p_list['endtime']);

        output_data(array('prod_info' => $pinfo));

    }


    /***
     *扫码兑换
     *
     ***/
    public function point_scanWt() {
        $str = trim($_POST['str']);
        $pm = explode("-", $str);
        if (count($pm) != 2) {
            output_error('参数错误!');
        }
        $point_ordersn = $pm[0];
        $member_id = $pm[1];
        $p_model = Model('pointorder');
        $store_id = $this->store_info['store_id'];
        $ret = $p_model->dhPointOrder($point_ordersn, $member_id, $store_id);
        if ($ret['state']) {
            //发送短信通知商家到店兑换成功
            $param = array();
            $param['code'] = 'gift_exchange';
            $param['store_id'] = $store_id;
            $pointprod_info = $p_model->getPointOrderGoodsInfo(array('point_ordersn' => $point_ordersn));
            $member_info = Model('member')->getMemberInfoByID($member_id);
            $param['param'] = array('giftname' => $pointprod_info['pgoods_name'], 'nickname' => $member_info['nickname']);
            QueueClient::push('sendStoreMsg', $param);

            output_data('1');
        } else {
            output_error($ret['msg']);
        }
    }


    /**
     * 确认收货
     */
    public function point_checkWt() {
        $checkcode = trim($_POST['checkcode']);
        if (empty($checkcode) || $checkcode == '') {
            output_error('请输入积分兑换码!');
        }
        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['checkcode'] = checkcode;
        $field = 'endtime,point_orderid,point_orderstate,store_id';
        $orderinfo = Model('pointorder')->getPointOrderAndGoodsInfo($where, $field);
        if (empty($orderinfo) || !is_array($orderinfo)) {
            output_error('兑换码不存在!');
            die();

        }
        if ($orderinfo['point_orderstate'] == '40') {
            output_error('该兑换码已兑换!');
            die();

        }
        if ($orderinfo['point_orderstate'] != '30') {
            output_error('该兑换码无效!');
            die();

        }

        if ($orderinfo['endtime'] < time()) {
            output_error('该兑换码已过期!');
            die();

        }

        $where = array();
        $where['point_orderid'] = $orderinfo['point_orderid'];
        $where['store_id'] = $this->store_info['store_id'];
        $where['point_orderstate'] = 30;
        //更新
        $result = Model('pointorder')->editPointOrder($where, array('point_orderstate' => 40, 'point_finnshedtime' => time()));
        if ($result) {
            output_data('1');
        } else {
            output_error('兑换失败!');
            die();
        }

        //$where['point_orderstate'] = 40;
        //$data = Model('pointorder')->receivingPointOrder($_POST['order_id']);

    }

}
