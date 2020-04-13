<?php
/**
 * 代理商模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class agentModel extends Model {

    public function __construct(){
        parent::__construct('agent');
    }

    /**
     * 详细信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getAgentInfo($condition, $field = '*', $master = false) {
        return $this->table('agent')->field($field)->where($condition)->master($master)->find();
    }

    /**
     * 取得详细信息
     * @param int $agent_id
     * @param string $field 
     * @return array
     */
    public function getAgentInfoByID($agent_id, $fields = '*') {
       
        $member_info = $this->getAgentInfo(array('agent_id'=>$agent_id),'*',true);
        
        return $member_info;
    }
	 /**
     * 取得正常代理商id;
     * @param int $agent_id
     * @param string $field 
     * @return array
     */
	public function getAgentIdByAreaID($area_id) {
		$agent_id = 0;
		$area_info = Model('agent_area')->getAgentAreaInfo(array('area_id'=>$area_id));
		if(!empty($area_info)&& is_array($area_info)){
			$agent_info=$this->getAgentInfoByID($area_info['agent_id']);
			if(!empty($agent_info)&& $agent_info['agent_state']==1){
				$agent_id = $area_info['agent_id'];
			}
		}
		return $agent_id;
    }

    /**
     * 列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getAgentList($condition = array(), $field = '*', $page = null, $order = 'agent_id desc', $limit = '') {
       return $this->table('agent')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    public function getAgenList($condition = array(), $limit = '', $field = '*',$order = 'agent_id desc') {
       return $this->table('agent')->field($field)->where($condition)->order($order)->limit($limit)->select();
    }
	
	/**
	 * 删除
	 *
	 * @param int $id 记录ID
	 * @return array $rs_row 返回数组形式的查询结果
	 */
	public function del($agent_id){
		if (intval($id) > 0){
			$where = " agent_id = '". intval($id) ."'";
			$result = Db::delete('agent',$where);
			return $result;
		}else {
			return false;
		}
	}
	
	 /**
     * 编辑代理商信息
     * @param array $condition
     * @param array $data
     */
    public function editAgent($condition, $data) {
        $update = $this->table('agent')->where($condition)->update($data);
       
        return $update;
    }
	
	/**
     * 添加代理商
     *
     * @param   array $param 信息
     * @return  array 数组格式的返回结果
     */
    public function addAgent($param) {
        if(empty($param)) {
            return false;
        }
        
		$insert_id  = $this->table('agent')->insert($param);
		
		return $insert_id;
        
    }
	
	    /**
     * 取数量
     * @param unknown $condition
     */
    public function getAgentCount($condition = array()) {
        return $this->where($condition)->count();
    }
	
	
	
	
}
