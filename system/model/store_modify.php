<?php
/**
 * 店铺名称修改记录
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class store_modifyModel extends Model {

    public function __construct(){
        parent::__construct('store_modify');
    }

    /**
     * 取店铺类别列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $order
     */
    public function getList($condition = array(), $pagesize = '', $limit = '', $order = 'id desc') {
        return $this->where($condition)->order($order)->page($pagesize)->limit($limit)->select();
    }
	//获得当年已修改次数
	public function getYearNum($store_id) {
		$year=strtotime(date('Y-01-01 00:00:01'));
		$condition = array();
		$condition['store_id'] = $store_id;
		$condition['addtime'] = array('egt',$year);
        $info=$this->where($condition)->order(' id desc')->find();
		if(empty($info)||!is_array($info)){
			return 0;
		}else{
			return intval($info['curr_num']);
		}
    }

    /**
     * 取得单条信息
     * @param unknown $condition
     */
    public function getInfo($condition = array()) {
        return $this->where($condition)->find();
    }

    /**
     * 删除类别
     * @param unknown $condition
     */
    public function del($condition = array()) {
        return $this->where($condition)->delete();
    }

    /**
     * 增加店铺分类
     * @param unknown $data
     * @return boolean
     */
    public function add($data) {
        return $this->insert($data);
    }

    /**
     * 更新分类
     * @param unknown $data
     * @param unknown $condition
     */
    public function edit($data = array(),$condition = array()) {
        return $this->where($condition)->update($data);
    }
	
	
	public function getCount($condition) {
        return  $this->where($condition)->count();
    }
	
}
