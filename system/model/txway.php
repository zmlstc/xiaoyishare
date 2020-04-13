<?php
/**
 * 提现
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class txwayModel extends Model {



    /**
     * 添加记录
     * @param array $data
     */
    public function add($data) {
        return $this->table('txway')->insert($data);
    }

    /**
     * 编辑
     * @param unknown $data
     * @param unknown $condition
     */
    public function edit($data,$condition = array()) {
        return $this->table('txway')->where($condition)->update($data);
    }

    /**
     * 取得单条信息
     * @param unknown $condition
     * @param string $fields
     */
    public function getInfo($condition = array(), $fields = '*',$lock = false) {
        return $this->table('txway')->where($condition)->field($fields)->lock($lock)->find();
    }


    /**
     * 取总数
     * @param unknown $condition
     */
    public function getCount($condition = array()) {
        return $this->table('txway')->where($condition)->count();
    }

    /**
     * 取得列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $fields
     * @param string $order
     */
    public function getList($condition = array(), $pagesize = '', $fields = '*', $order = '', $limit = '') {
        return $this->table('txway')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /**
     * 删除
     * @param unknown $condition
     */
    public function del($condition) {
        return $this->table('txway')->where($condition)->delete();
    }

   
}
