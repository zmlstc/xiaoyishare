<?php
/**
 * 活动
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class plat_actModel extends Model {
    /**
     * 活动列表
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getList($condition,$page=''){
        $param  = array();
        $param['table'] = 'plat_act';
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'] ? $condition['order'] : 'act_id';
        return Db::select($param,$page);
    }
	public function getActList($where = '', $field='*',$order='',$limit='',$page=''){
        if (empty($order)){
            $order = 'act_sort asc';
        }
        $list = $this->table('plat_act')->field($field)->where($where)->order($order)->limit($limit)->page($page)->select();

        return $list;
    }
    /**
     * 添加活动
     *
     * @param array $input
     * @return bool
     */
    public function add($input){
        return Db::insert('plat_act',$input);
    }
    /**
     * 更新活动
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updates($input,$id){
        return Db::update('plat_act',$input," act_id='$id' ");
    }
    /**
     * 删除活动
     *
     * @param string $id
     * @return bool
     */
    public function del($id){
        return Db::delete('plat_act','act_id in('.$id.')');
    }
    /**
     * 根据id查询一条活动
     *
     * @param int $id 活动id
     * @return array 一维数组
     */
    public function getOneById($id){
        return Db::getRow(array('table'=>'plat_act','field'=>'act_id','value'=>$id));
    }
	
	public function getActInfo($condition = array()) {
        return $this->table('plat_act')->where($condition)->find();
    }
    /**
     * 更新礼品浏览次数
     */
    public function editActViewnum($act_id){
        if (intval($act_id) <= 0){
            return array('state'=>false,'msg'=>'参数错误');
        }
        $viewnum = 0;//最新浏览次数
        $cache_arr = array();
        $tmptime = time();
        if (!C('cache_open')){//直接更新数据库浏览次数
            $this->updates(array('act_click'=>array('exp','act_click+1')), $act_id);
        } else {//通过缓存记录浏览次数
            $prod_info = rcache($act_id, 'plat_act', 'act_click,view_updatetime');
            if (empty($prod_info)){//如果兑换礼品的浏览次数缓存不存在，则查询兑换礼品数据库信息，建立缓存
                //查询兑换礼品信息
                $prod_info = $this->getOneById(act_id);
                $viewnum = intval($prod_info['act_click']) + 1;
                wcache($act_id, array('act_click'=>$viewnum,'view_updatetime'=>$tmptime), 'plat_act');
            } else {
                $viewnum = intval($prod_info['act_click']) + 1;
                if (($prod_info['view_updatetime']+3600) < $tmptime){//如果缓存时间超出1小时，则将更新进入数据库，时间初始为当前时间
                    $this->updates(array('act_click'=>$viewnum), $act_id);
                    wcache($act_id, array('act_click'=>$viewnum,'view_updatetime'=>$tmptime), 'plat_act');
                } else {//如果缓存时间未超出1小时，则更新浏览次数
                    wcache($act_id, array('act_click'=>$viewnum), 'plat_act');
                }
            }
        }
        return array('state'=>true);
    }
    /**
     * 构造查询条件
     *
     * @param array $condition 条件数组
     * @return string
     */
    private function getCondition($condition){
        $conditionStr   = '';
        if($condition['act_id'] != ''){
            $conditionStr   .= " and plat_act.act_id='{$condition['act_id']}' ";
        }
        if(isset($condition['parent_id'])){
            $conditionStr   .= " and plat_act.parent_id='{$condition['parent_id']}' ";
        }
       /*  if($condition['act_type'] != ''){
            $conditionStr   .= " and plat_act.act_type='{$condition['act_type']}' ";
        } */
        if ((string) $condition['act_state'] !== ''){
            $conditionStr   .= " and plat_act.act_state = '{$condition['act_state']}' ";
        }
        //活动删除in
        if(isset($condition['act_id_in'])){
            if ($condition['act_id_in'] == ''){
                $conditionStr   .= " and act_id in('')";
            }else{
                $conditionStr   .= " and act_id in({$condition['act_id_in']}) ";
            }
        }
        if($condition['act_name'] != ''){
            $conditionStr   .= " and act.act_name like '%{$condition['act_name']}%' ";
        }
        //当前时间大于结束时间（过期）
        if ($condition['act_enddate_greater'] != ''){
            $conditionStr   .= " and plat_act.activity_end_date < '{$condition['activity_enddate_greater']}'";
        }
        //可删除的活动记录
        if ($condition['act_enddate_greater_or'] != ''){
            $conditionStr   .= " or plat_act.activity_end_date < '{$condition['activity_enddate_greater_or']}'";
        }
        //某时间段内正在进行的活动
        if($condition['activity_daterange'] != ''){
            $conditionStr .= " and (activity.activity_end_date >= '{$condition['activity_daterange']['startdate']}' and activity.activity_start_date <= '{$condition['activity_daterange']['enddate']}')";
        }
        if($condition['opening']){//在有效期内、活动状态为开启
            $conditionStr   .= " and (activity.activity_start_date <=".time()." and activity.activity_end_date >= ".time()." and activity.activity_state =1)";
        }

        // 时间段检索
        if ($condition['pdates']) {
            $conditionStr .= " and ({$condition['pdates']})";
        }

        return $conditionStr;
    }
}
