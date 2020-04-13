<?php
/**
 * 签到
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */

defined('ShopWT') or exit('Access Denied By ShopWT');

class member_signinControl extends mobileMemberControl {
    public function __construct(){
        parent::__construct();
       /*  if (!C('signin_isuse')) {
            output_error('签到失败',array('state'=>'isclose'));
        } */
    }
	public function indexWt(){
		$model_signin = Model('signin');
		$num = $model_signin->getSigninCount(array('sl_memberid'=>$this->member_info['member_id']));
		$points = $model_signin->getSigninSum(array('sl_memberid'=>$this->member_info['member_id']));
		$data = array();
		$data['num']= $num;
		$data['points'] = is_null($points)?0:$points;
		$data['type'] = 1;
        $result =$model_signin->isAbleSignin($this->member_info['member_id']);
        if (!$result['done']) {
            //output_error($result['msg']);
			$data['type'] = 0;
        }
        output_data($data);
    }
    /**
     * 签到
     */
    public function signin_addWt(){
        //查询今天是否已签到
        $model_signin = Model('signin');
        $result = $model_signin->isAbleSignin($this->member_info['member_id']);
        if (!$result['done']) {
            output_error($result['msg']);
        }
        try {
            $points = C('points_signin');
            //增加签到记录
            $result = Model('signin')->addSignin(array('points'=>$points,'member_id'=>$this->member_info['member_id'],'member_name'=>$this->member_info['member_name']));
            if (!$result) {
                throw Exception('签到失败');
            }
            //增加积分
            $result = Model('points')->savePointsLog('signin',array('pl_memberid'=>$this->member_info['member_id'],'pl_membername'=>$this->member_info['member_name'],'pl_points'=>$points));
            if (!$result) {
                throw Exception('签到失败');
            }
            output_data(array('point'=>$points));
        } catch (Exception $e) {
            output_error($e->getMessage());
        }
    }
    /**
     * 获取是否能签到
     */
    public function checksigninWt(){
        $result = Model('signin')->isAbleSignin($this->member_info['member_id']);
        if (!$result['done']) {
            output_error($result['msg']);
        }
        output_data(array('points_signin'=>C('points_signin')));
    }
    /**
     * 获得签到日志
     */
    public function signin_listWt(){
        $model_signin = Model('signin');
        $where = array();
        $where['sl_memberid'] = $this->member_info['member_id'];
        $signin_list = $model_signin->getSigninList($where, '*', 0, 15, 'sl_id desc');
        $page_count = $model_signin->gettotalpage();
        if ($signin_list) {
            foreach ($signin_list as $k=>$v) {
                //$v['sl_addtime_text'] = @date('Y-m-d H:i:s', $v['sl_addtime']);
				$v['sl_addtime_text'] = @date('m-d', $v['sl_addtime']);
                $signin_list[$k] = $v;
            }
        }
        output_data(array('signin_list' => $signin_list), mobile_page($page_count));
    }
}