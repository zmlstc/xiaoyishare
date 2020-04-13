<?php
/**
 * 用户登录日志模型
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class member_login_logModel extends Model
{
    public function __construct()
    {
        parent::__construct('member_login_log');
    }

    /**
     * 删除用户登录日志
     */
    public function deleteOneLog($memberid)
    {
        return $this->where(array('member_id' => $memberid))->delete();
    }
}
