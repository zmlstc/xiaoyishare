<?php
/**
 * 我的代金券
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class member_pointsControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }
    /**
     * 积分日志列表
     */
    public function pointslogWt(){
        $where = array();
        $where['pl_memberid'] = $this->member_info['member_id'];
        $where['is_del'] = 0;
        //查询积分日志列表
        $points_model = Model('points');
        $log_list = $points_model->getPointsLogList($where, '*', 0, 12);
        $page_count = $points_model->gettotalpage();
        output_data(array('list' => $log_list), mobile_page($page_count));
    }
	    /**
     * 积分日志列表
     */
    public function pldeleteWt(){
        $where = array();
        $where['pl_memberid'] = $this->member_info['member_id'];
        $where['pl_id'] = intval($_POST['pl_id']);
        //查询积分日志列表
        $points_model = Model('points');
        $ret = $points_model->hidePoints($where);
		if($ret){
			 output_data(1);
		}else{
			 output_error('删除失败');
		}
		
    }
	
}
