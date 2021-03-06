<?php
/**
 * 活动类别模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class plat_act_classModel extends Model {

    public function __construct(){
        parent::__construct('plat_act_class');
    }

    /**
     * 取类别列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $order
     */
    public function getActClassList($condition = array(), $pagesize = '', $limit = '', $order = 'gc_sort asc,gc_id asc') {
        return $this->where($condition)->order($order)->page($pagesize)->limit($limit)->select();
    }
	
	

    /**
     * 取得单条信息
     * @param unknown $condition
     */
    public function getActClassInfo($condition = array()) {
        return $this->where($condition)->find();
    }

    /**
     * 删除类别
     * @param unknown $condition
     */
    public function delActClass($condition = array()) {
        return $this->where($condition)->delete();
    }

    /**
     * 增加分类
     * @param unknown $data
     * @return boolean
     */
    public function addActClass($data) {
        return $this->insert($data);
    }

    /**
     * 更新分类
     * @param unknown $data
     * @param unknown $condition
     */
    public function editActClass($data = array(),$condition = array()) {
        return $this->where($condition)->update($data);
    }
}
