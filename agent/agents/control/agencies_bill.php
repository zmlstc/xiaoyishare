<?php
/**
 * 结算管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class agencies_billControl extends SystemControl{
    /**
     * 每次导出订单数量
     * @var int
     */
    const EXPORT_SIZE = 1000;

    private $links = array(
        array('url'=>'w=bill&t=index','lang'=>'wt_manage'),
    );

    public function __construct(){
        parent::__construct();
    }

    /**
     * 结算单列表
     *
     */
    public function indexWt(){
						
		Tpl::setDirquna('agents');
        Tpl::showpage('agencies_bill.index');
    }

    /**
     * 某店铺某月订单列表
     *
     */
    public function show_billWt(){
        $ob_id = intval($_GET['ob_id']);
        if ($ob_id <= 0) {
            showMessage('参数错误','','html','error');
        }
        $model_bill = Model('agencies_bill');
		$admininfo = $this->getAdminInfo();
        $bill_info = $model_bill->getAgentBillInfo(array('ob_id'=>$ob_id,'ob_agent_id'=>$admininfo['id']));
        if (!$bill_info){
            showMessage('参数错误','','html','error');
        }

		//订单列表
		$sub_tpl_name = 'agencies_order_bill.show.order_list';
        

        Tpl::output('tpl_name',$sub_tpl_name);
        Tpl::output('bill_info',$bill_info);
						
		Tpl::setDirquna('agents');
        Tpl::showpage('agencies_order_bill.show');
    }

    public function get_bill_info_xmlWt(){
        $ob_id = intval($_GET['ob_id']);
        if ($ob_id <= 0 ) {
            exit();
        }
        $model_bill = Model('agencies_bill');
		$admininfo = $this->getAdminInfo();
        $bill_info = $model_bill->getAgentBillInfo(array('ob_id'=>$ob_id,'ob_agent_id'=>$admininfo['id']));
        if (!$bill_info){
            exit();
        }

        $order_condition = array();
        $order_condition['order_state'] = ORDER_STATE_SUCCESS;
        $order_condition['agent_id'] = $bill_info['ob_agent_id'];
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']) : null;
        $end_unixtime = $if_end_date ? $end_unixtime+86400-1 : null;
        if ($if_start_date || $if_end_date) {
            $order_condition['finnshed_time'] = array('between',"{$start_unixtime},{$end_unixtime}");
        } else {
            $order_condition['finnshed_time'] = array('between',"{$bill_info['ob_start_date']},{$bill_info['ob_end_date']}");
        }
	

		//订单列表
		$model_order = Model('order');
		if ($_POST['query'] != '' && in_array($_POST['qtype'],array('order_sn','buyer_name'))) {
			$order_condition[$_POST['qtype']] = array('like',"%{$_POST['query']}%");
		}
		if ($_GET['order_sn'] != ''){
			$order_condition['order_sn'] = array('like',"%{$_GET['order_sn']}%");
		}
		if ($_GET['buyer_name'] != ''){
			if ($_GET['jq_query']) {
				$order_condition['buyer_name'] = $_GET['buyer_name'];
			} else {
				$order_condition['buyer_name'] = array('like',"%{$_GET['buyer_name']}%");
			}
		}

		$sort_fields = array('order_amount','shipping_fee','commis_amount','add_time','finnshed_time','buyer_id','store_id');
		if (in_array($_POST['sortorder'],array('asc','desc')) && in_array($_POST['sortname'],$sort_fields)) {
			$order = $_POST['sortname'].' '.$_POST['sortorder'];
		}
		$order_list = $model_order->getOrderList($order_condition,$_POST['rp'],'*',$order);

		//然后取订单商品佣金
		$order_id_array = array();
		if (is_array($order_list)) {
			foreach ($order_list as $order_info) {
				$order_id_array[] = $order_info['order_id'];
			}
		}
		$order_goods_condition = array();
		$order_goods_condition['order_id'] = array('in',$order_id_array);
		$field = 'SUM(ROUND(goods_pay_price*commis_rate/100,2)) as commis_amount,order_id';
		$commis_list = $model_order->getOrderGoodsList($order_goods_condition,$field,null,null,'','order_id','order_id');

		$data = array();
		$data['now_page'] = $model_order->shownowpage();
		$data['total_num'] = $model_order->gettotalnum();
		foreach ($order_list as $order_info) {
			$list = array();
			$list['operation'] = "<a class=\"btn green\" href=\"index.php?w=order&t=show_order&order_id={$order_info['order_id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
			$list['order_sn'] = $order_info['order_sn'];
			$list['order_amount'] = wtPriceFormat($order_info['order_amount']);
			$list['commis_amount'] = wtPriceFormat($commis_list[$order_info['order_id']]['commis_amount']);
			$list['result_totals'] = wtPriceFormat($list['commis_amount']*0.01*$bill_info['ob_commis']);
			$list['add_time'] = date('Y-m-d',$order_info['add_time']);
			$list['finnshed_time'] = date('Y-m-d',$order_info['finnshed_time']);
			$list['buyer_name'] = $order_info['buyer_name'];
			$list['buyer_id'] = $order_info['buyer_id'];
			$list['store_name'] = $order_info['store_name'];
			$list['store_id'] = $order_info['store_id'];
			$data['list'][$order_info['order_id']] = $list;
		}
		exit(Tpl::flexigridXML($data));
	
    }



    /**
     * 账单付款
     *
     */
    public function bill_payWt(){
        $ob_id = intval($_GET['ob_id']);
        if ($ob_id <= 0) {
            showMessage('参数错误1','','html','error');
        }
        $model_bill = Model('agencies_bill');
        
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['ob_agent_id'] = $admininfo['id'];
        $condition['ob_id'] = $ob_id;
        $condition['ob_state'] = BILL_STATE_STORE_COFIRM;
        $bill_info = $model_bill->getAgentBillInfo($condition);
        if (!$bill_info){
            showMessage('参数错误','','html','error');
        }
		
		
		$admininfo = $this->getAdminInfo();
		$store_id = $bill_info['ob_store_id'];
		$store_info = Model('store')->getStoreInfoByID($store_id);
		if(!empty($store_info)){
			
			//$model_pd = Model('predeposit');
			$input = array();
			$input['ob_pay_content'] = '系统结算';
			$input['ob_pay_date'] = time();
			$input['ob_state'] = BILL_STATE_SUCCESS;
			$update = $model_bill->editOrderBill($input,$condition);
			if ($update){

				$this->log('账单付款,账单号：'.$ob_id,1);
				showMessage('保存成功','index.php?w=agencies_bill');
			}else{
				$this->log('账单付款,账单号：'.$ob_id,1);
				showMessage('保存失败','','html','error');
			}
		}else{
			showMessage('保存失败,店铺信息不存在','','html','error');
		}
        
    }

    /**
     * 打印结算单
     *
     */
    public function bill_printWt(){
        $ob_id = intval($_GET['ob_id']);
        if ($ob_id <= 0) {
            showMessage('参数错误','','html','error');
        }
        $model_bill = Model('agencies_bill');
        
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['ob_agent_id'] = $admininfo['id'];
        $condition['ob_id'] = $ob_id;
        $condition['ob_state'] = BILL_STATE_SUCCESS;
        $bill_info = $model_bill->getAgentBillInfo($condition);
        if (!$bill_info){
            showMessage('参数错误','','html','error');
        }

        Tpl::output('bill_info',$bill_info);
				
		Tpl::setDirquna('agents');
        Tpl::showpage('agencies_bill.print','null_layout');
    }


    /**
     * 导出平台月出账单表
     *
     */
    public function export_billWt(){
        $model_bill = Model('agencies_bill');
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['ob_agent_id'] = $admininfo['id'];
        if (preg_match('/^[\d,]+$/', $_GET['ob_id'])) {
            $_GET['ob_id'] = explode(',',trim($_GET['ob_id'],','));
            $condition['ob_id'] = array('in',$_GET['ob_id']);
        }
        list($condition,$order) = $this->_get_bill_condition($condition);

        if (!is_numeric($_GET['curpage'])){
            $count = $model_bill->getAgentBillCount($condition);
            $array = array();
            if ($count > self::EXPORT_SIZE){
                //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','javascript:history.back(-1)');
                Tpl::showpage('export.excel');
                exit();
            }
            $limit = false;
        }else{
            //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = "{$limit1},{$limit2}";
        }
        $data = $model_bill->getAgentBillList($condition,'*','','ob_id desc',$limit);

        $export_data = array();
        $export_data[0] = array('账单编号','开始日期','结束日期','订单金额','运费','佣金金额','退款金额','退还佣金','店铺费用','本期应结','出账日期','账单状态','店铺','店铺ID');
        if(C('fenxiao_isuse') == 1) {
        $export_data[0] = array('账单编号','开始日期','结束日期','订单金额','运费','佣金金额','退款金额','退还佣金','店铺费用','分销佣金','本期应结','出账日期','账单状态','店铺','店铺ID');
        }
        $ob_order_totals = 0;
        $ob_shipping_totals = 0;
        $ob_commis_totals = 0;
        $ob_order_return_totals = 0;
        $ob_commis_return_totals = 0;
        $ob_store_cost_totals = 0;
        $ob_fx_pay_amount = 0;
        $ob_result_totals = 0;
        foreach ($data as $k => $v) {
            $export_data[$k+1][] = $v['ob_id'];
            $export_data[$k+1][] = date('Y-m-d',$v['ob_start_date']);
            $export_data[$k+1][] = date('Y-m-d',$v['ob_end_date']);
            $ob_order_totals += $export_data[$k+1][] = $v['ob_order_totals'];
            $ob_shipping_totals += $export_data[$k+1][] = $v['ob_shipping_totals'];
            $ob_commis_totals += $export_data[$k+1][] = $v['ob_commis_totals'];
            $ob_order_return_totals += $export_data[$k+1][] = $v['ob_order_return_totals'];
            $ob_commis_return_totals += $export_data[$k+1][] = $v['ob_commis_return_totals'];
            $ob_store_cost_totals += $export_data[$k+1][] = $v['ob_store_cost_totals'];
            $ob_fx_pay_amount += $export_data[$k+1][] = $v['ob_fx_pay_amount'];
            $ob_result_totals += $export_data[$k+1][] = $v['ob_result_totals'];
            $export_data[$k+1][] = date('Y-m-d',$v['ob_create_date']);
            $export_data[$k+1][] = billState($v['ob_state']);
            $export_data[$k+1][] = $v['ob_store_name'];
            $export_data[$k+1][] = $v['ob_store_id'];
        }
        $count = count($export_data);
        $export_data[$count][] = '';
        $export_data[$count][] = '';
        $export_data[$count][] = '合计';
        $export_data[$count][] = $ob_order_totals;
        $export_data[$count][] = $ob_shipping_totals;
        $export_data[$count][] = $ob_commis_totals;
        $export_data[$count][] = $ob_order_return_totals;
        $export_data[$count][] = $ob_commis_return_totals;
        $export_data[$count][] = $ob_store_cost_totals;
        if(C('fenxiao_isuse') == 1) {
        $export_data[$count][] = $ob_fx_pay_amount;
        }
        $export_data[$count][] = $ob_result_totals;
        $csv = new Csv();
        $export_data = $csv->charset($export_data,CHARSET,'gbk');
        $csv->filename = 'bill';
        $csv->export($export_data);
    }

    /**
     * 导出结算订单明细CSV
     *
     */
    public function export_orderWt(){
        $ob_id = intval($_GET['ob_id']);
        if ($ob_id <= 0) {
            exit();
        }
        $model_bill = Model('agencies_bill');
		$admininfo = $this->getAdminInfo();
        $bill_info = $model_bill->getAgentBillInfo(array('ob_id'=>$ob_id,'ab_agent_id'=>$admininfo['id']));
        if (!$bill_info){
            exit();
        }

        $model_order = Model('order');
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['agent_id'] = $admininfo['id'];
        $condition['order_state'] = ORDER_STATE_SUCCESS;
        $condition['store_id'] = $bill_info['ob_store_id'];
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']) : null;
        $end_unixtime = $if_end_date ? $end_unixtime+86400-1 : null;
        if ($if_start_date || $if_end_date) {
            $condition['finnshed_time'] = array('between',"{$start_unixtime},{$end_unixtime}");
        } else {
            $condition['finnshed_time'] = array('between',"{$bill_info['ob_start_date']},{$bill_info['ob_end_date']}");
        }
        if (preg_match('/^[\d,]+$/', $_GET['order_id'])) {
            $_GET['order_id'] = explode(',',trim($_GET['order_id'],','));
            $condition['order_id'] = array('in',$_GET['order_id']);
        }

        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('order_sn','buyer_name'))) {
            $condition[$_REQUEST['qtype']] = array('like',"%{$_REQUEST['query']}%");
        }
        if ($_GET['order_sn'] != ''){
            $condition['order_sn'] = array('like',"%{$_GET['order_sn']}%");
        }
        if ($_GET['buyer_name'] != ''){
            if ($_GET['jq_query']) {
                $condition['buyer_name'] = $_GET['buyer_name'];
            } else {
                $condition['buyer_name'] = array('like',"%{$_GET['buyer_name']}%");
            }
        }

        $sort_fields = array('order_amount','shipping_fee','commis_amount','add_time','finnshed_time','buyer_id','store_id');
        if (in_array($_POST['sortorder'],array('asc','desc')) && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }

        if (!is_numeric($_GET['curpage'])){
            $count = $model_order->getOrderCount($condition);
            $array = array();
            if ($count > self::EXPORT_SIZE ){
                //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl','index.php?w=bill&t=show_bill&ob_id='.$ob_id);
								
		Tpl::setDirquna('agents');
                Tpl::showpage('export.excel');
                exit();
            }
            $limit = false;
        }else{
            //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $limit = "{$limit1},{$limit2}";
        }
        $data = $model_order->getOrderList($condition,'','*','order_id desc',$limit,array('order_goods'));

        //订单商品表查询条件
        $order_id_array = array();
        if (is_array($data)) {
            foreach ($data as $order_info) {
                $order_id_array[] = $order_info['order_id'];
            }
        }
        $order_goods_condition = array();
        $order_goods_condition['order_id'] = array('in',$order_id_array);

        $export_data = array();
        $export_data[0] = array('订单编号','订单金额','运费','佣金','下单日期','成交日期','商家','商家编号','买家','买家编号','商品');
        $order_totals = 0;
        $shipping_totals = 0;
        $commis_totals = 0;
        $k = 0;
        foreach ($data as $v) {
            //该订单算佣金
            $field = 'SUM(ROUND(goods_pay_price*commis_rate/100,2)) as commis_amount,order_id';
            $commis_list = $model_order->getOrderGoodsList($order_goods_condition,$field,null,null,'','order_id','order_id');
            $export_data[$k+1][] = $v['order_sn'];
            $order_totals += $export_data[$k+1][] = $v['order_amount'];
            $shipping_totals += $export_data[$k+1][] = $v['shipping_fee'];
            $commis_totals += $export_data[$k+1][] = floatval($commis_list[$v['order_id']]['commis_amount']);
            $export_data[$k+1][] = date('Y-m-d',$v['add_time']);
            $export_data[$k+1][] = date('Y-m-d',$v['finnshed_time']);
            $export_data[$k+1][] = $v['store_name'];
            $export_data[$k+1][] = $v['store_id'];
            $export_data[$k+1][] = $v['buyer_name'];
            $export_data[$k+1][] = $v['buyer_id'];
            $goods_string = '';
            if (is_array($v['extend_order_goods'])) {
                foreach ($v['extend_order_goods'] as $v) {
                    $goods_string .= $v['goods_name'].'|单价:'.$v['goods_price'].'|数量:'.$v['goods_num'].'|实际支付:'.$v['goods_pay_price'].'|佣金比例:'.$v['commis_rate'].'%';
                }
            }
            $export_data[$k+1][] = $goods_string;
            $k++;
        }
        $count = count($export_data);
        $export_data[$count][] = '合计';
        $export_data[$count][] = $order_totals;
        $export_data[$count][] = $shipping_totals;
        $export_data[$count][] = $commis_totals;
        $csv = new Csv();
        $export_data = $csv->charset($export_data,CHARSET,'gbk');
        $csv->filename = $ob_id.'-bill';
        $csv->export($export_data);
    }


    public function get_statis_xmlWt(){
        $condition = array();
        if (preg_match('/^\d{4}$/',$_POST['query'])) {
            $condition['os_year'] = $_POST['query'];
        }
        $sort_fields = array('os_month','os_start_date','os_end_date','os_order_totals','os_shipping_totals','os_commis_totals','os_order_return_totals','os_commis_return_totals','os_store_cost_totals','os_result_totals');
        if (in_array($_POST['sortorder'],array('asc','desc')) && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        $model_bill = Model('bill');
        $bill_list = $model_bill->getOrderStatisList($condition,'*',$_POST['rp'],$order);
        $data = array();
        $data['now_page'] = $model_bill->shownowpage();
        $data['total_num'] = $model_bill->gettotalnum();
        foreach ($bill_list as $bill_info) {
            $list = array();
            $list['operation'] = "<a class=\"btn green\" href=\"index.php?w=bill&t=show_statis&os_month={$bill_info['os_month']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            $list['os_month'] = substr($bill_info['os_month'],0,4).'-'.substr($bill_info['os_month'],4);
            $list['os_start_date'] = date('Y-m-d',$bill_info['os_start_date']);
            $list['os_end_date'] = date('Y-m-d',$bill_info['os_end_date']);
            $list['os_order_totals'] = wtPriceFormat($bill_info['os_order_totals']);
            $list['os_shipping_totals'] = wtPriceFormat($bill_info['os_shipping_totals']);
            $list['os_commis_totals'] = wtPriceFormat($bill_info['os_commis_totals']);
            $list['os_order_return_totals'] = wtPriceFormat($bill_info['os_order_return_totals']);
            $list['os_commis_return_totals'] = wtPriceFormat($bill_info['os_commis_return_totals']);
            $list['os_store_cost_totals'] = wtPriceFormat($bill_info['os_store_cost_totals']);
            $list['os_result_totals'] = wtPriceFormat($bill_info['os_result_totals']);
            $data['list'][$bill_info['os_month']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    public function get_bill_xmlWt(){
        $model_bill = Model('agencies_bill');
        
		$admininfo = $this->getAdminInfo();
        $condition = array();
		$condition['ob_agent_id'] = $admininfo['id'];
        list($condition,$order) = $this->_get_bill_condition($condition);
        $bill_list = $model_bill->getAgentBillList($condition,'*',$_POST['rp'],$order);
        $data = array();
        $data['now_page'] = $model_bill->shownowpage();
        $data['total_num'] = $model_bill->gettotalnum();
        foreach ($bill_list as $bill_info) {
            $list = array();
            if (in_array($bill_info['ob_state'],array(2,3))) {
                $list['operation'] = "<a class=\"btn orange\" href=\"index.php?w=agencies_bill&t=show_bill&ob_id={$bill_info['ob_id']}\"><i class=\"fa fa-gavel\"></i>处理</a>";
            } else {
                $list['operation'] = "<a class=\"btn green\" href=\"index.php?w=agencies_bill&t=show_bill&ob_id={$bill_info['ob_id']}\"><i class=\"fa fa-list-alt\"></i>查看</a>";
            }

            $list['ob_id'] = $bill_info['ob_id'];
            $list['ob_agent_id'] = $bill_info['ob_agent_id'];
            $list['ob_agent_name'] = $bill_info['ob_agent_name'];
            $list['ob_order_totals'] = wtPriceFormat($bill_info['ob_order_totals']);
            $list['ob_commis_totals'] = wtPriceFormat($bill_info['ob_commis_totals']);
            $list['ob_result_totals'] = wtPriceFormat($bill_info['ob_result_totals']);
            $list['ob_create_date'] = date('Y-m-d',$bill_info['ob_create_date']);
            $list['ob_state'] = billState($bill_info['ob_state']);
            $list['ob_start_date'] = date('Y-m-d',$bill_info['ob_start_date']);
            $list['ob_end_date'] = date('Y-m-d',$bill_info['ob_end_date']);
            $data['list'][$bill_info['ob_id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    /**
     * 合并相同代码
     */
    private function _get_bill_condition($condition) {
        if ($_GET['query_year'] && $_GET['query_month']) {
            $_GET['os_month'] = intval($_GET['query_year'].$_GET['query_month']);
        } elseif ($_GET['query_year']) {
            $condition['os_month'] = array('between',$_GET['query_year'].'01,'.$_GET['query_year'].'12');
        }
        if (!empty($_GET['os_month'])) {
            $condition['os_month'] = intval($_GET['os_month']);
        }
        if ($_REQUEST['query'] != '' && in_array($_REQUEST['qtype'],array('ob_no','ob_id','ob_agent_name'))) {
            $condition[$_REQUEST['qtype']] = $_REQUEST['query'];
        }
        if (is_numeric($_GET["ob_state"])) {
            $condition['ob_state'] = intval($_GET["ob_state"]);
        }
        if (is_numeric($_GET["ob_no"])) {
            $condition['ob_no'] = intval($_GET["ob_no"]);
        }
        if (is_numeric($_GET["ob_id"])) {
            $condition['ob_id'] = intval($_GET["ob_id"]);
        }
        if (is_numeric($_GET["agent_id"])) {
            $condition['ob_agent_id'] = intval($_GET["ob_agent_id"]);
        }
        if ($_GET['ob_agent_name'] != ''){
            if ($_GET['jq_query']) {
                $condition['ob_agent_name'] = $_GET['ob_agent_name'];
            } else {
                $condition['ob_agent_name'] = array('like',"%{$_GET['ob_agent_name']}%");
            }
        }
        $sort_fields = array('ob_id','ob_start_date','ob_end_date','ob_order_totals','ob_commis_totals','ob_result_totals','ob_create_date','ob_state','ob_agent_id');
        if (in_array($_REQUEST['sortorder'],array('asc','desc')) && in_array($_REQUEST['sortname'],$sort_fields)) {
            $order = $_REQUEST['sortname'].' '.$_REQUEST['sortorder'];
        } else {
            $order = 'ob_id desc';
        }
        return array($condition,$order);
    }
}
