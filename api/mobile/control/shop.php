<?php
/**
 * 所有店铺街
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');



class shopControl extends mobileHomeControl {

    public function __construct(){
        parent::__construct();
    }


    /*
     * 首页显示
     */
    public function indexWt(){
        $this->shop_listWt();

    }
	
	/*
     * 首页显示
     */
	public function shop_listWt(){
		//店铺搜索
		$model = Model();
		$condition = array();
		$scid1 = intval($_POST['cateid']);
		$city_id = intval($_POST['city_id']);
		$keyword = trim($_GET['keyword']);
		if(C('fullindexer.open') && !empty($keyword)){
			//全文搜索
			$condition = $this->full_search($keyword);
		} else{
			if ($keyword != ''){
				$condition['store_name|store_zy'] = array('like','%'.$keyword.'%');
			}
			if ($_GET['user_name'] != ''){
				$condition['member_name'] = trim($_GET['user_name']);
			}
		}
		
		if (!empty($_GET['area_info'])){
			$condition['area_info'] = array('like','%'.$_GET['area_info'].'%');
		}
		if ($_GET['sc_id'] > 0){
			$child = array_merge((array)$class_list[$_GET['sc_id']]['child'],array($_GET['sc_id']));
			$condition['sc_id'] = array('in',$child);
		}
		$condition['store_state'] = 1;
		$condition['isshow'] = 1;
		if (!in_array($_GET['order'],array('desc','asc'))){
			unset($_GET['order']);
		}
		if (!in_array($_GET['key'],array('store_sales','store_credit'))){
			unset($_GET['key']);
		}
		$order = 'store_sort asc';
		if (isset($condition['store.store_id'])){
			$condition['store_id'] = $condition['store.store_id'];
			unset($condition['store.store_id']);
		}
		if($scid1>0){
			$condition['scid1'] = $scid1;
		}
		if($city_id>0){
			$condition['city_id'] = $city_id;
		}
		$lngx=$_POST['lng'];//按层级关系提取经度数据
		$laty=$_POST['lat'];//按层级关系提取纬度数据
		//echo 'lng:'.$lngx.'lat'.$laty;
		$model_store = Model('store');
		$fields ='store_id,store_name,store_avatar,map_lat,map_lng,consume_num,map_address';
		$store_list = $model_store->where($condition)->field($fields)->order($order)->page(10)->select();
		$page_count = $model_store->gettotalpage();
		//获取店铺商品数，推荐商品列表等信息
		$store_list = $model_store->getStoreSearchList($store_list,4);
		foreach ($store_list as &$val) {
			$scores= Model('evaluate_store')->getEvaluateStoreInfoByStoreID($val['store_id']);
			$val['scores'] =$scores['seval_scores'];
			/**
             * 店铺等级
             */
			/* if($val['grade_id']==0){
				$val['grade_id'] ='自营';
			} else{
				$grade_class = Model('store_grade');
				$grade = $grade_class->getOneGrade($val['grade_id']);
				$val['grade_id'] =$grade['sg_name'];
			} */
			$val['store_avatar'] = getStoreLogo($val['store_avatar']);
			foreach ($val['search_list_goods'] as &$vals) {
				$vals['goods_image']= thumb($vals,'small');
			}
			
			if(!empty($val['map_lng']) || !empty($val['map_lat'])){
				
				$zbjl = $this->GetDistance($val['map_lat'], $val['map_lng'], $laty, $lngx);
				//计算坐标距离
				$val['distance'] = number_format($zbjl, 2, '.', '');
			}else{
				$val['distance'] = '--';
			}
			$val['voucher'] = '';
			$val['voucher2'] = '';
			//代金券
			$vcondition = array();
			$vcondition['voucher_t_end_date'] = array('gt',TIMESTAMP);
			$vcondition['voucher_t_state'] = 1;
			$vcondition['voucher_t_store_id'] = $val['store_id'];
			
			$vlist = Model('voucher')->getVoucherTemplateList($vcondition,'*',2);
			$_vlist= array();
			if(!empty($vlist)&& is_array($vlist)){
				foreach($vlist as $key=>$vv){
					$v=array();
					$v['mtitle']=$vv['voucher_t_title'].'  满'.floatval($vv['voucher_t_limit']).'减'.floatval($vv['voucher_t_price']);
					$_vlist[]=$v;
				}
			}
			//$voucher =Model('voucher')->getCurrentAvailableVoucherInfo_index(array('voucher_store_id'=>$val['store_id']));
			//echo json_encode($voucher).'================'.$val['store_id'].'=======================';
			if(!empty($_vlist)&&is_array($_vlist)){
				if(count($_vlist)>0){
					$val['voucher'] = $_vlist[0]['mtitle'];
				}
				if(count($_vlist)>1){
					$val['voucher2'] = $_vlist[1]['mtitle'];
				}
			}
			
		}
		
		//print_r($store_list);exit();
		//信用度排序
		/* if($_GET['key'] == 'store_credit') {
			if($_GET['order'] == 'desc') {
				$store_list = sortClass::sortArrayDesc($store_list, 'store_credit_average');
			} else {
				$store_list = sortClass::sortArrayAsc($store_list, 'store_credit_average');
			}
		} else if($_GET['key'] == 'store_sales') {
			//销量排行
			if($_GET['order'] == 'desc') {
				$store_list = sortClass::sortArrayDesc($store_list, 'num_sales_jq');
			} else {
				$store_list = sortClass::sortArrayAsc($store_list, 'num_sales_jq');
			}
		} */
		output_data(array('store_list' => $store_list), mobile_page($page_count));
	}
	
	protected function GetDistance($lat1, $lng1, $lat2, $lng2, $miles = true){   
		
		//将角度转为狐度

		$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度

		$radLat2=deg2rad($lat2);

		$radLng1=deg2rad($lng1);

		$radLng2=deg2rad($lng2);

		$a=$radLat1-$radLat2;

		$b=$radLng1-$radLng2;

		$s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;

		return $s;
		
		/*  $pi80 = M_PI / 180;
		 $lat1 *= $pi80;
		 $lng1 *= $pi80;
		 $lat2 *= $pi80;
		 $lng2 *= $pi80;
		 $r = 6372.797; // mean radius of Earth in km
		 $dlat = $lat2 - $lat1;
		 $dlng = $lng2 - $lng1;
		 $a = sin($dlat/2)*sin($dlat/2)+cos($lat1)*cos($lat2)*sin($dlng/2)*sin($dlng/2);
		 $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		 $km = $r * $c;
		 return ($miles ? ($km * 0.621371192) : $km);   */
	} 
	
	

    private  function  _get_Own_Store_List(){
		$model_store = Model('store');
        //查询条件
        $condition = array();
		$condition['store_state'] = 1;//店铺状态V6.3
		$keyword = trim($_GET['keyword']);
		if ($keyword != ''){
				$condition['store_name'] = array('like','%'.$keyword.'%');
			}
		if($_GET['area_info']!='undefined' && $_GET['area_info']!='null' ){
					$condition['area_info'] = array('like','%'.$_GET['area_info'].'%');
			}
        if(!empty($_GET['sc_id']) && intval($_GET['sc_id']) > 0) {
            $condition['sc_id'] = $_GET['sc_id'];
        } elseif (!empty($_GET['keyword'])) {
            $condition['store_name'] = array('like', '%' . $_GET['keyword'] . '%');
        }
        //所需字段
        $fields = "*";
        //排序方式
        $order = $this->_store_list_order($_GET['key'], $_GET['order']);
        $store_list = $model_store->where($condition)->order($order)->page(10)->select();
        $page_count = $model_store->gettotalpage();
        $own_store_list = $store_list;
        $simply_store_list = array();

        foreach ($own_store_list as $key => $value) {

            $simply_store_list[$key]['store_id'] = $own_store_list[$key]['store_id'];
            $simply_store_list[$key]['store_name'] = $own_store_list[$key]['store_name'];
			$simply_store_list[$key]['store_collect'] = $own_store_list[$key]['store_collect'];
            $simply_store_list[$key]['store_address'] = $own_store_list[$key]['store_address'];
            $simply_store_list[$key]['store_area_info'] = $own_store_list[$key]['area_info'];
			$simply_store_list[$key]['store_avatar'] = $own_store_list[$key]['store_avatar'];
			$simply_store_list[$key]['goods_count'] = $own_store_list[$key]['goods_count'];
            $simply_store_list[$key]['store_avatar_url'] = UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');
			if($own_store_list[$key]['store_avatar']){
				$simply_store_list[$key]['store_avatar_url'] = UPLOAD_SITE_URL.'/shop/store/'.$own_store_list[$key]['store_avatar'];
			}
			

        }
		
		 output_data(array('store_list' => $simply_store_list), mobile_page($page_count));
       
    }

	 /**
     * 商品列表排序方式
     */
    private function _store_list_order($key, $order) {
        $result = 'store_id desc';
        if (!empty($key)) {

            $sequence = 'desc';
            if($order == 1) {
                $sequence = 'asc';
            }

            switch ($key) {
                //销量
                case '1' :
                    $result = 'store_id' . ' ' . $sequence;
                    break;
                //浏览量
                case '2' :
                    $result = 'store_name' . ' ' . $sequence;
                    break;
                //价格
                case '3' :
                    $result = 'store_name' . ' ' . $sequence;
                    break;
            }
        }
        return $result;
    }

	
		
	 public function ttWt(){
		
		 $lng1= 109.598327;
		 $lat1= 27.948308;
		 $lng2 = 114.129;
		 $lat2 = 22.5496;
         $juli = $this->GetDistance($lat1, $lng1, $lat2, $lng2);
	 
	     echo $juli;

    }
	
	
	
	
	
	
	
}