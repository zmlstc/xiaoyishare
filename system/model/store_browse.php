<?php
/**
 * 
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class store_browseModel extends Model{
    public function __construct(){
        parent::__construct('goods_browse');
    }

    /**
     * 删除浏览记录
     *
     * @param array $where
     * @return array
     */
    public function delStorebrowse($where){
        return $this->table('store_browse')->where($where)->delete();
    }
	
	public function saveViewedStore($member_id = 0,$store_id = 0) {
        if (!$store_id){
            return false;
        }
		$store_b = $this->table('store_browse')->where(array('store_id'=>$store_id,'member_id'=>$member_id))->find();
		
		if(!empty($store_b)&& is_array($store_b)){
			$result=$this->table('store_browse')->where(array('store_id'=>$store_id,'member_id'=>$member_id))->update(array('browsetime'=>time()));
		}else{
			$result=$this->addStorebrowse(array('store_id'=>$store_id,'member_id'=>$member_id,'browsetime'=>time()));
		}
        return $result;
    }

    /**
     * 添加单条浏览记录
     *
     * @param array $where
     * @return array
     */
    public function addStorebrowse($insert_arr){
        return $this->table('store_browse')->insert($insert_arr);
    }
    /**
     * 添加多条浏览记录
     *
     * @param array $where
     * @return array
     */
    public function addStorebrowseAll($insert_arr){
        return $this->table('store_browse')->insertAll($insert_arr);
    }
    /**
     * 查询浏览记录
     *
     * @param array $where
     * @return array
     */
    public function getStorebrowseList($where, $field = '*', $page = 0, $limit = 0, $order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('store_browse')->field($field)->where($where)->page($page[0],$page[1])->limit($limit)->order($order)->group($group)->select();
            } else {
                return $this->table('store_browse')->field($field)->where($where)->page($page[0])->limit($limit)->order($order)->group($group)->select();
            }
        } else {
            return $this->table('store_browse')->field($field)->where($where)->page($page)->limit($limit)->order($order)->group($group)->select();
        }
    }

}
