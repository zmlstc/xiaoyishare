<?php
/**
 * 微信通知日志
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class wx_logModel extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function addWx($log_array)
	{
		$log_array['msg_type'] = 3;
		$log_array['log_type'] = 2;
		$log_array['add_time'] = TIMESTAMP;
		$log_id = $this->table('realtime_log')->insert($log_array);
		return $log_id;
	}

	public function getWxInfo($condition)
	{
		if (empty($condition)) {
			return false;
		}

		$result = $this->table('realtime_log')->where($condition)->order('log_id desc')->find();
		return $result;
	}

	public function getWxList($condition = array(), $page = '', $limit = '', $order = 'log_id desc')
	{
		$result = $this->table('realtime_log')->where($condition)->page($page)->limit($limit)->order($order)->select();
		return $result;
	}

	public function getLogCount($condition = array())
	{
		return $this->table('realtime_log')->where($condition)->count();
	}

	public function getWxTpl($condition = array())
	{
		$result = $this->table('weixin_msg_tpl')->where($condition)->select();
		return $result;
	}

	public function editWxTpl($condition, $data)
	{
		if (empty($condition)) {
			return false;
		}

		if (is_array($data)) {
			$result = $this->table('weixin_msg_tpl')->where($condition)->update($data);
			return $result;
		}
		else {
			return false;
		}
	}
}

?>
