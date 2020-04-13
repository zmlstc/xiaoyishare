<?php
/**
 * 预存款管理
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class store_predepositControl extends SystemControl{
    const EXPORT_SIZE = 1000;
    public function __construct(){
        parent::__construct();
        Language::read('predeposit');
    }

    public function indexWt() {
        $this->pd_cash_listWt();
    }

    /**
     * 预存款日志
     */
    public function pd_log_listWt(){
		Tpl::setDirquna('shop');
        Tpl::showpage('store_pd_log.list');
    }

    /**
     * 提现列表
     */
    public function pd_cash_listWt(){
		Tpl::setDirquna('shop');
        Tpl::showpage('store_pd_cash.list');
    }

    /**
     * 删除提现记录
     */
    public function pd_cash_delWt(){
        $id = intval($_GET['id']);
        if ($id > 0) {
            $model_pd = Model('store_predeposit');
            $condition = array();
            $condition['pdc_id'] = $id;
            $condition['pdc_payment_state'] = 0;
            $info = $model_pd->getPdCashInfo($condition);
            if (!$info) {
                exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
            }
            try {
                $result = $model_pd->delPdCash($condition);
                if (!$result) {
                    throw new Exception(Language::get('admin_predeposit_cash_del_fail'));
                }
                //退还冻结的预存款
				$store_info = Model('store')->getStoreInfoByID($info['pdc_store_id']);
                //扣除冻结的预存款
                $admininfo = $this->getAdminInfo();
                $data = array();
                $data['store_id'] = $store_info['store_id'];
                $data['store_name'] = $store_info['store_name'];
                $data['amount'] = $info['pdc_amount'];
                $data['order_sn'] = $info['pdc_sn'];
                $data['admin_name'] = $admininfo['name'];
                $model_pd->changePd('cash_del',$data);
                $model_pd->commit();

                $this->log('提现申请删除[ID:'.$id.']',null);
                exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
            } catch (Exception $e) {
                $model_pd->commit();
                exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
            }
        } else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }

    /**
     * 更改提现为支付状态
     */
    public function pd_cash_payWt(){
        $id = intval($_GET['id']);
        if ($id <= 0){
            showMessage(Language::get('admin_predeposit_parameter_error'),urlAdminShop('store_predeposit','pd_cash_list'),'','error');
        }
        $model_pd = Model('store_predeposit');
        $condition = array();
        $condition['pdc_id'] = $id;
        $condition['pdc_payment_state'] = 0;
        $info = $model_pd->getPdCashInfo($condition);
        if (!is_array($info) || count($info)<0){
            showMessage(Language::get('admin_predeposit_record_error'),urlAdminShop('store_predeposit','pd_cash_list'),'','error');
        }

        //查询信息
        $store_info = Model('store')->getStoreInfoByID($info['pdc_store_id']);

        $update = array();
        $admininfo = $this->getAdminInfo();
        $update['pdc_payment_state'] = 1;
        $update['pdc_payment_admin'] = $admininfo['name'];
        $update['pdc_payment_time'] = TIMESTAMP;
        $log_msg = L('admin_predeposit_cash_edit_state').','.L('admin_predeposit_cs_sn').':'.$info['pdc_sn'];

        try {
            $model_pd->beginTransaction();
            $result = $model_pd->editPdCash($update,$condition);
            if (!$result) {
                throw new Exception(Language::get('admin_predeposit_cash_edit_fail'));
            }
            //扣除冻结的预存款
            $data = array();
            $data['store_id'] = $store_info['store_id'];
            $data['store_name'] = $store_info['store_name'];
            $data['amount'] = $info['pdc_amount'];
            $data['order_sn'] = $info['pdc_sn'];
            $data['admin_name'] = $admininfo['name'];
            $model_pd->changePd('cash_pay',$data);
            $model_pd->commit();
            $this->log($log_msg,1);
            showMessage(Language::get('admin_predeposit_cash_edit_success'),urlAdminShop('store_predeposit','pd_cash_list'));
        } catch (Exception $e) {
            $model_pd->rollback();
            $this->log($log_msg,0);
            showMessage($e->getMessage(),urlAdminShop('store_predeposit','pd_cash_list'),'html','error');
        }
    }

    /**
     * 查看提现信息
     */
    public function pd_cash_viewWt(){
        $id = intval($_GET['id']);
        $model_pd = Model('store_predeposit');
        $condition = array();
        $condition['pdc_id'] = $id;
        $info = $model_pd->getPdCashInfo($condition);
        Tpl::output('info',$info);
		Tpl::setDirquna('shop');
        Tpl::showpage('store_pd_cash.view', 'null_layout');
    }


    /**
     * 导出预存款提现记录
     *
     */
    public function export_cash_step1Wt(){
        $condition = array();
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['stime']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['etime']);
        $start_unixtime = $if_start_date ? strtotime($_GET['stime']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['etime']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['pdc_add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
        if (!empty($_GET['store_name'])){
            $condition['pdc_store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if (!empty($_GET['store_id'])){
            $condition['pdc_store_id'] = array('like', '%' . $_GET['store_id'] . '%');
        }
        if (!empty($_GET['user_name'])){
            $condition['pdc_bank_user'] = array('like', '%' . $_GET['user_name'] . '%');
        }
        if ($_GET['pdc_payment_state'] != ''){
            $condition['pdc_payment_state'] = $_GET['pdc_payment_state'];
        }
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['pdc_id'] = array('in', $id_array);
        }

        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        $order = '';
        $param = array('pdr_id', 'pdr_sn', 'pdr_store_id', 'pdr_store_name', 'pdr_amount', 'pdr_add_time', 'pdr_payment_name', 'pdr_trade_sn', 'pdr_payment_state', 'pdr_payment_time', 'pdr_admin');
        if (in_array($_GET['sortname'], $param) && in_array($_GET['sortorder'], array('asc', 'desc'))) {
            $order = $_GET['sortname'] . ' ' . $_GET['sortorder'];
        }
        $model_pd = Model('store_predeposit');

        if (!is_numeric($_GET['curpage'])){
            $count = $model_pd->getPdCashCount($condition);
            $array = array();
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl',urlAdminShop('store_predeposit','pd_cash_list'));
				Tpl::setDirquna('shop');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $data = $model_pd->getPdCashList($condition,'','*',$order,self::EXPORT_SIZE);
                $cashpaystate = array(0=>'未支付',1=>'已支付');
                foreach ($data as $k=>$v) {
                    $data[$k]['pdc_payment_state'] = $cashpaystate[$v['pdc_payment_state']];
                }
                $this->createCashExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_pd->getPdCashList($condition,'','*','pdc_id desc',"{$limit1},{$limit2}");
            $cashpaystate = array(0=>'未支付',1=>'已支付');
            foreach ($data as $k=>$v) {
                $data[$k]['pdc_payment_state'] = $cashpaystate[$v['pdc_payment_state']];
            }
            $this->createCashExcel($data);
        }
    }

    /**
     * 生成导出预存款提现excel
     *
     * @param array $data
     */
    private function createCashExcel($data = array()){
        Language::read('export');
        import('bin.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_tx_no'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'店铺名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_tx_money'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_tx_ctime'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_tx_state'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'店铺ID');
        foreach ((array)$data as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['pdc_sn']);
            $tmp[] = array('data'=>$v['pdc_store_name']);
            $tmp[] = array('format'=>'Number','data'=>wtPriceFormat($v['pdc_amount']));
            $tmp[] = array('data'=>date('Y-m-d H:i:s',$v['pdc_add_time']));
            $tmp[] = array('data'=>$v['pdc_payment_state']);
            $tmp[] = array('data'=>$v['pdc_storeid']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_tx_title'),CHARSET));
        $excel_obj->generateXML($excel_obj->charset(L('exp_tx_title'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }

    /**
     * 预存款明细信息导出
     */
    public function export_mx_step1Wt(){
        $condition = array();
        if ($_GET['id'] != '') {
            $id_array = explode(',', $_GET['id']);
            $condition['lg_id'] = array('in', $id_array);
        }
        if ($_GET['query'] != '') {
            $condition[$_GET['qtype']] = array('like', '%' . $_GET['query'] . '%');
        }
        $order = '';
        $param = array('lg_id', 'lg_store_id', 'lg_store_name', 'lg_av_amount', 'lg_freeze_amount', 'lg_add_time', 'lg_desc', 'lg_admin_name');
        if (in_array($_GET['sortname'], $param) && in_array($_GET['sortorder'], array('asc', 'desc'))) {
            $order = $_GET['sortname'] . ' ' . $_GET['sortorder'];
        }
        $model_pd = Model('store_predeposit');
        if (!is_numeric($_GET['curpage'])){
            $count = $model_pd->getPdLogCount($condition);
            $array = array();
            if ($count > self::EXPORT_SIZE ){   //显示下载链接
                $page = ceil($count/self::EXPORT_SIZE);
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*self::EXPORT_SIZE + 1;
                    $limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Tpl::output('list',$array);
                Tpl::output('murl',urlAdminShop('store_predeposit','pd_log_list'));
				Tpl::setDirquna('shop');
                Tpl::showpage('export.excel');
            }else{  //如果数量小，直接下载
                $data = $model_pd->getPdLogList($condition,'','*','lg_id desc',self::EXPORT_SIZE);
                $this->createmxExcel($data);
            }
        }else{  //下载
            $limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
            $limit2 = self::EXPORT_SIZE;
            $data = $model_pd->getPdLogList($condition,'','*','lg_id desc',"{$limit1},{$limit2}");
            $this->createmxExcel($data);
        }
    }

    /**
     * 导出预存款明细excel
     *
     * @param array $data
     */
    private function createmxExcel($data = array()){
        Language::read('export');
        import('bin.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'店铺名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_mx_ctime'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_mx_av_money'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_mx_freeze_money'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_mx_system'));
        $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_mx_mshu'));
        foreach ((array)$data as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['lg_store_name']);
            $tmp[] = array('data'=>date('Y-m-d H:i:s',$v['lg_add_time']));
            if (floatval($v['lg_av_amount']) == 0){
                $tmp[] = array('data'=>'');
            } else {
                $tmp[] = array('format'=>'Number','data'=>wtPriceFormat($v['lg_av_amount']));
            }
            if (floatval($v['lg_freeze_amount']) == 0){
                $tmp[] = array('data'=>'');
            } else {
                $tmp[] = array('format'=>'Number','data'=>wtPriceFormat($v['lg_freeze_amount']));
            }
            $tmp[] = array('data'=>$v['lg_admin_name']);
            $tmp[] = array('data'=>$v['lg_desc']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_mx_rz'),CHARSET));
        $excel_obj->generateXML($excel_obj->charset(L('exp_mx_rz'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
    }


    /**
     * 输出提现XML数据
     */
    public function get_cash_xmlWt() {
        $model_pd = Model('store_predeposit');
        $condition = array();
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['stime']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['etime']);
        $start_unixtime = $if_start_date ? strtotime($_GET['stime']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['etime']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['pdc_add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
        if (!empty($_GET['store_name'])){
            $condition['pdc_store_name'] = array('like', '%' . $_GET['store_name'] . '%');
        }
        if (!empty($_GET['store_id'])){
            $condition['pdc_store_id'] = array('like', '%' . $_GET['store_id'] . '%');
        }
        if (!empty($_GET['user_name'])){
            $condition['pdc_bank_user'] = array('like', '%' . $_GET['user_name'] . '%');
        }
        if ($_GET['pdc_payment_state'] != ''){
            $condition['pdc_payment_state'] = $_GET['pdc_payment_state'];
        }
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('pdc_id', 'pdc_sn', 'pdc_store_id', 'pdc_store_name', 'pdc_amount', 'pdc_add_time', 'pdc_bank_name', 'pdc_bank_no'
                ,'pdc_bank_user','pdc_payment_state','pdc_payment_time','pdc_payment_admin'
        );
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $cash_list = $model_pd->getPdCashList($condition,$page,'*',$order);
        $data = array();
        $data['now_page'] = $model_pd->shownowpage();
        $data['total_num'] = $model_pd->gettotalnum();
        foreach ($cash_list as $value) {
            $param = array();
            $param['operation'] = "";
            if ($value['pdc_payment_state'] == 0) {
                $param['operation'] .= "<a class='btn red' href=\"javascript:void(0)\" onclick=\"fg_delete('" . $value['pdc_id'] . "')\"><i class='fa fa-trash-o'></i>删除</a>";
            }
            $param['operation'] .= "<a class='btn green' href='javascript:void(0)' onclick=\"ajax_form('cash_info','查看提现编号“". $value['pdc_sn'] ."”的明细', '".urlAdminShop('store_predeposit','pd_cash_view',array('id'=>$value['pdc_id']))."', 640)\" ><i class='fa fa-list-alt'></i>查看</a>";
            $param['pdc_id'] = $value['pdc_id'];
            $param['pdc_sn'] = $value['pdc_sn'];
            $param['pdc_store_id'] = $value['pdc_store_id'];
            $param['pdc_store_name'] = $value['pdc_store_name'];
            $param['pdc_amount'] = wtPriceFormat($value['pdc_amount']);
            $param['pdc_add_time'] = date('Y-m-d', $value['pdc_add_time']);
            $param['pdc_bank_name'] = preg_replace('/[\x00-\x1F]/','',$value['pdc_bank_name']);
            $param['pdc_bank_no'] = preg_replace('/[\x00-\x1F]/','',$value['pdc_bank_no']);
            $param['pdc_bank_user'] = preg_replace('/[\x00-\x1F]/','',$value['pdc_bank_user']);
            $param['pdc_payment_state'] = $value['pdc_payment_state'] == '0' ? '未支付' : '已支付';
            $param['pdc_payment_time'] = $value['pdc_payment_time'] > 0 ? date('Y-m-d', $value['pdc_payment_time']) : '';
            $param['pdc_payment_admin'] = $value['pdc_payment_admin'];
            $data['list'][$value['pdc_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 输出预存款明细XML数据
     */
    public function get_log_xmlWt() {
        $model_pd = Model('store_predeposit');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('lg_id', 'lg_store_id', 'lg_store_name', 'lg_av_amount', 'lg_freeze_amount', 'lg_add_time', 'lg_desc', 'lg_admin_name');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $log_list = $model_pd->getPdLogList($condition,$page,'*',$order);
        $data = array();
        $data['now_page'] = $model_pd->shownowpage();
        $data['total_num'] = $model_pd->gettotalnum();
        foreach ($log_list as $value) {
            $param = array();
            $param['operation'] = "--";
            $param['lg_id'] = $value['lg_id'];
            $param['lg_store_id'] = $value['lg_store_id'];
            $param['lg_store_name'] = $value['lg_store_name'];
            $param['lg_av_amount'] = wtPriceFormat($value['lg_av_amount']);
            $param['lg_freeze_amount'] = wtPriceFormat($value['lg_freeze_amount']);
            $param['lg_add_time'] = date('Y-m-d', $value['lg_add_time']);
            $param['lg_desc'] = $value['lg_desc'];
            $param['lg_admin_name'] = $value['lg_admin_name'];
            $data['list'][$value['lg_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }
}
