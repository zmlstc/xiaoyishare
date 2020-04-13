<?php
/**
 * 地区
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class areaControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
    }

    public function indexWt() {
        $this->area_listWt();
    }

    /**
     * 地区列表
     */
    public function area_listWt() {
        $area_id = intval($_GET['area_id']);

        $model_area = Model('area');

        $condition = array();
        if($area_id > 0) {
            $condition['area_parent_id'] = $area_id;
        } else {
            $condition['area_deep'] = 1;
        }
        $area_list = $model_area->getAreaList($condition, 'area_id,area_name');
        output_data(array('area_list' => $area_list));
    }
	
	/**
     * json输出地址数组 原data/resource/js/area_array.js
     */
    public function json_areaWt()
    {
        $_GET['src'] = $_GET['src'] != 'db' ? 'cache' : 'db';
        echo $_GET['callback'].'('.json_encode(Model('area')->getAreaArrayForJson($_GET['src'])).')';
    }
	
	/**
     * 根据ID返回所有父级地区名称
     */
    public function json_area_showWt()
    {
        $area_info['text'] = Model('area')->getTopAreaName(intval($_GET['area_id']));
        echo $_GET['callback'].'('.json_encode($area_info).')';
    }
	
	//获取代理地区
	public function getcitysWt(){
		$list = Model('agent_area')->getAgentCitys();
		$citys = array();
		if(!empty($list)&&is_array($list)){
			$city = array();
			foreach($list as $val){
				$city[$val['firstchar']][] = array('name'=>$val['shortname'],'adcode'=>$val['adcode'],'city_id'=>$val['area_id']);
				//$citys[]= array('letter'=>$val['firstchar'],'list'=>$city);
			}
			ksort($city);
			foreach($city as $k=>$val){
				$citys[] = array('letter'=>$k,'list'=>$val);
			}
		}
		output_data(array('citys' => $citys));
	}
	
	//获取代理地区(商家注册)
	public function getallareaWt(){
		$list = Model('agent_area')->getAllDataList();
		
		$citys = array();
		if(!empty($list)&&is_array($list)){
			$city = array();
			foreach($list as $val){
				$city[$val['firstchar']][] = array('name'=>$val['shortname'],'adcode'=>$val['adcode'],'city_id'=>$val['area_id']);
				//$citys[]= array('letter'=>$val['firstchar'],'list'=>$city);
			}
			ksort($city);
			foreach($city as $k=>$val){
				$citys[] = array('letter'=>$k,'list'=>$val);
			}
		}
		output_data($list);
	}
	
	//获取地区信息
	public function getareainfoWt(){
		$adcode = trim($_POST['adcode']);
		$where = array();
		$where['adcode'] = $adcode;
		$areainfo = Model('area')->getAreaInfo($where,'area_id,area_name,shortname,area_parent_id');
		
		if(!empty($areainfo)&&is_array($areainfo)){
			$areainfo = Model('area')->getAreaInfo(array('area_id'=>$areainfo['area_parent_id']),'area_id,area_name,shortname');
			$agentInfo = Model('agent_area')->getAgentAreaInfo(array('city_id'=>$areainfo['area_id']));
			$has_agent = 0;
			if(!empty($agentInfo)){
				$has_agent = 1;
			}
			output_data(array('areainfo' => $areainfo,'has_agent'=>$has_agent));
		}else{
			output_error('获取地区信息失败');
		}
	}
	
	
	public function upWt(){
		Model('area')->getAllAreaFirstCharter();
		echo time();
	}

}
