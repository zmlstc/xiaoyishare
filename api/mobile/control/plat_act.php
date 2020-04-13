<?php
/**
 * 积分活动
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */

defined('ShopWT') or exit('Access Denied By ShopWT');
class plat_actControl extends mobileHomeControl{
	protected $member_info = array();
	public function __construct() {
		parent::__construct();
		
		
	}
	public function indexWt(){
	    $this->plistWt();
	}
	/**
	 * 积分活动列表
	 */
	public function plistWt(){
	   
	    $model_pointprod = Model('plat_act');
		$city_id = intval($_POST['cityid']);
	 
	    
	    $model_member = Model('member');
	   
	    //查询兑换服务列表
	    $where = array();
	    $where['act_state'] = 1;
	    //$where['pgoods_endtime'] = array('gt',time());
	    $where['jh_city_id'] = $city_id;
		
		if (!empty($_POST['keyword'])) {
            $where['act_name'] = array('like', '%' . $_POST['keyword'] . '%');
            if ($_COOKIE['hisSearch2'] == '') {
                $his_sh_list = array();
            } else {
                $his_sh_list = explode('~', $_COOKIE['hisSearch2']);
            }
            if (strlen($_POST['keyword']) <= 20 && !in_array($_POST['keyword'],$his_sh_list)) {
                if (array_unshift($his_sh_list, $_POST['keyword']) > 8) {
                    array_pop($his_sh_list);
                }
            }
            setcookie('hisSearch2', implode('~', $his_sh_list), time()+2592000, '/', SUBDOMAIN_SUFFIX ? SUBDOMAIN_SUFFIX : '', false);

        }

	    //分类
		$gc_id = intval($_POST['gc_id']);
		if($gc_id>0){
			$where['gc_id'] = $gc_id;
		}
	   
		
		
	    //排序
	    switch ($_POST['orderby']){
	    	case 'etime':
	    	    $orderby = 'act_etime desc,';
	    	    break;
	    	case 'points':
	    	    $orderby = 'act_points desc,';
	    	    break;
	    	case 'stimedesc':
	    	    $orderby = 'pgoods_starttime desc,';
	    	    break;
	    	case 'stimeasc':
	    	    $orderby = 'pgoods_starttime asc,';
	    	    break;
	    	case 'pointsdesc':
	    	    $orderby = 'pgoods_points desc,';
	    	    break;
	    	case 'pointsasc':
	    	    $orderby = 'pgoods_points asc,';
	    	    break;
	    }
	    $orderby .= 'act_sort asc,act_id desc';
	    $filed ='act_id,act_name,act_image,gc_id,act_price,act_points,act_storage,act_salenum,act_datetime,act_stime,act_etime,';
		$filed .='act_body,act_state,jh_area_info,jh_address,act_area_info,act_address';
		$pageSize=10;
		$list = $model_pointprod->getActList($where, $filed, $orderby,'',$pageSize);
		$page_count = $model_pointprod->gettotalpage();
		$act_list = array();
		if(!empty($list)&&is_array($list)){
			foreach($list as $v){
				$data= array();
				$data['act_id'] =$v['act_id'];
				$data['act_name'] =$v['act_name'];
				$data['act_image'] =UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$v['act_image'];
				$data['act_price'] =floatval($v['act_price']);
				$data['act_points'] =$v['act_points'];
				$data['act_storage'] =$v['act_storage'];
				$data['act_salenum'] =$v['act_salenum'];
				$data['act_state'] =$v['act_state'];
				$data['jh_area_info'] =$v['jh_area_info'];
				$data['jh_address'] =$v['jh_address'];
				$data['act_area_info'] =$v['act_area_info'];
				$data['act_address'] =$v['act_address'];
				$data['act_date'] =date('m月d日 H:i',$v['act_datetime']);
				$data['act_id'] =$v['act_id'];
				
				$act_list[] = $data;
			}
		}
        
        output_data(array('list' => $act_list), mobile_page($page_count));

	}
	
	
	public function gclistWt(){
		//分类
		$list = Model('plat_act_class')->getActClassList(array('parent_id'=>0), '', 10);
		$gc_list = array();
		if(!empty($list)&&is_array($list)){
			foreach($list as $key=>$sc){
				$gc_info = array();
				$gc_info['gc_id'] = $sc['gc_id'];
				$gc_info['gc_name'] = $sc['gc_name'];
				$gc_list[]= $gc_info;
			}
		}
        output_data(array('gc_list'=>$gc_list));
		
	}
	
	
	
	/**
	 * 详细
	 */
	public function act_detailWt() {
		$pid = intval($_POST['id']);
		if (!$pid){
			 output_error('参数错误!');
		}
		$model_activity = Model('plat_act');
		//查询兑换礼品详细
		$info = $model_activity->getOneById($pid);
		if (empty($info)){
			 output_error('参数错误!');
			
		}		
		
		//更新礼品浏览次数
		$tm_tm_visite_pgoods = cookie('tm_visite_plat_act');
		$tm_tm_visite_pgoods = $tm_tm_visite_pgoods?explode(',', $tm_tm_visite_pgoods):array();
		if (!in_array($pid, $tm_tm_visite_pgoods)){//如果已经浏览过该服务则不重复累计浏览次数 
		    $result = $model_activity->editActViewnum($pid);
 		    if ($result['state'] == true){//累加成功则cookie中增加该服务ID
		        $tm_tm_visite_pgoods[] = $pid;
		        setWtCookie('tm_visite_plat_act',implode(',', $tm_tm_visite_pgoods));
		    }
		}
		$data = array();
		$data['act_id'] =$info['act_id'];
		$data['act_name'] =$info['act_name'];
		$data['act_image'] =UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$info['act_image'];
		$data['act_price'] =floatval($info['act_price']);
		$data['act_points'] =$info['act_points'];
		$data['act_storage'] =$info['act_storage'];
		$data['act_salenum'] =$info['act_salenum'];
		$data['act_state'] =$info['act_state'];
		$data['jh_area_info'] =$info['jh_area_info'];
		$data['jh_address'] =$info['jh_address'];
		$data['act_area_info'] =$info['act_area_info'];
		$data['act_address'] =$info['act_address'];
		$data['act_date'] =date('m月d日 H:i',$info['act_datetime']);
		$data['act_body'] =$info['act_body'];
		$data['act_sdate'] =date('Y年m月d日',$info['act_stime']);
		$data['act_edate'] =date('m月d日',$info['act_etime']);
		$data['gc_name'] ='';
		$gcinfo = Model('plat_act_class')->getActClassInfo(array('gc_id'=>$info['gc_id']));
		if(!empty($gcinfo)){
			$data['gc_name'] = $gcinfo['gc_name'];
		}
		$list = Model()->table('plat_act_store')->where(array('act_id'=>$info['act_id']))->select();
		$storelist = array();
		if(!empty($list)&&is_array($list)){
			$storeIds= array();
			foreach($list as $v){
				$storeIds[] = $v['store_id'];
			}
			if(!empty($storeIds)){
				$storelist_ = Model('store')->getStoreList(array('store_id'=>array('in',$storeIds)));
			}
			if(!empty($storelist_)){
				foreach($storelist_ as $store){
					$data1 = array();
					$data1['store_id'] = $store['store_id'];
					$data1['store_name'] = $store['store_name'];
					$data1['store_avatar'] = getStoreLogo($store['store_avatar']);
					$storelist[] = $data1;
				}
			}
		}
		
        $sublist_ = $model_activity->getList(array('parent_id'=>$info['act_id']));
		$sublist = array();
		if(!empty($sublist_)){
			foreach($sublist_ as $v){
				$data2= array();
				$data2['act_id']= $v['act_id'];
				$data2['act_name']= $v['act_name'];
				$data2['act_image'] =UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$v['act_image'];
				$sublist[] = $data2;
			}
		}
		
		output_data(array('act_detail'=>$data,'storelist'=>$storelist,'sublist'=>$sublist));
		
	}
	
	/**
	 * 详细
	 */
	public function act_subdetailWt() {
		$pid = intval($_POST['id']);
		if (!$pid){
			 output_error('参数错误!');
		}
		$model_activity = Model('plat_act');
		//查询兑换礼品详细
		$info = $model_activity->getOneById($pid);
		if (empty($info)){
			 output_error('参数错误!!');
			
		}		
		
		//更新礼品浏览次数
		$tm_tm_visite_pgoods = cookie('tm_visite_plat_act');
		$tm_tm_visite_pgoods = $tm_tm_visite_pgoods?explode(',', $tm_tm_visite_pgoods):array();
		if (!in_array($pid, $tm_tm_visite_pgoods)){//如果已经浏览过该服务则不重复累计浏览次数 
		    $result = $model_activity->editActViewnum($pid);
 		    if ($result['state'] == true){//累加成功则cookie中增加该服务ID
		        $tm_tm_visite_pgoods[] = $pid;
		        setWtCookie('tm_visite_plat_act',implode(',', $tm_tm_visite_pgoods));
		    }
		}
		$data = array();
		$data['act_id'] =$info['act_id'];
		$data['act_name'] =$info['act_name'];
		$data['act_image'] =UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$info['act_image'];
		$data['act_price'] =floatval($info['act_price']);
		$data['act_points'] =$info['act_points'];
		$data['act_storage'] =$info['act_storage'];
		$data['act_salenum'] =$info['act_salenum'];
		$data['act_state'] =$info['act_state'];
		$data['jh_area_info'] =$info['jh_area_info'];
		$data['jh_address'] =$info['jh_address'];
		$data['act_area_info'] =$info['act_area_info'];
		$data['act_address'] =$info['act_address'];
		$data['act_date'] =date('m月d日 H:i',$info['act_datetime']);
		$data['act_body'] =$info['act_body'];
		$data['act_sdate'] =date('Y年m月d日',$info['act_stime']);
		$data['act_edate'] =date('m月d日',$info['act_etime']);
		$data['gc_name'] ='';
		
		$info2 = $model_activity->getOneById($info['parent_id']);;
		$data['p_act_name'] =$info2['act_name'];
		
		output_data(array('act_detail'=>$data));
		
	}
	
	/**
	 * 报名活动
	 */
	public function act_joinWt() {
		$pid = intval($_POST['id']);
		if (!$pid){
			 output_error('参数错误!');
		}
		// 如果已登录
        if ($memberId = $this->getMemberIdIfExists()) {
            $memberinfo = Model('member')->getMemberInfoByID($memberId);
			$member_info = array();
			;
			if($memberinfo['is_realverify']==1){
				$rv = Model('member_realverify')->getInfo(array('member_id'=>$memberId,'state'=>1));
				if(!empty($rv)){
					$member_idcard = $rv['cardid'];
					/* $priv = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 2).'0000'),'area_name');
					$city = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 4).'00'),'area_name');
					$area = Model('area')->getAreaInfo(array('adcode'=>substr($member_idcard, 0, 6).''),'area_name'); */
					$sexint = (int)substr($member_idcard,16,1);
					
					//$data = array();
					//$data['truename'] = $rv['truename'];
					//$data['truename'] = $this->_PassStart($rv['truename'],1,5);
					//$data['cardid'] = encryptShow($rv['cardid'],5,11);;
					//$data['area'] = $priv['area_name'].'  '.$city['area_name'].'  '.$area['area_name'];
					//$data['birthday'] = substr($member_idcard, 6, 4).'-'.substr($member_idcard, 10, 2).'-'.substr($member_idcard, 12, 2);
					//$data['member_sex'] = ($sexint % 2 === 0) ? '女' : '男';
					$member_info['member_truename'] = $this->_PassStart($rv['truename'],1,5);
					$member_info['member_id'] = $memberinfo['member_id'];
					$member_info['member_name'] = $memberinfo['member_name'];
					$member_info['member_phone'] = $memberinfo['member_mobile'];
					$member_info['member_sex'] = ($sexint % 2 === 0) ? '女' : '男';
				}else{
					output_error('请先实名验证');
				}
			}else{
					output_error('请先实名验证!');
				}
            
        } else {
             output_error('请先登陆');
        }
		$model_activity = Model('plat_act');
		//查询兑换礼品详细
		$info = $model_activity->getOneById($pid);
		if (empty($info)){
			 output_error('参数错误!!');
			
		}		
		
		$data = array();
		$data['act_id'] =$info['act_id'];
		$data['act_name'] =$info['act_name'];
		$data['act_image'] =UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$info['act_image'];
		$data['act_price'] =floatval($info['act_price']);
		$data['act_points'] =$info['act_points'];
		$data['act_storage'] =$info['act_storage'];
		$data['act_salenum'] =$info['act_salenum'];
		$data['act_state'] =$info['act_state'];
		$data['jh_area_info'] =$info['jh_area_info'];
		$data['jh_address'] =$info['jh_address'];
		$data['act_area_info'] =$info['act_area_info'];
		$data['act_address'] =$info['act_address'];
		$data['act_date'] =date('m月d日 H:i',$info['act_datetime']);
		$data['act_body'] =$info['act_body'];
		$data['act_sdate'] =date('Y年m月d日',$info['act_stime']);
		$data['act_edate'] =date('m月d日',$info['act_etime']);
		
		
		output_data(array('act_detail'=>$data,'member_info'=>$member_info));
		
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
	
	/**
	 * 报名子活动
	 */
	public function act_subjoinWt() {
		$pid = intval($_POST['id']);
		if (!$pid){
			 output_error('参数错误!');
		}
		// 如果已登录
        if ($memberId = $this->getMemberIdIfExists()) {
            $memberinfo = Model('member')->getMemberInfoByID($memberId);
			$member_info = array();
			;
			if($memberinfo['is_realverify']==1){
				$rv = Model('member_realverify')->getInfo(array('member_id'=>$memberId,'state'=>1));
				if(!empty($rv)){
					$member_idcard = $rv['cardid'];
					$sexint = (int)substr($member_idcard,16,1);
					
					$member_info['member_truename'] = $this->_PassStart($rv['truename'],1,5);
					$member_info['member_id'] = $memberinfo['member_id'];
					$member_info['member_name'] = $memberinfo['member_name'];
					$member_info['member_phone'] = $memberinfo['member_mobile'];
					$member_info['member_sex'] = ($sexint % 2 === 0) ? '女' : '男';
				}else{
					output_error('请先实名验证');
				}
			}else{
				output_error('请先实名验证!');
			}
            
        } else {
             output_error('请先登陆');
        }
		$model_activity = Model('plat_act');
		//查询兑换礼品详细
		$info = $model_activity->getOneById($pid);
		if (empty($info)){
			 output_error('参数错误!!');
			
		}		
		
		$data = array();
		$data['act_id'] =$info['act_id'];
		$data['act_name'] =$info['act_name'];
		$data['act_image'] =UPLOAD_SITE_URL."/".ATTACH_ACTIVITY."/".$info['act_image'];
		$data['act_price'] =floatval($info['act_price']);
		$data['act_points'] =$info['act_points'];
		$data['act_storage'] =$info['act_storage'];
		$data['act_salenum'] =$info['act_salenum'];
		$data['act_state'] =$info['act_state'];
		$data['jh_area_info'] =$info['jh_area_info'];
		$data['jh_address'] =$info['jh_address'];
		$data['act_area_info'] =$info['act_area_info'];
		$data['act_address'] =$info['act_address'];
		$data['act_date'] =date('m月d日 H:i',$info['act_datetime']);
		$data['act_body'] =$info['act_body'];
		$data['act_sdate'] =date('Y年m月d日',$info['act_stime']);
		$data['act_edate'] =date('m月d日',$info['act_etime']);
		
		$info2 = $model_activity->getOneById($info['parent_id']);;
		$data['p_act_name'] =$info2['act_name'];
		
		output_data(array('act_detail'=>$data,'member_info'=>$member_info));
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getmemberinfoWt(){
		
		// 如果已登录
        if ($memberId = $this->getMemberIdIfExists()) {
            $memberinfo = Model('member')->getMemberInfoByID($memberId);
			$info = array();
			$info['member_points'] = $memberinfo['member_points'];
			$info['member_id'] = $memberinfo['member_id'];
			$info['member_name'] = $memberinfo['member_name'];
             output_data(array('member'=>$info));
        } else {
             output_error('0');
        }
	}	
	
	
}
