<?php
/**
 * 意见反馈
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */

defined('ShopWT') or exit('Access Denied By ShopWT');

class mb_feedbackModel extends Model{
    public function __construct(){
        parent::__construct('mb_feedback');
    }

    /**
     * 列表
     *
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @return array
     */
    public function getMbFeedbackList($condition, $page = null, $order = 'id desc'){
        $list = $this->where($condition)->page($page)->order($order)->select();
        return $list;
    }
	
	
    public function getInfoById($id){
        $info = $this->where(array('id'=>$id))->find();
        return $info;
    }

    /**
     * 新增
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addMbFeedback($param){
        return $this->insert($param);
    }
	
	public function editMbFeedback($update, $condition) {
        return $this->where($condition)->update($update);
    }

    /**
     * 删除
     *
     * @param int $id 记录ID
     * @return bool 布尔类型的返回结果
     */
    public function delMbFeedback($id){
        $condition = array('id' => array('in', $id));
        return $this->where($condition)->delete();
    }
		
    /**
     * 数量
     *
     * @param array $condition
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getMbFeedbackCount($condition) {
        return $this->where($condition)->count();
    }
	
}
