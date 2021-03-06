<?php
/**
 * 广告模型类
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.sh opwt.com
 * @link       交流群号：138 182377
 *
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class showModel extends Model
{
    /**
     * 新增广告位
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function ap_add($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $result = Db::insert('show_position',$tmp);
            return $result;
        }else {
            return false;
        }
    }
   /**
     * 新增广告
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function show_add($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $result = Db::insert('show',$tmp);

            // drop cache
            $apId = (int) $tmp['ap_id'];
            dkcache("shopwt/{$apId}");

            return $result;
        }else {
            return false;
        }
    }
    /**
     * 删除一条广告
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function show_del($show_id)
    {
        $show_array = Model()->table('show')->where(array('show_id'=>$show_id))->find();
        if ($show_array) {
            // drop cache
            $apId = (int) $show_array['ap_id'];
            dkcache("shopwt/{$apId}");
        }

        $where  = "where show_id = '$show_id'";
        $result = Db::delete("show",$where);
        return $result;
    }
    /**
     * 删除一个广告位
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */

	
	  public function ap_del($ap_id)
    {
        // drop cache
        $apId = (int) $ap_id;
        dkcache("shopwt/{$apId}");

        $where  = "where ap_id = '$ap_id'";
        $result = Db::delete("show_position",$where);
        return $result;
    }
    /**
     * 获取广告位列表
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getApList($condition=array(), $page='', $orderby=''){
        $param  = array();
        $param['table'] = 'show_position';
        $param['where'] = $this->getCondition($condition);
        if($orderby == ''){
            $param['order'] = 'ap_id desc';
        }else{
            $param['order'] = $orderby;
        }
        return Db::select($param,$page);
    }
    /**
     * 根据条件查询多条记录
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getList($condition=array(), $page='', $limit='', $orderby=''){
        $param  = array();
        $param['table'] = 'show';
        $param['field'] = $condition['field']?$condition['field']:'*';
        $param['where'] = $this->getCondition($condition);
        if($orderby == ''){
            $param['order'] = 'slide_sort, show_id desc';
        }else{
            $param['order'] = $orderby;
        }
        $param['limit'] = $limit;
        return Db::select($param,$page);
    }
    /**
     * 根据id查询一条记录
     *
     * @param int $id 广告id
     * @return array 一维数组
     */
    public function getOneById($id){
        $param  = array();
        $param['table'] = 'show';
        $param['field'] = 'show_id';
        $param['value'] = $id;
        return Db::getRow($param);
    }

    /**
     * 更新记录
     *
     * @param array $param 更新内容
     * @return bool
     */
    public function updates($param)
    {
        $show_array = Model()->table('show')->where(array('show_id'=>$param['show_id']))->find();
        if ($show_array) {
            // drop cache
            $apId = (int) $show_array['ap_id'];
            dkcache("shopwt/{$apId}");
        }

        return Db::update('show',$param,"show_id='{$param['show_id']}'");
    }
    /**
     * 更新广告位记录
     *
     * @param array $param 更新内容
     * @return bool
     */
    public function ap_update($param)
    {
        $apId = (int) $param['ap_id'];
        dkcache("shopwt/{$apId}");

        return Db::update('show_position',$param,"ap_id='{$param['ap_id']}'");
    }
    /**
     * 构造查询条件
     *
     * @param array $condition
     * @return string
     */
    private function getCondition($condition = array()){
        $return = '';
        $time   = time();
        if($condition['show_type'] != ''){
            $return .= " and show_type='".$condition['show_type']."'";
        }
        if($condition['show_code'] != ''){
            $return .= " and show_code='".$condition['show_code']."'";
        }
        if($condition['no_show_type'] != ''){
            $return .= " and show_type!='".$condition['no_show_type']."'";
        }
        if ($condition['show_state'] != '') {
            $return .= " and show_state='".$condition['show_state']."'";
        }
        if ($condition['ap_id'] != '') {
            $return .= " and ap_id='".$condition['ap_id']."'";
        }
        if ($condition['show_id'] != '') {
            $return .= " and show_id='".$condition['show_id']."'";
        }
        if ($condition['show_end_date'] == 'over'){
            $return .= " and show_end_date<'".$time."'";
        }
        if ($condition['show_end_date'] == 'notover'){
            $return .= " and show_end_date>'".$time."'";
        }
        if ($condition['ap_name'] != ''){
            $return .= " and ap_name like '%".$condition['ap_name']."%'";
        }
        if ($condition['show_title'] != ''){
            $return .= " and show_title like '%".$condition['show_title']."%'";
        }
        if ($condition['add_time_from'] != ''){
            $return .= " and show_start_date > '{$condition['add_time_from']}'";
        }
        if ($condition['add_time_to'] != ''){
            $return .= " and show_end_date < '{$condition['add_time_to']}'";
        }
        if ($condition['member_name'] != ''){
            $return .= " and member_name ='".$condition['member_name']."'";
        }
        if($condition['is_allow'] != ''){
            $return .= " and is_allow = '".$condition['is_allow']."' ";
        }
        if($condition['buy_style'] != ''){
            $return .= " and buy_style = '".$condition['buy_style']."' ";
        }
        if($condition['show_start_date'] == 'nowshow'){
            $return .= " and show_start_date <'".$time."'";
        }
        if($condition['member_id'] != ''){
            $return .= " and member_id = '".$condition['member_id']."'";
        }
        if($condition['is_use'] != ''){
            $return .= " and is_use = '".$condition['is_use']."' ";
        }
        if ($condition['show_buy_id'] != '') {
            $return .= " and ap_id not in (".$condition['show_buy_id'].")";
        }
        return $return;
    }

    public function delapcache($id)
    {
        if (!is_numeric($id))
            return;

        dkcache("shopwt/{$id}");

        return true;
    }

    /**
     * 广告
     *
     * @return array
     */
    public function makeApAllCache(){
        if (C('cache_open')) {
            // *kcache() doesnt support iterating on keys
        } else {
            delCacheFile('show');
        }

        $model = Model();
        $ap_list =$model->table('show_position')->where(array('is_use'=>1))->select();
        $show_list =$model->table('show')->where(array('show_end_date'=>array('gt',time())))->order('slide_sort, show_id desc')->select();
        $array = array();
        foreach ((array)$ap_list as $v) {
            foreach ((array)$show_list as $xv) {
                if ($v['ap_id'] == $xv['ap_id']){
                    $v['show_list'][] = $xv;
                }
            }

            // 写入缓存
            $apId = (int) $v['ap_id'];
            if (C('cache_open')) {
                wkcache("shopwt/{$apId}", $v);
            } else {
                write_file(BASE_DATA_PATH . '/cache/shopwt/' . $apId . '.php', $v);
            }
        }
    }

    public function getApById($apId)
    {
        $apId = (int) $apId;
        return rkcache("shopwt/{$apId}", array($this, 'getApByCacheId'));
    }

    /**
     * 通过缓存id获取广告，生成缓存时使用
     *
     * @param $apCacheId 格式为 shopwt/{ap_id}
     */
    public function getApByCacheId($apCacheId)
    {
        $apId = substr($apCacheId, strlen('shopwt/'));
        return $this->getAp($apId);
    }

    /**
     * 生成广告位
     *
     * @param int $ap_id
     */
    protected function getAp($ap_id)
    {
        $model = Model();

        $ap_info = $model->table('show_position')->where(array('ap_id'=>$ap_id))->find();
        $ap_info['show_list'] = $model->table('show')->where(array(
            'ap_id' => $ap_id,
            'show_end_date' => array('gt',time()),
        ))->order('slide_sort, show_id desc')->select();

        return $ap_info;
    }

    /**
     * 删除缓存
     */
    public function dropApCacheByShowIds($showIds)
    {
        $apIds = array_keys((array) Model()->table('show')->field('ap_id')->where(array(
            'show_id' => array('in', (array) $showIds),
        ))->key('ap_id')->select());

        foreach ($apIds as $apId) {
            $apId = (int) $apId;
            dkcache("shopwt/{$apId}");
        }
    }
}
