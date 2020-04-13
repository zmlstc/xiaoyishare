<?php
/**
 * 我的代金券
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class member_voucherControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
        // 判断系统是否开启代金券功能
       /*  if (intval(C('voucher_allow')) !== 1) {
            output_error('系统未开启代金券功能');
        } */
    }
    /**
     * 我的代金券列表
     */
    public function voucher_listWt() {
        $param = $_POST;
		$member_id = $this->member_info['member_id'];
        $model_voucher = Model('voucher');
        
		$orderby = 'voucher_state asc,voucher_id desc';
		if(!empty($param['bysort'])&&trim($param['bysort'])!=''){
			switch (trim($param['bysort'])){
				case 'dhasc' : $orderby = 'voucher_active_date asc';break;
				case 'dhdesc' : $orderby = 'voucher_active_date desc';break;
				case 'dqasc' : $orderby = 'voucher_end_date asc';break;
				case 'dqdesc' : $orderby = 'voucher_end_date desc';break;
			}
		}
        $voucher_list = $model_voucher->getMemberVoucherList($member_id, $param['state'], 12, $orderby);
        $page_count = $model_voucher->gettotalpage();
		$list = array();
		if(!empty($voucher_list)&&is_array($voucher_list)){
			foreach($voucher_list as $val){
				$data = array();
				$data['voucher_title'] = $val['voucher_title'];
				$data['voucher_price'] = floatval($val['voucher_price']);
				$data['voucher_limit'] = floatval($val['voucher_limit']);
				$data['voucher_end_date_text'] = $val['voucher_end_date_text'];
				$data['voucher_active_date_txt'] = @date('Y-m-d',$val['voucher_active_date']);
				$data['voucher_state_text'] = $val['voucher_state_text'];
				$data['voucher_id'] = $val['voucher_id'];
				$data['voucher_state'] = $val['voucher_state'];
                $data['voucher_store_name'] = $val['store_name'];
                $data['voucher_store_id'] = $val['store_id'];

				$list[] = $data;
			}
		}
        output_data(array('list' => $list), mobile_page($page_count));
    }
    /**
     * 卡密领取代金券
     */
    public function voucher_pwexWt()
    {
        $param = $_POST;

        $pwd_code = trim($param["pwd_code"]);
        if (!$pwd_code){
            output_error('请输入代金券卡密');
        }
        if (!Model('apivercode')->checkApiVercode($param["codekey"],$param['captcha'])) {
            output_error('验证码错误');
        }
        // 查询代金券
        $model_voucher = Model('voucher');
        $voucher_info = $model_voucher->getVoucherInfo(array('voucher_pwd'=>md5($pwd_code)));
        if (!$voucher_info) {
            output_error('代金券卡密错误');
        }
        if ($this->member_info['store_id'] == $voucher_info['voucher_store_id']) {
            output_error('不能领取自己店铺的代金券');
        }
        if ($voucher_info['voucher_owner_id'] > 0) {
            output_error('该代金券卡密已被使用');
        }
        $where = array();
        $where['voucher_id'] = $voucher_info['voucher_id'];
        $update_arr = array();
        $update_arr['voucher_owner_id'] = $this->member_info['member_id'];
        $update_arr['voucher_owner_name'] = $this->member_info['member_name'];
        $update_arr['voucher_active_date'] = time();
        $result = $model_voucher->editVoucher($update_arr, $where, $this->member_info['member_id']);
        if ($result) {
            // 更新代金券模板
            $update_arr = array();
            $update_arr['voucher_t_giveout'] = array('exp', 'voucher_t_giveout+1');
            $model_voucher->editVoucherTemplate(array('voucher_t_id'=>$voucher_info['voucher_t_id']), $update_arr);
            output_data('1');
        } else {
            output_error('代金券领取失败');
        }
    }
    /**
     * 免费领取代金券
     */
    public function voucher_freeexWt() {
        $param = $_REQUEST;

        $t_id = intval($param['tid']);
        if($t_id <= 0){
            output_error('代金券信息错误');
        }
        $model_voucher = Model('voucher');
        //验证是否可领取代金券
        $data = $model_voucher->getCanChangeTemplateInfo($t_id, $this->member_info['member_id']);
        if ($data['state'] == false){
            output_error($data['msg']);
        }
        try {
            $model_voucher->beginTransaction();
            //添加代金券信息
            $data = $model_voucher->exchangeVoucher($data['info'], $this->member_info['member_id'], $this->member_info['member_name']);
            if ($data['state'] == false) {
                throw new Exception($data['msg']);
            }
            $model_voucher->commit();
            output_data('1');
        } catch (Exception $e) {
            $model_voucher->rollback();
            output_error($e->getMessage());
        }
    }

	public function voucherdeleteWt(){
		$v_id = intval($_POST['voucher_id']);
		if($v_id>0){
			$member_id = $this->member_info['member_id'];
			$model_voucher = Model('voucher');
			$return = $model_voucher->delVoucher(array('voucher_owner_id'=>$member_id,'voucher_id'=>$v_id));
			if($return){
				output_data(array('state' => 1));
			}
		}
		output_error('删除失败！', array('state' => 0));
	}	
	
}
