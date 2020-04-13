<?php
/**
 * 店铺
 *
 * @好商城 (c) 2015-2018 33HAO Inc. (http://www.33hao.com)
 * @license    http://www.33 hao.c om
 * @link       交流群号：138182377
 * @since      好商城提供技术支持 授权请购买shopnc授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class seller_voucherControl extends mobileSellerControl{
    public function __construct()
    {
        parent::__construct();
    }
	
	/*
     * 代金券模版列表
     */
    public function templatelistWt(){
        //检查过期的代金券模板状态设为失效
        $this->check_voucher_template_expire();
        $model_voucher = Model('voucher');

        //查询列表
        $param = array();
        $param['voucher_t_gettype'] = 3;//免费领取
        $param['voucher_t_store_id'] = $this->store_info['store_id'];
        $param['voucher_t_state'] = 1;
		
      
      /*   if($_GET['txt_startdate']){
            $param['voucher_t_end_date'] = array('egt',strtotime($_GET['txt_startdate']));
        }
        if($_GET['txt_enddate']){
            $param['voucher_t_start_date'] = array('elt',strtotime($_GET['txt_enddate']));
        } */
        $list = $model_voucher->getVoucherTemplateList($param, '*', 0, 10, 'voucher_t_id desc');
         $page_count = $model_voucher->gettotalpage();
		if ($list) {
            foreach($list as $k=>$v){
                $v['voucher_t_end_date_text'] = $v['voucher_t_end_date']?@date('Y年m月d日',$v['voucher_t_end_date']):'';
				$v['voucher_t_limit_text'] = $v['voucher_t_limit']==0?'无消费金额限制':'消费满'.$v['voucher_t_limit'].'元使用';
				$v['voucher_t_eachlimit_text'] = '每人限领'.$v['voucher_t_eachlimit'].'次';
				$v['voucher_t_limit'] = floatval($v['voucher_t_limit']);
                $list[$k] = $v;
            }
        }

        output_data(array('list' => $list), mobile_page($page_count));
		
    }
	
	    /*
     * 把代金券模版设为失效
     */
    private function check_voucher_template_expire($voucher_template_id=''){
        $where_array = array();
        if(empty($voucher_template_id)) {
            $where_array['voucher_t_end_date'] = array('lt',time());
        } else {
            $where_array['voucher_t_id'] = $voucher_template_id;
        }
        $where_array['voucher_t_state'] = 1;
        $model = Model();
        $model->table('voucher_template')->where($where_array)->update(array('voucher_t_state'=>2));
    }

    /*
     * 代金券模版添加
     */
    public function voucher_addWt(){
        $model = Model('voucher');

        //查询面额列表
        $pricelist =  $model->table('voucher_price')->order('voucher_price asc')->select();
        if(empty($pricelist)){
            output_error('系统面额查询失败', array('state' => 0));
        }else{
			$list = array();
			foreach($pricelist as $val){
				$list[] = $val['voucher_price'];
			}
			output_data(array('price_list' =>$list));
        }
    }	
    /*
     * 代金券模版添加
     */
    public function voucher_saveWt(){
        $model = Model('voucher');
     

        //查询面额列表
      /*   $pricelist =  $model->table('voucher_price')->order('voucher_price asc')->select();
        if(empty($pricelist)){
            showMessage(Language::get('voucher_template_pricelisterror'),'index.php?act=store_voucher&op=templatelist','html','error');
        } */
        
            //验证提交的内容面额不能大于限额
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST['t_title'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>'名称不能为空且不能大于30个字符'),
                array("input"=>$_POST['t_total'], "require"=>"true","validator"=>"Number","min"=>"1","message"=>'可发放数量不能为空且必须为大于1的整数'),
                array("input"=>$_POST['t_price'], "require"=>"true","validator"=>"Number","message"=>'面额不能为空且必须为整数，且面额不能大于限额'),
                array("input"=>$_POST['t_limit'], "require"=>"true","validator"=>"Double","message"=>'使用消费限额不能为空且必须是数字'),
                //array("input"=>$_POST['txt_template_describe'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"255","message"=>Language::get('voucher_template_describe_error')),
               
            );
            $error = $obj_validate->validate();
            //金额验证
            $price = intval($_POST['t_price'])>0?intval($_POST['t_price']):0;
           /*  foreach($pricelist as $k=>$v){
                if($v['voucher_price'] == $price){
                    $chooseprice = $v;//取得当前选择的面额记录
                }
            }
            if(empty($chooseprice)){
                $error.=Language::get('voucher_template_pricelisterror');
            } */
            $limit = floatval($_POST['t_limit'])>0?floatval($_POST['t_limit']):0;
            if($price>=$limit) output_error('面额必须小于消费价格', array('state' => 0));
          
            if ($error){
                
				output_error($error, array('state' => 0));
            }else {
                $insert_arr = array();
                $insert_arr['voucher_t_title'] = trim($_POST['t_title']);
                $insert_arr['voucher_t_desc'] = '';//trim($_POST['t_describe']);
                $insert_arr['voucher_t_start_date'] = time();//默认代金券模板的有效期为当前时间
                if ($_POST['t_enddate']){
                    $enddate = strtotime($_POST['t_enddate'].' 23:59:59');
                    $insert_arr['voucher_t_end_date'] = $enddate;
                }else {//如果没有添加有效期则默认为套餐的结束时间
                    $insert_arr['voucher_t_end_date'] = time() + 2592000; //  默认30天到期
                    
                }
                $insert_arr['voucher_t_price'] = $price;
                $insert_arr['voucher_t_limit'] = $limit;
                $insert_arr['voucher_t_store_id'] = $this->store_info['store_id'];
                $insert_arr['voucher_t_storename'] = $this->store_info['store_name'];
                $insert_arr['voucher_t_sc_id'] = 0;//intval($_POST['sc_id']);
                $insert_arr['voucher_t_creator_id'] = 0;//$this->store_info['member_id'];
                $insert_arr['voucher_t_state'] = 1;
                $insert_arr['voucher_t_total'] = intval($_POST['t_total'])>0?intval($_POST['t_total']):0;
                $insert_arr['voucher_t_giveout'] = 0;
                $insert_arr['voucher_t_used'] = 0;
                $insert_arr['voucher_t_add_date'] = time();
                $insert_arr['voucher_t_quotaid'] = 0;
                $insert_arr['voucher_t_points'] = 0;
                $insert_arr['voucher_t_eachlimit'] = intval($_POST['t_eachlimit'])>0?intval($_POST['t_eachlimit']):0;
                
                //领取方式
                $insert_arr['voucher_t_gettype'] = 3;
                $insert_arr['voucher_t_isbuild'] = 0;
                //会员级别
                $insert_arr['voucher_t_mgradelimit'] = 0;
                
                $rs = $model->table('voucher_template')->insert($insert_arr);
                if($rs){
                    
                   output_data(array('state' =>'1'));
                }else{
                   output_error('添加失败', array('state' => 0));
                }
            }
        
    }
	
	/*
     * 失效
     */
	public function voucher_delWt(){
        $voucher_template_id = intval($_POST['id']);
		$this->check_voucher_template_expire($voucher_template_id);
		 output_data(array('state' => 1));
	}
	
	
	
	
	
	
	
	/**
     * 代金券列表
     */
    public function voucher_tpl_listWt(){
        $param = $_REQUEST;

        $model_voucher = Model('voucher');
        $templatestate_arr = $model_voucher->getTemplateState();
        $voucher_gettype_array = $model_voucher->getVoucherGettypeArray();

        $where = array();
        $where['voucher_t_state'] = $templatestate_arr['usable'][0];
        $store_id = intval($param['store_id']);
        if ($store_id > 0){
            $where['voucher_t_store_id'] = $store_id;
        }
        $where['voucher_t_gettype'] = array('in',array($voucher_gettype_array['points']['sign'],$voucher_gettype_array['free']['sign']));
        if ($param['gettype'] && in_array($param['gettype'], array('points','free'))) {
            $where['voucher_t_gettype'] = $voucher_gettype_array[$param['gettype']]['sign'];
        }
        $order = 'voucher_t_id asc';
        $voucher_list = $model_voucher->getVoucherTemplateList($where, '*', 20, 0, $order);
        if ($voucher_list) {
            foreach($voucher_list as $k=>$v){
                $v['voucher_t_end_date_text'] = $v['voucher_t_end_date']?@date('Y年m月d日',$v['voucher_t_end_date']):'';
                $voucher_list[$k] = $v;
            }
        }
        output_data(array('voucher_list' => $voucher_list));
    }
}
