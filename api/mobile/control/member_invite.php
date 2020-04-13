<?php
/**
 * 我的余额
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');


class member_inviteControl extends mobileMemberControl {

	public function __construct() {
		parent::__construct();
	}

    /**
     * 添加返利
     */
    public function indexWt() {
		
		$member_id=$this->member_info['member_id'];
		
        $memberInfo = Model('member')->getMemberInfo(array('member_id'=>$member_id));
		
		$encode_member_id = base64_encode(intval($member_id)*1);
	    $myurl=BASE_SITE_URL."/#V5".$encode_member_id;
		
		$str_member="memberqr_".$member_id;
		$myurl_src=UPLOAD_SITE_URL.DS."shop".DS."member".DS.$str_member.'.png';
		$imgfile=BASE_UPLOAD_PATH.DS."shop".DS."member".DS.$str_member . '.png';
		if(!file_exists($imgfile)){			
			require_once(BASE_STATIC_PATH.DS.'phpqrcode'.DS.'index.php');
			$PhpQRCode = new PhpQRCode();
			
			$PhpQRCode->set('pngTempDir',BASE_UPLOAD_PATH.DS."shop".DS."member".DS);
			$PhpQRCode->set('date',$myurl);
			$PhpQRCode->set('pngTempName', $str_member . '.png');
			$PhpQRCode->init();
		}
		$member_info = array();
        $member_info['user_name'] = $this->member_info['member_name'];
        $member_info['avator'] = getMemberAvatarForID($this->member_info['member_id']);
        $member_info['point'] = $this->member_info['member_points'];
        $member_info['predepoit'] = $this->member_info['available_predeposit'];
		$member_info['available_rc_balance'] = $this->member_info['available_rc_balance'];
        $member_info['myurl']=$myurl;
		$member_info['myurl_src']=$myurl_src;
		$member_info['invite_bm']=createUserInviteCode($member_id);
		$member_info['user_url'] = WAP_SITE_URL . '/index.php?ubm='.$member_info['invite_bm'];
		$member_info['store_url']= WAP_SITE_URL . '/index.php?sbm='.$member_info['invite_bm'];
		$member_info['nickname'] = $memberInfo['nickname'];
		//下载连接
		/* $mydownurl=BASE_SITE_URL."/index.php?act=invite&op=downqrfile&id=".$member_id;
		$member_info['mydownurl']=$mydownurl; */
        output_data(array('member_info' => $member_info,'is_realverify'=>$memberInfo['is_realverify']));
    }
	
		
	public function minvitenumWt() {

		$member_model = Model('member');
		$memberid = $this->member_info['member_id'];

		$condition = array();
		$condition['inviter_id'] = $memberid ;
		if(!empty($_POST['keyword'])&&trim($_POST['keyword'])!='')
		{
			$keyword = trim($_POST['keyword']);
			$condition['member_name|nickname'] = array('like','%'.$keyword.'%');
		}

        $num = $member_model->getMemberCount($condition); 
		
        output_data(array('num' => $num));

    }
	
	public function memberlistWt() {

		$member_model = Model('member');
		$memberid = $this->member_info['member_id'];

		$condition = array();
		$condition['inviter_id'] = $memberid ;
		if(!empty($_POST['keyword'])&&trim($_POST['keyword'])!='')
		{
			$keyword = trim($_POST['keyword']);
			$condition['member_name|nickname'] = array('like','%'.$keyword.'%');
		}

        $_list = $member_model->getMembersList($condition,20); 
		$page_count = $member_model->gettotalpage(); 
		$list = array();
		if($_list){
			foreach($_list as $key => $val)
			{
				$data = array();
				$data['date'] = date('Y/m/d',$val['member_time']);
				$data['nickname'] = $val['nickname'];
				$data['avatar'] = getMemberAvatarForID($val['member_id']);
				$list[] = $data;
			}
		}
        output_data(array('list' => $list),mobile_page($page_count));

    }
	
			
	public function sinvitenumWt() {

		$store_model = Model('store');
		$memberid = $this->member_info['member_id'];

		$condition = array();
		$condition['invite_bm'] = $memberid ;
		if(!empty($_POST['keyword'])&&trim($_POST['keyword'])!='')
		{
			$keyword = trim($_POST['keyword']);
			$condition['store_name'] = array('like','%'.$keyword.'%');
		}

        $num = $store_model->getStoreCount($condition); 
		
        output_data(array('num' => $num));

    }
	
		
	public function storelistWt() {

		$store_model = Model('store');
		$memberid = $this->member_info['member_id'];

		$condition = array();
		$condition['invite_bm'] = $memberid ;
		if(!empty($_POST['keyword'])&&trim($_POST['keyword'])!='')
		{
			$keyword = trim($_POST['keyword']);
			$condition['store_name'] = array('like','%'.$keyword.'%');
		}

        $_list = $store_model->getStoreList($condition,20,'store_id desc'); 
		$page_count = $store_model->gettotalpage(); 
		$list = array();
		if($_list){
			foreach($_list as $key => $val)
			{
				$data = array();
				$data['date'] = date('Y/m/d',$val['store_time']);
				$data['store_name'] = $val['store_name'];
				$data['avatar'] = getStoreLogo($val['store_avatar'], 'store_avatar');
				$list[] = $data;
			}
		}
        output_data(array('list' => $list),mobile_page($page_count));

    }
	
}
