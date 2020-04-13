<?php
/**
 * 实名验证模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class member_realverifyModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加
     *
     * @param
     * @return int
     */
    public function add($array) {
        $id = $this->table('member_realverify')->insert($array);
        return $id;
    }

    /**
     * 删除
     *
     * @param
     * @return bool
     */
    public function del($condition) {
        if (empty($condition)) {
            return false;
        } else {
            $result = $this->table('member_realverify')->where($condition)->delete();
            return $result;
        }
    }

    /**
     * 修改
     *
     * @param
     * @return bool
     */
    public function edit($condition, $data) {
        if (empty($condition)) {
            return false;
        }
        if (is_array($data)) {
            $result = $this->table('member_realverify')->where($condition)->update($data);
            return $result;
        } else {
            return false;
        }
    }
	
	public function getInfo($condition, $field = '*') {
        return $this->table('member_realverify')->field($field)->where($condition)->find();
    }

    /**
     * 列表
     *
     * @param
     * @return array
     */
    public function getList($condition = array(), $page = '', $limit = '', $order = 'id desc') {
        $result = $this->table('member_realverify')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }
	
	public function getCount($condition) {
        return  $this->table('member_realverify')->where($condition)->count();
    }
	
}
