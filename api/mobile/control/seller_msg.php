<?php
/**
 * 
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class seller_msgControl extends mobileSellerControl {

    public function __construct(){
        parent::__construct();
    }
    public function indexWt() {
		
	}
	

    /**
     * 系统站内信列表
     *
     * @param
     * @return
     */
    public function systemmsgWt(){
		$where = array();
        $where['store_id'] = $this->store_info['store_id'];
       // if (!$_SESSION['seller_is_admin']) {
        //    $where['smt_code'] = array('in', $_SESSION['seller_smt_limits']);
        //}
        $model_storemsg = Model('store_msg');
		
		Model()->table('store_msg')->where(array('store_id'=>$this->store_info['store_id'],'is_read'=>0))->update(array('is_read'=>1));
        $msg_list = $model_storemsg->getStoreMsgList($where, '*', 10);
		 $page_count = $model_storemsg->gettotalpage();

		 $list = array();
        // 整理数据
        if (!empty($msg_list)) {
            foreach ($msg_list as $key => $val) {
                $msg_list[$key]['sm_readids'] = explode(',', $val['sm_readids']);
				$data = array();
               
                $data['from_member_name'] = '系统消息';
                $data['message_id'] = $val['sm_id'];
                $data['message_body'] = $val['sm_content'];
				$data['time_txt'] = $this->get_time($val['sm_addtime']);
				$list[] = $data;
            }
        }

		
        output_data(array('list' => $list), mobile_page($page_count));
		
    }
	
	/**
     * 删除系统消息
     */
    public function sysmsgdelWt() {
        // 验证参数
        $smids = $_POST['msgid'];
        if(empty($_POST['msgid'])||intval($_POST['msgid'])<1){
			 output_error('参数错误');
        }
        //$smid_array = explode(',', $smids);

        // 验证是否为管理员
     /*    if (!$this->checkIsAdmin()) {
            showDialog(L('para_error'), '', 'error');
        } */

        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['sm_id'] = $smids;
        // 删除消息记录
        Model('store_msg')->delStoreMsg($where);
        // 删除阅读记录
        unset($where['store_id']);
        Model('store_msg_read')->delStoreMsgRead($where);
        // 清除店铺消息数量缓存
        setWtCookie('storemsgnewnum'.$this->store_info['store_id'],0,-3600);
		output_data(array('state' => 1));
    }

    /**
     * 系统公告
     *
     * @param
     * @return
     */
    public function ggmsgWt(){
        $model_message  = Model('article');
        $page   = new Page();
        $page->setEachNum(12);
        $page->setStyle('admin');
        $condition = array();
        $condition['ac_id'] = 1;
        //$condition['article_position_in'] = ARTICLE_POSIT_ALL.','.ARTICLE_POSIT_SELLER;
        $message_array  = $model_message->getArticleList($condition,$page);
        $page_count = $model_message->gettotalpage();
	    $list = array();
		if(!empty($message_array)&&is_array($message_array)){
			 foreach ($message_array as $k=>$v){
				 $data= array();
                $data['article_title'] = $v['article_title'];
                $data['article_content'] = $v['article_content'];
                //$data['article_content'] = $v['article_content'];
                $data['article_id'] = $v['article_id'];
				$data['time_txt'] = $this->get_time($v['article_time']);
                $list[$k]	= $data;
            }
		}
        output_data(array('list' => $list), mobile_page($page_count));
		
	   
    }	
	
	private function get_time($targetTime)
	{
		// 今天最大时间
		$todayLast   = strtotime(date('Y-m-d 23:59:59'));
		$agoTimeTrue = time() - $targetTime;
		$agoTime     = $todayLast - $targetTime;
		$agoDay      = floor($agoTime / 86400);

		if ($agoTimeTrue < 60) {
			$result = '刚刚';
		} elseif ($agoTimeTrue < 3600) {
			$result = (ceil($agoTimeTrue / 60)) . '分钟前';
		} elseif ($agoTimeTrue < 3600 * 12) {
			$result = (ceil($agoTimeTrue / 3600)) . '小时前';
		} elseif ($agoDay == 0) {
			$result = '今天 ' . date('H:i', $targetTime);
		} elseif ($agoDay == 1) {
			$result = '昨天 ' . date('H:i', $targetTime);
		} elseif ($agoDay == 2) {
			$result = '前天 ' . date('H:i', $targetTime);
		} elseif ($agoDay > 2 && $agoDay < 16) {
			$result = $agoDay . '天前 ' . date('H:i', $targetTime);
		} else {
			$format = date('Y') != date('Y', $targetTime) ? "Y-m-d H:i" : "m-d H:i";
			$result = date($format, $targetTime);
		}
		return $result;
	}

	
    public function msgnumWt(){		
		
		$where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['is_read'] = '0';
        $model_storemsg = Model('store_msg');
        $sysnum = $model_storemsg->getStoreMsgCount($where);
        $sysMsg = $model_storemsg->getStoreMsgInfo($where, '*', 'sm_id desc');
		
		$data = array();
		$data['sysnum'] = $sysnum;
		if(!empty($sysMsg)&&is_array($sysMsg)){
			//$data['sys_message_body'] = $sysMsg['sm_content'];
			$data['sys_message_body'] = str_cut(strip_tags($sysMsg['sm_content']),20);
			$data['sys_time_txt'] = $this->get_time($sysMsg['sm_addtime']);
		}
		
		$model_message  = Model('article');
        $condition = array();
        $condition['ac_id'] = 1;
        //$condition['article_position_in'] = ARTICLE_POSIT_ALL.','.ARTICLE_POSIT_SELLER;
        $ggMsg  = $model_message->getArticleList($condition,1);
		if(!empty($ggMsg)&&is_array($ggMsg)){
			//$data['gg_message_body'] = $ggMsg[0]['article_content'];
			$data['gg_message_body'] = str_cut(strip_tags($ggMsg[0]['article_content']),20);
			$data['gg_time_txt'] = $this->get_time($ggMsg[0]['article_time']);
		}
		output_data(array('info' => $data));
	}
	
	
	
	
}
