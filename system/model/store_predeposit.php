<?php
/**
 * 预存款
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class store_predepositModel extends Model
{
    /**
     * 生成充值编号
     * @return string
     */
    public function makeSn()
    {
        return mt_rand(10, 99)
            . sprintf('%010d', time() - 946656000)
            . sprintf('%03d', (float)microtime() * 1000)
            . sprintf('%03d', (int)$_SESSION['member_id'] % 1000);
    }

    /**
     * 取提现单信息总数
     * @param unknown $condition
     */
    public function getPdCashCount($condition = array())
    {
        return $this->table('store_pd_cash')->where($condition)->count();
    }

    /**
     * 取日志总数
     * @param unknown $condition
     */
    public function getPdLogCount($condition = array())
    {
        return $this->table('store_pd_log')->where($condition)->count();
    }

    /**
     * 取得预存款变更日志列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $fields
     * @param string $order
     */
    public function getPdLogList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '')
    {
        return $this->table('store_pd_log')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }


    /**
     * 变更预存款
     * @param unknown $change_type
     * @param unknown $data
     * @throws Exception
     * @return unknown
     */
    public function changePd($change_type, $data = array())
    {
        $data_log = array();
        $data_pd = array();

        $data_log['lg_store_id'] = $data['store_id'];
        $data_log['lg_store_name'] = $data['store_name'];
        $data_log['lg_add_time'] = TIMESTAMP;
        $data_log['lg_type'] = $change_type;

        switch ($change_type) {
            case 'cash_apply':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '申请提现，冻结预存款，提现单号: ' . $data['order_sn'];
                $data_pd['available_predeposit'] = array('exp', 'available_predeposit-' . $data['amount']);
                $data_pd['freeze_predeposit'] = array('exp', 'freeze_predeposit+' . $data['amount']);

                break;
            case 'cash_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '提现成功，提现单号: ' . $data['order_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
                $data_pd['freeze_predeposit'] = array('exp', 'freeze_predeposit-' . $data['amount']);

                break;
            case 'seller_money':
                $msg = $data['lg_desc'];
                $data_log['lg_av_amount'] = +$data['amount'];
                $data_log['lg_desc'] = $msg . '，结算单号: ' . $data['pdr_sn'];
                $data_log['lg_pdr_sn'] = $data['pdr_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
                $data_pd['available_predeposit'] = array('exp', 'available_predeposit+' . $data['amount']);
                $data_pd['sell_amount'] = array('exp', 'sell_amount+' . $data['amount']);
                $data_pd['consume_num'] = array('exp', 'consume_num+1');

                break;
            default:
                throw new Exception('参数错误');
                break;
        }

        $update = Model('store')->editStore($data_pd, array('store_id' => $data['store_id']));

        if (!$update) {
            throw new Exception('操作失败');
        }
        $insert = $this->table('store_pd_log')->insert($data_log);
        if ($insert) {
            if ($change_type == 'seller_money') {
                $data_api = array();
                $data_api['lg_id'] = $insert;
                $data_api['store_id'] = $data['store_id'];
                $data_api['order_sn'] = $data['pdr_sn'];
                $data_api['amount'] = $data['amount'];

                $handle_trans = Handle('trans_api');
                Log::record('---data失败--- ' . json_encode($data));
                Log::record('---写入api发送临时表--- ' . json_encode($data_api));
                if ($data['paytype'] == 'wxminipay' || $data['paytype'] == 'wx_jsapi') {

                    $txinfo = Model('txway')->getInfo(array('store_id' => $data['store_id'], 'bill_type_code' => 'wxpay', 'type' => 2));
                    if (empty($txinfo) || !is_array($txinfo)) {
                        $insert2 = $this->table('store_pd_api')->insert($data_api);
                        if (!$insert2) {
                            Log::record('---写入api发送临时表失败--- ' . json_encode($data_api));
                        }

                    } else {

                        $t_data = array();
                        $t_data['order_sn'] = $data_api['order_sn'];
                        $t_data['pay_account'] = $txinfo['bill_openid'];
                        $t_data['amount'] = $data_api['amount'];
                        $t_data['real_name'] = $txinfo['bill_user_name'];
                        $t_data['remark'] = '商家交易单号:' . $data_api['order_sn'];

                        $trans_data = $handle_trans->wxpayTransfer($t_data);

                        Log::record('minutes--trans_api==:' . json_encode($trans_data));
                        if (!empty($trans_data) && $trans_data['code'] == 10000) {
                            $updata = Model('store_predeposit')->editPdLog(array('api_state' => 1, 'trade_no' => $trans_data['pay_order_id'], 'trade_time' => $trans_data['pay_date']), array('lg_id' => $insert));
                            $updata2 = Model('store_order')->editOrder(array('tx_api_state' => 1, 'tx_trade_no' => $trans_data['pay_order_id'], 'tx_trade_time' => $trans_data['pay_date']), array('order_sn' => $data_api['order_sn']));

                        } else {
                            $insert2 = $this->table('store_pd_api')->insert($data_api);
                            Model('store_order')->editOrder(array('tx_error' => $trans_data['sub_msg']), array('order_sn' => $data_api['order_sn']));

                            if (!$insert2) {
                                Log::record('---写入api发送临时表失败--- ' . json_encode($data_api));
                            }

                        }
                    }
                } else {

                    $txinfo = Model('txway')->getInfo(array('store_id' => $data['store_id'], 'bill_type_code' => 'alipay', 'type' => 2));
                    if (empty($txinfo) || !is_array($txinfo)) {
                        $insert2 = $this->table('store_pd_api')->insert($data_api);
                        if (!$insert2) {
                            Log::record('---写入api发送临时表失败--- ' . json_encode($data_api));
                        }
                    } else {

                        $t_data = array();
                        $t_data['order_sn'] = $data_api['order_sn'];
                        $t_data['pay_account'] = $txinfo['bill_type_number'];
                        $t_data['amount'] = $data_api['amount'];
                        $t_data['real_name'] = $txinfo['bill_user_name'];
                        $t_data['remark'] = '商家交易单号:' . $data_api['order_sn'];

                        $trans_data = $handle_trans->alipayTransfer($t_data);
                        Log::record('minutes--trans_api==:' . json_encode($trans_data));
                        if (!empty($trans_data) && $trans_data['code'] == 10000) {
                            $updata = Model('store_predeposit')->editPdLog(array('api_state' => 1, 'trade_no' => $trans_data['pay_order_id'], 'trade_time' => $trans_data['pay_date']), array('lg_id' => $insert));
                            $updata2 = Model('store_order')->editOrder(array('tx_api_state' => 1, 'tx_trade_no' => $trans_data['pay_order_id'], 'tx_trade_time' => $trans_data['pay_date']), array('order_sn' => $data_api['order_sn']));
                        } else {
                            $insert2 = $this->table('store_pd_api')->insert($data_api);
                            //写入失败原因
                            Model('store_order')->editOrder(array('tx_error' => $trans_data['sub_msg']), array('order_sn' => $data_api['order_sn']));
                            if (!$insert2) {
                                Log::record('---写入api发送临时表失败--- ' . json_encode($data_api));
                            }

                        }
                    }
                }

            }
        } else {
            throw new Exception('操作失败');
        }

        return $insert;
    }

    /**
     * 更改信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editPdLog($data, $condition)
    {
        $update = $this->table('store_pd_log')->where($condition)->update($data);
        return $update;
    }

    /**
     * 取得提现列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $fields
     * @param string $order
     */
    public function getPdCashList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '')
    {
        return $this->table('store_pd_cash')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /**
     * 添加提现记录
     * @param array $data
     */
    public function addPdCash($data)
    {
        return $this->table('store_pd_cash')->insert($data);
    }

    /**
     * 编辑提现记录
     * @param unknown $data
     * @param unknown $condition
     */
    public function editPdCash($data, $condition = array())
    {
        return $this->table('store_pd_cash')->where($condition)->update($data);
    }

    /**
     * 取得单条提现信息
     * @param unknown $condition
     * @param string $fields
     */
    public function getPdCashInfo($condition = array(), $fields = '*')
    {
        return $this->table('store_pd_cash')->where($condition)->field($fields)->find();
    }

    /**
     * 删除提现记录
     * @param unknown $condition
     */
    public function delPdCash($condition)
    {
        return $this->table('store_pd_cash')->where($condition)->delete();
    }
}
