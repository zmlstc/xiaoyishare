<?php
/**
 * 店铺评分模型
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class evaluate_storeModel extends Model {

    public function __construct(){
        parent::__construct('evaluate_store');
    }

    /**
     * 查询店铺评分列表
     *
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 字段
     * @return array
     */
    public function getEvaluateStoreList($condition, $page=null, $order='seval_id desc', $field='*') {
        $list = $this->field($field)->where($condition)->page($page)->order($order)->select();
        return $list;
    }

    /**
     * 获取店铺评分信息
     */
    public function getEvaluateStoreInfo($condition, $field='*') {
        $list = $this->field($field)->where($condition)->find();
        return $list;
    }

	/**
     * 获取店铺评分条数
     */
    public function getEvaluateStoreCount($condition) {
        $count = $this->field($field)->where($condition)->count();
        return $count;
    }

    /**
     * 根据店铺编号获取店铺评分数据
     *
     * @param int @store_id 店铺编号
     */
    public function getEvaluateStoreInfoByStoreID($store_id) {
        $prefix = 'evaluate_store_info';
        $info = rcache($store_id, $prefix);
        if(empty($info)) {
            $info = array();
            $info = $this->_getEvaluateStore(array('seval_storeid' => $store_id));
           // $info['store_credit_average'] = number_format(round((($info['store_credit']['store_desccredit']['credit'] + $info['store_credit']['store_servicecredit']['credit'] + $info['store_credit']['store_deliverycredit']['credit']) / 3), 1), 1);
           // $info['store_credit_percent'] = intval($info['store_credit_average'] / 5 * 100);
          
            $cache = array();
            $cache['evaluate'] = serialize($info);
            wcache($store_id, $cache, $prefix, 60 * 24);
        } else {
            $info = unserialize($info['evaluate']);
        }
        return $info;
    }

    /**
     * 根据分类编号获取分类评分数据
     */
    public function getEvaluateStoreInfoByScID($sc_id) {
        $prefix = 'sc_evaluate_store_info';
        $info = rcache($sc_id, $prefix);
        if(empty($info)) {
            $model_store = Model('store');
            $store_id_string = $model_store->getStoreIDString(array('sc_id' => $sc_id));
            $info = $this->_getEvaluateStore(array('seval_storeid' => array('in', $store_id_string)));
            $cache = array();
            $cache['evaluate_store_info'] = serialize($info);
            wcache($sc_id, $cache, $prefix, 60 * 24);
        } else {
            $info = unserialize($info['evaluate_store_info']);
        }
        return $info;
    }

    /**
     * 获取店铺评分数据
     */
    private function _getEvaluateStore($condition) {
        $result = array();
        $field = 'AVG(seval_scores) as seval_scores,';
        $field .= 'COUNT(seval_id) as count';
        $info = $this->getEvaluateStoreInfo($condition, $field);
        
        if(intval($info['count']) > 0) {
            $result['seval_scores'] = number_format(round($info['seval_scores'], 1), 1);
           
        } else {
            $result['seval_scores'] = 0;//number_format(5, 1);
        }
        return $result;
    }


    /**
     * 添加店铺评分
     */
    public function addEvaluateStore($param) {
        return $this->insert($param);
    }

    /**
     * 删除店铺评分
     */
    public function delEvaluateStore($condition) {
		if(empty($condition)) return false;
        return $this->where($condition)->delete();
    }
	
    public function EditEvaluateStore($condition,$data) {
		if(empty($condition)||empty($data)) return false;
        return $this->where($condition)->update($data);;
    }
	
}
