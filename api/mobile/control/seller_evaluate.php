<?php
/**
 * 
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class seller_evaluateControl extends mobileSellerControl {

    public function __construct(){
        parent::__construct();
    }
    public function indexWt() {
		
	}
	
	/**
     * 评价列表
     */
    public function listWt(){
        $model_evaluate = Model('evaluate_store');
		$is_reply = intval($_POST['type']);
        $condition = array();
		$condition['is_reply'] = ($is_reply==1? 1:0);//0未回复，1已回复
		
		$condition['member_del'] = 0;
        $condition['seval_storeid'] = $this->store_info['store_id'];
        $goodsevallist = $model_evaluate->getEvaluateStoreList($condition, 10);
		
		$page_count = $model_evaluate->gettotalpage();
		$memberids = array();
		if(!empty($goodsevallist) && is_array($goodsevallist)){
			foreach( $goodsevallist as $key=>$val){
				$memberids[] = $val['seval_memberid'];
			
			}
			
		}
		$memberList = array();
		if(!empty($memberids)&&is_array($memberids))
		{
			$member_list = Model('member')->getMemberList(array('member_id'=>array('in',$memberids)));
			if(!empty($member_list)&&is_array($member_list)){
				foreach($member_list as $val){
					$memberList[$val['member_id']] = $val;
				}
			}
		}
		$eval_list = array();
		if(!empty($goodsevallist) && is_array($goodsevallist)){
			foreach( $goodsevallist as $key=>$val){
				$data =array();
				$data['seval_id'] = $val['seval_id'];
				$data['member_name'] = $memberList[$val['seval_memberid']]['nickname'];
				//$data['store_id'] = $val['store_id'];
				$data['seval_scores'] = $val['seval_scores'];
				$data['seval_content'] = $val['seval_content'];
				$data['seval_explain'] = $val['seval_explain']===null?'':$val['seval_explain'];
				// 头像
				$data['member_avatar'] = getMemberAvatarForID($memberList[$val['seval_memberid']]['member_id']);
				$data['date_txt'] = date('m月d日',$val['seval_addtime']);
				$data['time_txt'] = date('H:i:s',$val['seval_addtime']);
				
				$data['is_reply'] = $val['is_reply'];
				$data['seval_explain'] = $val['seval_explain'];
				$data['reply_date_txt'] = date('m月d日',$val['seval_explain_time']);
				$data['reply_time_txt'] = date('H:i:s',$val['seval_explain_time']);
				$data['reply_del'] = $val['reply_del'];
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
     * 保存
     */
    public function saveWt() {
        $model_evaluate_store = Model('evaluate_store');
		
		$obj_validate = new Validate();
		$validate_arr[] = array("input"=>$_POST["explain"], "require"=>"true","message"=>'回复内容不能为空');
		$validate_arr[] = array("input"=>$_POST["id"], "require"=>"true","message"=>'参数错误');
		$obj_validate -> validateparam = $validate_arr;
		$error = $obj_validate->validate();
		if ($error != ''){
			output_error($error);
		}
        
		$data = array();
		$data['seval_explain'] = $_POST['explain'];
		$data['seval_explain_time'] = time();
		$data['is_reply'] = 1;
		
		$condition = array();
		$condition['seval_storeid'] = $this->store_info['store_id'];
		$condition['seval_id'] = intval($_POST['id']);
        
        $state = $model_evaluate_store->EditEvaluateStore($condition,$data);
        if ($state){
            output_data(array('state' =>1));
        }else{
			output_error('保存失败！');
		}
        
    }
	
	/**
     * 删除
     */
    public function delevlWt() {
        $model_evaluate_store = Model('evaluate_store');
		
		$obj_validate = new Validate();
		$validate_arr[] = array("input"=>$_POST["id"], "require"=>"true","message"=>'参数错误');
		$obj_validate -> validateparam = $validate_arr;
		$error = $obj_validate->validate();
		if ($error != ''){
			output_error($error);
		}
        
		$data = array();
		$data['seval_explain'] = '';
		$data['reply_del'] = 1;
		
		$condition = array();
		$condition['seval_storeid'] = $this->store_info['store_id'];
		$condition['seval_id'] = intval($_POST['id']);
        
        $state = $model_evaluate_store->EditEvaluateStore($condition,$data);
        if ($state){
            output_data(array('state' =>1));
        }else{
			output_error('删除失败！');
		}
        
    }
	
}
