<?php
/**
 * 设施模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class facilityModel extends Model {

    public function __construct(){
        parent::__construct('facility');
    }

    /**
     * 取设施列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $order
     */
    public function getFacilityList($condition = array(), $pagesize = '', $limit = '', $order = 'f_sort asc,f_id asc') {
        return $this->where($condition)->order($order)->page($pagesize)->limit($limit)->select();
    }
	
	

    /**
     * 取得单条信息
     * @param unknown $condition
     */
    public function getFacilityInfo($condition = array()) {
        return $this->where($condition)->find();
    }

    /**
     * 删除
     * @param unknown $condition
     */
    public function delFacility($condition = array()) {
        return $this->where($condition)->delete();
    }

    /**
     * 增加
     * @param unknown $data
     * @return boolean
     */
    public function addFacility($data) {
        return $this->insert($data);
    }

    /**
     * 更新
     * @param unknown $data
     * @param unknown $condition
     */
    public function editFacility($data = array(),$condition = array()) {
        return $this->where($condition)->update($data);
    }
}
