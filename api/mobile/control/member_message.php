<?php
/**
 * 我的消息
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class member_messageControl extends mobileMemberControl {
    private $coupon_state_arr;

    public function __construct() {
        parent::__construct();
       
    }
	public function indexWt(){
		
	}

    /**
     * 系统站内信列表
     *
     * @param
     * @return
     */
    public function systemmsgWt(){
		$member_id = $this->member_info['member_id'];
        $model_message	= Model('message');
		Model()->table('message')->where(array('to_member_id'=>$member_id,'message_open'=>0))->update(array('message_open'=>1));
        $message_array	= $model_message->listMessage(array('from_member_id'=>'0','message_state'=>'0','message_type'=>'1','to_member_id'=>$member_id,'no_del_member_id'=>$member_id),8);
        $page_count = $model_message->gettotalpage();
		
	   if (!empty($message_array) && is_array($message_array)){
            foreach ($message_array as $k=>$v){
				$data = array();
                $data['message_open'] = '0';
                if (!empty($v['read_member_id'])){
                    $tmp_readid_arr = explode(',',$v['read_member_id']);
                    if (in_array($member_id,$tmp_readid_arr)){
                        $data['message_open'] = '1';
                    }
                }
                $data['from_member_name'] = '系统消息';
                $data['message_id'] = $v['message_id'];
                $data['message_body'] = $v['message_body'];
				$data['time_txt'] = $this->get_time($v['message_time']);
                $message_array[$k]	= $data;
            }
        }
		
		
        // 新消息数量
        //$this->showReceivedNewNum();
		
        output_data(array('list' => $message_array), mobile_page($page_count));
		
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
        //$condition['article_position_in'] = ARTICLE_POSIT_ALL.','.ARTICLE_POSIT_BUYER;
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
		$member_id = $this->member_info['member_id'];
        $message_model = Model('message');
        $condition_arr = array();
        $condition_arr['message_type'] = '1';//系统消息
		$condition_arr['message_state'] = '0';
        $condition_arr['to_member_id'] = $member_id;
        $condition_arr['no_del_member_id'] = $member_id;
        $condition_arr['no_read_member_id'] = $member_id;
		$condition_arr['message_open_common'] = '0';
        $sysnum = $message_model->countMessage($condition_arr);
		
        $sysMsg	= $message_model->listMessage(array('from_member_id'=>'0','message_state'=>'0','message_type'=>'1','to_member_id'=>$member_id,'no_del_member_id'=>$member_id),1);
		$data = array();
		if(!empty($sysMsg)&&is_array($sysMsg)){
			$data['sysnum'] = $sysnum;
			//$data['sys_message_body'] = $sysMsg[0]['message_body'];
			$data['sys_message_body'] = str_cut(strip_tags($sysMsg[0]['message_body']),30);
			$data['sys_time_txt'] = $this->get_time($sysMsg[0]['message_time']);
		}
		
		$model_message  = Model('article');
        $condition = array();
        $condition['ac_id'] = 1;
        //$condition['article_position_in'] = ARTICLE_POSIT_ALL.','.ARTICLE_POSIT_BUYER;
        $ggMsg  = $model_message->getArticleList($condition,1);
		if(!empty($ggMsg)&&is_array($ggMsg)){
			//$data['gg_message_body'] = $ggMsg[0]['article_content'];
			$data['gg_message_body'] = str_cut(strip_tags($ggMsg[0]['article_content']),20);
			$data['gg_time_txt'] = $this->get_time($ggMsg[0]['article_time']);
		}
		output_data(array('info' => $data));
	}
	
	
		/**
     * 删除
     */
    public function sysmsgdelWt() {
        $message_id = trim($_POST['msgid']);
        $drop_type = 'msg_list';
        if( empty($message_id)) {
           output_error('参数错误！');
        }
        $messageid_arr = explode(',',$message_id);
        $messageid_str = '';
        if (!empty($messageid_arr)){
            $messageid_str = "'".implode("','",$messageid_arr)."'";
        }
		$member_id = $this->member_info['member_id'];
        $model_message  = Model('message');
        $param  = array('message_id_in'=>$messageid_str);
       
        $param['from_to_member_id'] = $member_id;
        
        $drop_state = $model_message->dropCommonMessage($param,$drop_type);
        if ($drop_state){
            //更新未读站内信数量cookie值
           /*  $cookie_name = 'msgnewnum'.$_SESSION['member_id'];
            $countnum = $model_message->countNewMessage($_SESSION['member_id']);
            setWtCookie($cookie_name,$countnum,2*3600);//保存2小时 */
            output_data(array('state'=>1));
        }else {
            output_error('删除失败');
        }
    }
	
	
	
	
	
	
	
	
	

	
}
