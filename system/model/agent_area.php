<?php
/**
 * 代理商区域模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class agent_areaModel extends Model {

    protected $cachedData;
    public function __construct(){
        parent::__construct('agent_area');
    }

    /**
     * 详细信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getAgentAreaInfo($condition, $field = '*', $master = false) {
        return $this->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 取得详细信息
     * @param int $agent_id
     * @param string $field 
     * @return array
     */
    public function getAgentAreaInfoByID($a_id, $fields = '*') {
       
        $member_info = $this->getAgentAreaInfo(array('a_id'=>$a_id),'*',true);
        
        return $member_info;
    }

    /**
     * 列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getAgentAreaList($condition = array(), $field = '*', $page = null, $order = 'a_id desc', $limit = '') {
       return $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }
		
	/**
	 * 删除
	 *
	 * @param int $id 记录ID
	 * @return array $rs_row 返回数组形式的查询结果
	 */
	public function del($id){
		if (intval($id) > 0){
			$where = " a_id = '". intval($id) ."'";
			$result = Db::delete('agent_area',$where);
			return $result;
		}else {
			return false;
		}
	}
	
	 /**
     * 编辑信息
     * @param array $condition
     * @param array $data
     */
    public function editAgentArea($condition, $data) {
        $update = $this->where($condition)->update($data);
       
        return $update;
    }
	
	/**
     * 添加
     *
     * @param   array $param 信息
     * @return  array 数组格式的返回结果
     */
    public function addAgentArea($param) {
        if(empty($param)) {
            return false;
        }
        
		$insert_id  = $this->table('agent_area')->insert($param);
		
		return $insert_id;
        
    }
    /**
     * 联查地区表代理区域表(已代理的地区)
     *
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     * @return array
     */
    public function getAgentCitys() {
		  // 对象属性中有数据则返回
     /*    if ($this->cachedData !== null)
            return $this->cachedData;

        // 缓存中有数据则返回
        if ($agent_area = rkcache('agent_area_citys')) {
            $this->cachedData = $agent_area;
            return $agent_area;
        } */
		$arealist = array();
		$all_list = array();
		$all_data = array();
		$citys = array();
		
        $list=$this->table('area,agent_area')->join('inner')->on('agent_area.city_id=area.area_id')->field('area.*')->limit(false)->select();
		/* if(!empty($list)&& is_array($list)){
			foreach($list as $area){
				unset($area['adcode_parent_id']);
				unset($area['citycode']);
				unset($area['area_sort']);
				unset($area['area_region']);
				
				$all_list[$area['area_id']] = $area;
				$all_data[$area['parent_id']][] = $area;
				 if($area['deep']==2){
					$citys[$area['area_id']] = $area;
				} 
			}

		} */
		$agent_area = array('provinces'=>$all_data[0],'citys'=>$citys,'alldata'=>$arealist);
		/* wkcache('agent_area', $agent_area);
        $this->cachedData = $agent_area; */
		return $list;
    }
	
		
    /**
     * 联查地区表代理区域表(已代理的地区)
     *
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     * @return array
     */
    public function getAllDataList() {
		  // 对象属性中有数据则返回
     /*    if ($this->cachedData !== null)
            return $this->cachedData;

        // 缓存中有数据则返回
        if ($agent_area = rkcache('agent_area')) {
            $this->cachedData = $agent_area;
            return $agent_area;
        } */
		$province = array();
		$city = array();
		$area = array();
		
		$provice_list=$this->table('area,agent_area')->join('inner')->on('agent_area.province_id=area.area_id')->field('area.shortname,area.area_id')->group('province_id')->limit(false)->select();
		
        foreach ((array) $provice_list as $p) {
			$province[] =array('name'=>$p['shortname'],'value'=>$p['area_id']);
			$_city=$this->table('area,agent_area')->join('inner')->on('agent_area.city_id=area.area_id')->where(array('area_parent_id'=>$p['area_id']))->field('area.shortname,area.area_id')->group('city_id')->limit(false)->select();
		
			$city_array = array();
			$area_2 = array();
			foreach((array)$_city as $c){
				$city_array[] =array('name'=>$c['shortname'],'value'=>$c['area_id']);
				$_area=$this->table('area,agent_area')->join('inner')->on('agent_area.area_id=area.area_id')->where(array('area_parent_id'=>$c['area_id']))->field('area.shortname,area.area_id')->group('area_id')->limit(false)->select();
				$area_array = array();
				foreach((array)$_area as $a){
					$area_array[] = array('name'=>$a['shortname'],'value'=>$a['area_id']);
				}
				$area_2[] = $area_array;
			}
			$area[] = $area_2;
            $city[] = $city_array;
        }
		return array('province'=>$province,'city'=>$city,'area'=>$area);
        
    }
	
	    /**
     * 联查地区表代理区域表(已代理的地区)
     *
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     * @return array
     */
    public function getAllDataList_bak() {
		  // 对象属性中有数据则返回
     /*    if ($this->cachedData !== null)
            return $this->cachedData;

        // 缓存中有数据则返回
        if ($agent_area = rkcache('agent_area')) {
            $this->cachedData = $agent_area;
            return $agent_area;
        } */
		$arealist = array();
		$all_list = array();
		$all_data = array();
		$citys = array();
		
        $list=$this->table('area,agent_area')->join('inner')->on('agent_area.area_id=area.area_id')->field('area.*')->limit(false)->select();
		if(!empty($list)&& is_array($list)){
			foreach($list as $area){
				unset($area['adcode_parent_id']);
				unset($area['citycode']);
				unset($area['area_sort']);
				unset($area['area_region']);
				
				$all_list[$area['area_id']] = $area;
				$all_data[$area['parent_id']][] = $area;
				 if($area['deep']==2){
					$citys[$area['area_id']] = $area;
				} 
			}
			
			if(!empty($all_data)&&is_array($all_data)){
				foreach($all_data as $val){
					//$data = array();
					//$data = $val;
					$_v = array();
					if(!empty($all_data[$val['area_id']])&& is_array($all_data[$val['area_id']])){
						foreach($all_data[$val['area_id']] as $v){
							$v['child'] = $all_data[$v['area_id']];
							$_v[] = $v;
						}
					}
					$val['child']= $_v;
					//$data = $val;
					$arealist[] = $val;
				}
			}
		}
		$agent_area = array('provinces'=>$all_data[0],'citys'=>$citys,'alldata'=>$arealist);
		wkcache('agent_area', $agent_area);
        $this->cachedData = $agent_area;
		return $agent_area;
    }
	
	
	
}
