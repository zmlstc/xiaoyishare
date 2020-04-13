<?php
/**
 * 我的商城
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class member_indexControl extends mobileMemberControl {

    public function __construct(){
        parent::__construct();
    }

    /**
     * 我的
     */
    public function indexWt() {
		$memberInfo = Model('member')->getMemberInfoByID($this->member_info['member_id']);
        $member_info = array();
        $member_info['user_name'] = $this->member_info['member_name'];
        $member_info['avatar'] = getMemberAvatarForID($this->member_info['member_id']).'?rd='.time();
		$member_info['member_mobile'] = $memberInfo['member_mobile'];
		$member_info['nickname'] = $memberInfo['nickname'];
		$member_info['is_realverify'] = $memberInfo['is_realverify'];

   
		//公告栏
		$ac_id=1;
		$article_class_model	= Model('article_class');
		$article_model	= Model('article');
		$condition	= array();
		
		$child_class_list = $article_class_model->getChildClass($ac_id);
		$ac_ids	= array();
		$art_class = array();
		if(!empty($child_class_list) && is_array($child_class_list)){
			foreach ($child_class_list as $v){
				$ac_ids[]	= $v['ac_id'];
				$art_class[$v['ac_id']] = $v;
			}
		}
		$ac_ids	= implode(',',$ac_ids);
		$condition['ac_ids']	= $ac_ids;
		$condition['article_show']	= '1';
		$_artlist = $article_model->getArticleList($condition,6);
		$artlist= array();
		if(!empty($_artlist)&&is_array($_artlist)){
			foreach($_artlist as $v){
				$data= array();
				$data['article_id'] = $v['article_id'];
				$data['ac_id'] = $v['ac_id'];
				$data['ac_name'] = $art_class[$v['ac_id']]['ac_name'];
				$data['article_title'] = $v['article_title'];
				$data['article_time'] = $v['article_time'];
				$data['article_time_txt'] = date('m-d',$v['article_time']);
				$artlist[] =$data;
			}
		}
		
		$member_id = $this->member_info['member_id'];
        $message_model = Model('message');
        $condition_arr = array();
        $condition_arr['message_type'] = '1';//系统消息
		$condition_arr['message_state'] = '0';
        $condition_arr['to_member_id'] = $member_id;
        $condition_arr['no_del_member_id'] = $member_id;
        $condition_arr['no_read_member_id'] = $member_id;
		$condition_arr['message_open_common'] = '0';
        $sysnum = $message_model->countMessage($condition_arr);
		
        output_data(array('member_info' => $member_info,'artlist'=>$artlist,'sysnum'=>$sysnum));
    }
    
    /**
     * 我的资产
     */
    public function my_assetWt() {
        $param = $_GET;
        $fields_arr = array('point','predepoit','available_rc_balance','coupon','voucher','tg_predepoit');
        $fields_str = trim($param['fields']);
        if ($fields_str) {
            $fields_arr = explode(',',$fields_str);
        }
        $member_info = array();
        if (in_array('point',$fields_arr)) {
            $member_info['point'] = $this->member_info['member_points'];
        }
        if (in_array('predepoit',$fields_arr)) {
            $member_info['predepoit'] = $this->member_info['available_predeposit'];
        }
        if (in_array('available_rc_balance',$fields_arr)) {
            $member_info['available_rc_balance'] = $this->member_info['available_rc_balance'];
        }
        if (in_array('coupon',$fields_arr)) {
            $member_info['coupon'] = Model('coupon')->getCurrentAvailableCouponCount($this->member_info['member_id']);
        }
        if (in_array('voucher',$fields_arr)) {
            $member_info['voucher'] = Model('voucher')->getCurrentAvailableVoucherCount($this->member_info['member_id']);
        }
		
		if(in_array('tg_predepoit',$fields_arr)){
			$memberInfo = Model('member')->getMemberInfo(array('member_id'=>$this->member_info['member_id']));
			$member_info['tg_predepoit'] =$memberInfo['tg_predeposit'];
		}
        output_data($member_info);
    }

	
	private function upload_image($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'realverify'.DS;
        $upload->set('default_dir',$uploaddir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png'));
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return $pic_name;
    }
		
	public function image_uploadWt() { 
		$img=$this->upload_image('pic');
        output_data(array('image_name' => $img,'thumb_name'=>UPLOAD_SITE_URL.DS.ATTACH_PATH.DS.'realverify'.DS.$img));
    }	
	

    public function realverify_addWt() {
		Log::record(json_encode($_POST));
        if(!empty($_POST)) 
		{
			$memberInfo = Model('member')->getMemberInfoByID($this->member_info['member_id']);
			if($memberInfo['is_realverify']==1){
				 output_error('您已经通过实名验证！');
			}
			if(!checkMIdCard(trim($_POST['cardid']))){
				output_error('身份证号码不正确！');
			}
			
            $model_member_realverify = Model('member_realverify');
            $param = array();
            $param['member_id'] = $this->member_info['member_id'];
            $param['truename'] = trim($_POST['truename']);
            $param['cardid'] = trim($_POST['cardid']);
            $param['pic1'] = $_POST['pic1'];
            $param['pic2'] = $_POST['pic2'];
            $param['iddate'] = $_POST['iddate'];
            $param['state'] = 0;
            $param['addtime'] = time();
			
           Log::record(json_encode($param));
            $this->step2_save_valid($param);

			$ret=$model_member_realverify->add($param);
			if($ret){
				Model('member')-> editMember(array('member_id'=>$this->member_info['member_id']), array('is_realverify'=>2));
				
				//发送用户消息
				$param = array();
				$param['code'] = 'member_reg_succ';
				$param['member_id'] = $this->member_info['member_id'];
				$param['param'] = array();
				QueueClient::push('sendMemberMsg', $param);
				
				output_data(array('state'=>1));
			}
        }
        output_error('数据验证失败！');
        exit;
    }	
	private function step2_save_valid($param) {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['truename'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"10","message"=>"姓名不能为空且必须小于5个字"),
            array("input"=>$param['cardid'], "require"=>"true","validator"=>"Length","min"=>"18","max"=>"18","message"=>"身份证号码不能为空且必须18个字"),
			array("input"=>$param['pic1'], "require"=>"true","message"=>"身份证正面不能为空"),
			array("input"=>$param['pic2'], "require"=>"true","message"=>"身份证背面不能为空"),
			array("input"=>$param['iddate'], "require"=>"true","message"=>"有效期不能为空"),
			
        );
        $error = $obj_validate->validate();
        if ($error != ''){
			output_error($error, array('state' => 0));
        }
    }	
	

    /**
     * 实名验证状态
     */
    public function realverWt() {
		$memberInfo = Model('member')->getMemberInfoByID($this->member_info['member_id']);
        $member_info = array();
        //$member_info['user_name'] = $this->member_info['member_name'];
        //$member_info['avatar'] = getMemberAvatarForID($this->member_info['member_id']).'?rd='.time();
		//$member_info['member_mobile'] = $memberInfo['member_mobile'];
		//$member_info['nickname'] = $memberInfo['nickname'];
		$member_info['is_realverify'] = $memberInfo['is_realverify'];
		if($memberInfo['is_realverify']==1){
			$rv = Model('member_realverify')->getInfo(array('member_id'=>$this->member_info['member_id'],'state'=>1));
			if(!empty($rv)){
				$member_idcard = $rv['cardid'];
				$priv = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 2).'0000'),'area_name');
				$city = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 4).'00'),'area_name');
				$area = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 6).''),'area_name');
				$sexint = (int)substr($member_idcard,16,1);
				
				$data = array();
				//$data['truename'] = $rv['truename'];
				$data['truename'] = $this->_PassStart($rv['truename'],1,5);
				$data['cardid'] = encryptShow($rv['cardid'],5,11);;
				$data['area'] = $priv['area_name'].'  '.$city['area_name'].'  '.$area['area_name'];
				$data['birthday'] = substr($member_idcard, 6, 4).'-'.substr($member_idcard, 10, 2).'-'.substr($member_idcard, 12, 2);
				$data['sex'] = ($sexint % 2 === 0) ? '女' : '男';
				$member_info['realverify'] = $data;
			}else{
				$member_info['is_realverify'] = 0;
			}
		}else if($memberInfo['is_realverify']==2){
			$rv = Model('member_realverify')->getInfo(array('member_id'=>$this->member_info['member_id'],'state'=>0));
			if(!empty($rv)){
				$member_idcard = $rv['cardid'];
				$priv = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 2).'0000'),'area_name');
				$city = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 4).'00'),'area_name');
				$area = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 6).''),'area_name');
				$sexint = (int)substr($member_idcard,16,1);
				
				$data = array();
				//$data['truename'] = $rv['truename'];
				$data['truename'] = $this->_PassStart($rv['truename'],1,5);
				$data['cardid'] = encryptShow($rv['cardid'],5,11);;
				$data['area'] = $priv['area_name'].'  '.$city['area_name'].'  '.$area['area_name'];
				$data['birthday'] = substr($member_idcard, 6, 4).'-'.substr($member_idcard, 10, 2).'-'.substr($member_idcard, 12, 2);
				$data['sex'] = ($sexint % 2 === 0) ? '女' : '男';
				$member_info['realverify'] = $data;
			}else{
				$member_info['is_realverify'] = 0;
			}
		}else{
			$member_info['phone_show'] = encryptShow($this->member_info['member_mobile'],4,4);
			$member_info['phone'] = $this->member_info['member_mobile'];
		}
		
        output_data(array('member_info' => $member_info));
    }	
	
	public function _PassStart($str,$start,$end=0,$dot="*",$charset="UTF-8"){
		$len = mb_strlen($str,$charset);
		if($start==0||$start>$len){
		$start = 1;
		}
		if($end!=0&&$end>$len){
		$end = $len-2;
		}
		$endStart = $len-$end;
		$top = mb_substr($str, 0,$start,$charset);
		$bottom = "";
		if($endStart>0){
		$bottom =  mb_substr($str, $endStart,$end,$charset);
		}
		$len = $len-mb_strlen($top,$charset);
		$len = $len-mb_strlen($bottom,$charset);
		$newStr = $top;
		for($i=0;$i<$len;$i++){
		$newStr.=$dot;
		}
		$newStr.=$bottom;
		return $newStr;
	}
	
	
	
	
	
	
	
	

}
