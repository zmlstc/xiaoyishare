<?php
/**
 * 店铺消息接收设置模型
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class store_msg_settingModel extends Model{
    public function __construct() {
        parent::__construct('store_msg_setting');
    }

    /**
     * 店铺消息接收设置列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getStoreMsgSettingList($condition, $field = '*', $key = '', $page = 0, $order = 'smt_code asc') {
        return $this->field($field)->where($condition)->key($key)->order($order)->page($page)->select();
    }

    /**
     * 店铺消息接收设置详细
     * @param array $condition
     * @param string $field
     */
    public function getStoreMsgSettingInfo($condition, $field = '*') {
        return $this->field($field)->where($condition)->find();
    }

    /**
     * 编辑店铺模板接收设置
     * @param array $insert
     */
    public function addStoreMsgSetting($insert) {
        return $this->insert($insert, true);
    }
}
