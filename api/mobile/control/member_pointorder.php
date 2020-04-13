<?php
/**
 * 积分兑换信
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class member_pointorderControl extends mobileMemberControl {
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
        $order_state = intval($_POST['state']);
        //兑换信息列表
        $where = array();
        $where['point_buyerid'] = $this->member_info['member_id'];
        $where['point_orderstate'] = $order_state;
        $where['is_del'] = '0';
        $model_pointorder = Model('pointorder');
        $order_list = $model_pointorder->getPointOrderList($where, '*', 10, 0, 'point_orderid desc');
        $num = $model_pointorder->getPointOrderCount($where);
        $page_count = $model_pointorder->gettotalpage();
        $order_idarr = array();
        $order_listnew = array();
        if (is_array($order_list) && count($order_list) > 0) {
            foreach ($order_list as $k => $v) {
                $data = array();
                $data['point_orderid'] = $v['point_orderid'];
                $data['point_ordersn'] = $v['point_ordersn'];
                $data['point_addtime'] = date('m-d H:i:s', $v['point_addtime']);
                $data['point_finnshedtime'] = date('m-d H:i:s', $v['point_finnshedtime']);
                $data['point_allpoint'] = $v['point_allpoint'];
                $data['point_orderstate'] = $v['point_orderstate'];
                $data['store_id'] = $v['store_id'];
                $data['store_name'] = $v['store_name'];
                $data['p_info'] = $v['point_ordersn'] . '-' . $this->member_info['member_id'];
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
                        //$order_listnew[$v['point_orderid']]['prodlist'][] = $v;
                        $order_listnew[$v['point_orderid']]['point_recid'] = $v['point_recid'];
                        $order_listnew[$v['point_orderid']]['point_goodsid'] = $v['point_goodsid'];
                        $order_listnew[$v['point_orderid']]['point_goodsname'] = $v['point_goodsname'];
                        $order_listnew[$v['point_orderid']]['point_goodspoints'] = $v['point_goodspoints'];
                        $order_listnew[$v['point_orderid']]['point_goodsnum'] = $v['point_goodsnum'];
                        $order_listnew[$v['point_orderid']]['point_goodsimage'] = $v['point_goodsimage'];
                        $order_listnew[$v['point_orderid']]['endtime'] = $v['endtime'];
                        $order_listnew[$v['point_orderid']]['checkcode'] = $v['checkcode'];
                        $order_listnew[$v['point_orderid']]['price'] = floatval($v['price']);
                    }
                }

                foreach ($order_listnew as $k => $v) {
                    $order_listnew1[] = $v;
                }
            }
        }
        output_data(array('list' => $order_listnew1, 'num' => $num), mobile_page($page_count));

    }

    //未兑换商品总数
    public function ordernumWt() {
        $order_state = '20';
        $where = array();
        $where['point_buyerid'] = $this->member_info['member_id'];
        $where['point_orderstate'] = $order_state;
        $where['is_del'] = '0';
        $model_pointorder = Model('pointorder');
        $num = $model_pointorder->getPointOrderCount($where);
        output_data(array('num' => $num));
    }

    /**
     * 积分兑换订单
     */
    public function pointbuyWt() {
        $pg_id = intval($_POST['pgid']);
        $member_id = $this->member_info['member_id'];
        //查询积分礼品信息
        $model_pointprod = Model('pointprod');
        $pointprod_info = $model_pointprod->getOnlinePointProdInfo(array('pgoods_id' => $pg_id));
        if (!$pointprod_info) {
            output_error('参数错误');
        }

        //验证积分礼品兑换状态
        $ex_state = $model_pointprod->getPointProdExstate($pointprod_info);
        if ('going' !== $ex_state) {
            output_error('兑换信息错误');
        }
        //验证兑换数量是否合法
        $data = $this->getPointProdExnum($pointprod_info, 1, $member_id);
        if (!$data['state']) {
            output_error($data['msg']);
        }

        $points = intval($pointprod_info['pgoods_points']);
        if ($member_id <= 0) {
            output_error('会员信息错误');
        }

        $member_info = Model('member')->getMemberInfoByID($member_id);

        if (intval($member_info['member_points']) < $points) {
            output_error('积分不足，暂时不能兑换');
        }


        //创建兑换订单
        $data = Model('pointorder')->createOrder($_POST, $pointprod_info, $member_info);
        if (!$data['state']) {
            output_error($data['msg']);
            die();
        }
        $order_id = $data['data']['order_id'];

        //发送短信通知用户兑换成功

        $param = array();
        $param['code'] = 'gift_exchange';
        $param['member_id'] = $member_id;
        $date = date('Y-m-d', $pointprod_info['pgoods_endtime']);
        $shop = Model('store')->getStoreInfo($pointprod_info['store_id']);
        $shopphone = $shop['shop_phone'];
        //$time = date('H:i:s', $cash_info['pdc_add_time']);
        $param['param'] = array('gift_name' => $pointprod_info['pgoods_name'], 'date' => $date, 'store_name' => $pointprod_info['store_name'], 'shop_phone' => $shopphone);

        QueueClient::push('sendMemberMsg', $param);

        //发送短信通知商家兑换成功
        $param = array();
        $param['code'] = 'gift_exchange';
        $param['store_id'] = $pointprod_info['store_id'];
        $giftnum = $pointprod_info['pgoods_storage'] - 1;
        $param['param'] = array('giftname' => $pointprod_info['pgoods_name'], 'nickname' => $member_info['nickname'], 'giftnum' => $giftnum);

        QueueClient::push('sendStoreMsg', $param);
        output_data(array('pointprod_arr' => $order_id));
    }


    public function orderdelWt() {
        $model_order = Model('pointorder');
        $order_id = intval($_POST['orderid']);

        $condition = array();
        $condition['point_orderid'] = $order_id;
        $condition['point_buyerid'] = $this->member_info['member_id'];

        $result = $model_order->editPointOrder($condition, array('is_del' => 1));
        if (!$result) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }

    }

    /**
     * 获得礼品可兑换数量
     * @param array $prodinfo 礼品数组
     * @param array $quantity 兑换数量
     * @param array $member_id 会员编号
     * return array 兑换数量及其错误数组
     */
    public function getPointProdExnum($prodinfo, $quantity, $member_id) {

        if ($prodinfo['pgoods_storage'] <= 0) {
            return array('state' => false, 'msg' => '该礼品已兑换完');
        }
        //查询已兑换数量，并获得剩余可兑换数量
        if ($prodinfo['pgoods_islimit'] == 1) {
            $model_pointorder = Model('pointorder');
            //获取兑换订单状态
            $pointorderstate_arr = $model_pointorder->getPointOrderStateBySign();
            $where = array();
            $where['point_goodsid'] = $prodinfo['pgoods_id'];
            $where['point_buyerid'] = $member_id;
            $where['point_orderstate'] = array('neq', $pointorderstate_arr['canceled'][0]);//未取消
            $pordergoodsinfo = $model_pointorder->getPointOrderAndGoodsInfo($where, "SUM(point_goodsnum) as exnum", '', 'point_goodsid');
            if ($pordergoodsinfo) {
                $ablenum = $prodinfo['pgoods_limitnum'] - intval($pordergoodsinfo['exnum']);
                if ($ablenum <= 0) {
                    return array('state' => false, 'msg' => '您已达到礼品最大兑换数');
                }
                if ($ablenum < $quantity) {
                    $quantity = $ablenum;
                }
            }
        }
        return array('state' => true, 'data' => array('quantity' => $quantity));
    }


    /**
     *    取消兑换
     */
    public function cancel_orderWt() {
        $model_pointorder = Model('pointorder');
        //取消订单
        $data = $model_pointorder->cancelPointOrder($_POST['order_id'], $this->member_info['member_id']);
        if ($data['state']) {
            output_data('1');
        } else {
            output_error('取消失败!');
            die();
        }
    }

    /**
     * 确认收货
     */
    public function receiving_orderWt() {
        $data = Model('pointorder')->receivingPointOrder($_POST['order_id']);
        if ($data['state']) {
            output_data('1');
        } else {
            output_error('处理失败!');
            die();
        }
    }

    /**
     * 兑换信息详细
     */
    public function order_infoWt() {
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            output_error('订单不正确!');
            die();
        }
        $model_pointorder = Model('pointorder');
        //查询兑换订单信息
        $where = array();
        $where['point_orderid'] = $order_id;
        $where['point_buyerid'] = $this->member_info['member_id'];
        $order_info = $model_pointorder->getPointOrderInfo($where);
        if (!$order_info) {
            output_error('订单不存在!');
            die();
        }
        if ($order_info['point_addtime']) {
            $order_info['point_addtime'] = date('Y-m-d H:i:s', $order_info['point_addtime']);
        } else {
            $order_info['point_addtime'] = '';
        }
        if ($order_info['point_shippingtime']) {
            $order_info['point_shippingtime'] = date('Y-m-d H:i:s', $order_info['point_shippingtime']);
        } else {
            $order_info['point_shippingtime'] = '';
        }
        if ($order_info['point_finnshedtime']) {
            $order_info['point_finnshedtime'] = date('Y-m-d H:i:s', $order_info['point_finnshedtime']);
        } else {
            $order_info['point_finnshedtime'] = '';
        }
        //获取订单状态
        $pointorderstate_arr = $model_pointorder->getPointOrderStateBySign();

        //查询兑换订单收货人地址
        $orderaddress_info = $model_pointorder->getPointOrderAddressInfo(array('point_orderid' => $order_id));

        //兑换商品信息
        $prod_list = $model_pointorder->getPointOrderGoodsList(array('point_orderid' => $order_id));

        //物流公司信息
        if ($order_info['point_shipping_ecode'] != '') {
            $data = Model('express')->getExpressInfoByECode($order_info['point_shipping_ecode']);
            if ($data['state']) {
                $express_info = $data['data']['express_info'];
            }
        }
        output_data(array('order_info' => $order_info, 'express_info' => $express_info, 'prod_list' => $prod_list, 'orderaddress_info' => $orderaddress_info, 'pointorderstate_arr' => $pointorderstate_arr));

    }

}
