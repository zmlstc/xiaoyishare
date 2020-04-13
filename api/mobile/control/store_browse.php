<?php
/**
 * 浏览历史
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class store_browseControl extends mobileMemberControl {
    public function __construct(){
        parent::__construct();
    }

    /**
     * 我的足迹
     */
    public function browselistWt(){
        $model_storebrowse = Model('store_browse');
        //查询浏览记录
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $browselist = $model_storebrowse->getStorebrowseList($where, '*', 12, 0, 'browsetime desc');
        $page_count = $model_storebrowse->gettotalpage();
        $storeid_arr = array();
        foreach ((array)$browselist as $k=>$v){
            $storeid_arr[] = $v['store_id'];
        }
        //查询商品信息
        $browselist_new = array();
        if ($storeid_arr){
			$store_list_tmp = Model('store')->getStoreOnlineList(array('store_id' => array('in', $storeid_arr)));
            $store_list = array();
            foreach ((array)$store_list_tmp as $v){
                $store_list[$v['store_id']] = $v;
            }
            foreach ($browselist as $k=>$v){
                if ($store_list[$v['store_id']]){
					$store =$store_list[$v['store_id']];
                    $tmp = array();
                    $tmp['consume_num'] = $store['consume_num'];
                    $tmp['store_id'] = $store['store_id'];
                    $tmp['store_name'] = $store['store_name'];
                   
					$tmp['store_avatar'] = getStoreLogo($store['store_avatar']);
					
                    $tmp['browsetime'] = date('Y-m-d H:i:s',$v['browsetime']);
					$scores= Model('evaluate_store')->getEvaluateStoreInfoByStoreID($v['store_id']);
					$tmp['store_score'] =$scores['seval_scores'];
                    $browselist_new[] = $tmp;
                }
            }
        }
        output_data(array('list' => $browselist_new), mobile_page($page_count));
    }
	 /**
     * 删除足迹
     */
    public function browsedelWt(){
        if (Model('store_browse')->delStorebrowse(array('member_id'=>$this->member_info['member_id'],'store_id'=>intval($_POST['store_id'])))){
            output_data('1');
        } else {
            output_error('清空失败');
        }
    }
    /**
     * 清空足迹
     */
/*     public function browse_clearallWt(){
        if (Model('goods_browse')->delGoodsbrowse(array('member_id'=>$this->member_info['member_id']))){
            output_data('1');
        } else {
            output_error('清空失败');
        }
    } */
}
