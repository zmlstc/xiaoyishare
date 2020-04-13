<?php
/**
 * 地区模型
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class areaModel extends Model {

    public function __construct() {
        parent::__construct('area');
    }

    /**
     * 获取地址列表
     *
     * @return mixed
     */
    public function getAreaList($condition = array(), $fields = '*', $group = '', $page = null) {
        return $this->where($condition)->field($fields)->page($page)->limit(false)->group($group)->select();
    }

	/**
	 * 从缓存获取分类 通过分类id v1.0
	 *
	 * @param int $id 分类id
	 */
	public function getAreaInfoById($id) {
		$data = $this->getCache();
		return $data['name'][$id];
	}
	
	/**
     * 获取地址详情
     *
     * @return mixed
     */
    public function getAreaInfo($condition = array(), $fileds = '*') {
        return $this->where($condition)->field($fileds)->find();
    }
	


    /**
     * 获取一级地址（省级）名称数组
     *
     * @return array 键为id 值为名称字符串
     */
    public function getTopLevelAreas() {
        $data = $this->getCache();

        $arr = array();
        foreach ($data['children'][0] as $i) {
            $arr[$i] = $data['name'][$i];
        }

        return $arr;
    }

    /**
     * 获取获取市级id对应省级id的数组
     *
     * @return array 键为市级id 值为省级id
     */
    public function getCityProvince() {
        $data = $this->getCache();

        $arr = array();
        foreach ($data['parent'] as $k => $v) {
            if ($v && $data['parent'][$v] == 0) {
                $arr[$k] = $v;
            }
        }

        return $arr;
    }

    /**
     * 获取地区缓存
     *
     * @return array
     */
    public function getAreas() {
        return $this->getCache();
    }

    /**
     * 获取全部地区名称数组
     *
     * @return array 键为id 值为名称字符串
     */
    public function getAreaNames() {
        $data = $this->getCache();

        return $data['name'];
    }

    /**
     * 获取用于前端js使用的全部地址数组
     *
     * @return array
     */
    public function getAreaArrayForJson($src = 'cache') {
        if ($src == 'cache') {
            $data = $this->getCache();
        } else {
            $data = $this->_getAllArea();
        }

        $arr = array();
        foreach ($data['children'] as $k => $v) {
            foreach ($v as $vv) {
                $arr[$k][] = array($vv, $data['name'][$vv]);
            }
        }
        return $arr;
    }
	
	 /**
     * 根据父类ID获取下级列表用于前端js使用的地址数组
     *
     * @return array
     */
    public function getAreaArrayForJsonByParentId($parent_id=0,$qstr='',$src = 'cache') {
        if ($src == 'cache') {
            $data = $this->getCache();
        } else {
            $data = $this->_getAllArea();
        }
		
        $arr = array();
        foreach ($data['children'][$parent_id] as $k => $v) { //echo json_encode($data['children'][$parent_id]);
			$url = replaceParamAndStr(array('area_id' => $v),$qstr);
				$arr[] = array($v, $data['name'][$v],$url);											 
            //foreach ($v as $vv) {
            //    $arr[$k][] = array($vv, $data['name'][$vv]);
            //}
        }
        return $arr;
    }

    /**
     * 获取地区数组 格式如下
     * array(
     *   'name' => array(
     *     '地区id' => '地区名称',
     *     // ..
     *   ),
     *   'parent' => array(
     *     '子地区id' => '父地区id',
     *     // ..
     *   ),
     *   'children' => array(
     *     '父地区id' => array(
     *       '子地区id 1',
     *       '子地区id 2',
     *       // ..
     *     ),
     *     // ..
     *   ),
     *   'region' => array(array(
     *     '华北区' => array(
     *       '省级id 1',
     *       '省级id 2',
     *       // ..
     *     ),
     *     // ..
     *   ),
     * )
     *
     * @return array
     */
    protected function getCache() {
        // 对象属性中有数据则返回
        if ($this->cachedData !== null)
            return $this->cachedData;

        // 缓存中有数据则返回
        if ($data = rkcache('area')) {
            $this->cachedData = $data;
            return $data;
        }

        // 查库
        $data = $this->_getAllArea();
        wkcache('area', $data);
        $this->cachedData = $data;

        return $data;
    }

    protected $cachedData;

    private function _getAllArea() {
        $data = array();
        $area_all_array = $this->limit(false)->select();
        foreach ((array) $area_all_array as $a) {
            $data['name'][$a['area_id']] = $a['area_name'];
            $data['parent'][$a['area_id']] = $a['area_parent_id'];
            $data['children'][$a['area_parent_id']][] = $a['area_id'];
        
            if ($a['area_deep'] == 1 && $a['area_region'])
                $data['region'][$a['area_region']][] = $a['area_id'];
        }
        return $data;
    }

    public function addArea($data = array()) {
        return $this->insert($data);
    }

    public function editArea($data = array(), $condition = array()) {
        return $this->where($condition)->update($data);
    }

    public function delArea($condition = array()) {
        return $this->where($condition)->delete();
    }

    /**
     * 递归取得本地区及所有上级地区名称
     * @return string
     */
    public function getTopAreaName($area_id,$area_name = '') {
        $info_parent = $this->getAreaInfo(array('area_id'=>$area_id),'area_name,area_parent_id');
        if ($info_parent) {
            return $this->getTopAreaName($info_parent['area_parent_id'],$info_parent['area_name']).' '.$info_parent['area_name'];
        }
    }

    /**
     * 递归取得本地区所有孩子ID
     * @return array
     */
    public function getChildrenIDs($area_id) {
        $result = array();
        $list = $this->getAreaList(array('area_parent_id'=>$area_id),'area_id');
        if ($list) {
            foreach ($list as $v) {
                $result[] = $v['area_id'];
                $result = array_merge($result,$this->getChildrenIDs($v['area_id']));
            }
        }
        return $result;
    }
	
	/*********生成小程序用的省市区********/
	public function getAllAreaForJS() {
        $province = array();
		$city = array();
		$area = array();
        $_province = $this->where(array('area_parent_id'=>0))->limit(false)->select();
        foreach ((array) $_province as $p) {
			$province[] =array('name'=>$p['area_name'],'value'=>$p['area_id']);
			$_city = $this->where(array('area_parent_id'=>$p['area_id']))->limit(false)->select();
			$city_array = array();
			$area_2 = array();
			foreach((array)$_city as $c){
				$city_array[] =array('name'=>$c['area_name'],'value'=>$c['area_id']);
				$_area = $this->where(array('area_parent_id'=>$c['area_id']))->limit(false)->select();
				$area_array = array();
				foreach((array)$_area as $a){
					$area_array[] = array('name'=>$a['area_name'],'value'=>$a['area_id']);
				}
				$area_2[] = $area_array;
			}
			$area[] = $area_2;
            $city[] = $city_array;
        }
		wkcache('province', json_encode($province, JSON_UNESCAPED_UNICODE));
		wkcache('city', json_encode($city, JSON_UNESCAPED_UNICODE));
		wkcache('area_', json_encode($area, JSON_UNESCAPED_UNICODE));
        //return $data;
    }
	
	/*********生成首字母********/
	public function getAllAreaFirstCharter() {
        $province = array();
		$city = array();
		$area = array();
        $_province = $this->where(array())->limit(false)->select();
        foreach ((array) $_province as $p) {
			$firstChar = $this->Getzimu($p['area_name']);
			$this->where(array('area_id'=>$p['area_id']))->update(array('firstchar'=>$firstChar));
			
        }
		/* wkcache('province', json_encode($province, JSON_UNESCAPED_UNICODE));
		wkcache('city', json_encode($city, JSON_UNESCAPED_UNICODE));
		wkcache('area_', json_encode($area, JSON_UNESCAPED_UNICODE)); */
        //return $data;
    }
	
	private function Getzimu($str)
	{
		if(empty($str)){return '';}

		$fchar=ord($str{0});

		if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});

		$s1=iconv('UTF-8','gb2312',$str);

		$s2=iconv('gb2312','UTF-8',$s1);

		$s=$s2==$str?$s1:$str;

		$asc=ord($s{0})*256+ord($s{1})-65536;

		if($asc>=-20319&&$asc<=-20284) return 'A';

		if($asc>=-20283&&$asc<=-19776) return 'B';

		if($asc>=-19775&&$asc<=-19219) return 'C';

		if($asc>=-19218&&$asc<=-18711) return 'D';

		if($asc>=-18710&&$asc<=-18527) return 'E';

		if($asc>=-18526&&$asc<=-18240) return 'F';

		if($asc>=-18239&&$asc<=-17923) return 'G';

		if($asc>=-17922&&$asc<=-17418) return 'H';

		if($asc>=-17417&&$asc<=-16475) return 'J';

		if($asc>=-16474&&$asc<=-16213) return 'K';

		if($asc>=-16212&&$asc<=-15641) return 'L';

		if($asc>=-15640&&$asc<=-15166) return 'M';

		if($asc>=-15165&&$asc<=-14923) return 'N';

		if($asc>=-14922&&$asc<=-14915) return 'O';

		if($asc>=-14914&&$asc<=-14631) return 'P';

		if($asc>=-14630&&$asc<=-14150) return 'Q';

		if($asc>=-14149&&$asc<=-14091) return 'R';

		if($asc>=-14090&&$asc<=-13319) return 'S';

		if($asc>=-13318&&$asc<=-12839) return 'T';

		if($asc>=-12838&&$asc<=-12557) return 'W';

		if($asc>=-12556&&$asc<=-11848) return 'X';

		if($asc>=-11847&&$asc<=-11056) return 'Y';

		if($asc>=-11055&&$asc<=-10247) return 'Z';

		return "#";
	}
	
	

}
