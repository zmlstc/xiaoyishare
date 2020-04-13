<?php
/**
 * 反馈分类
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class feedback_classModel extends Model {
    /**
     * 类别列表
     *
     * @param array $condition 检索条件
     * @return array 数组结构的返回结果
     */
    public function getClassList($condition){
        $condition_str = $this->_condition($condition);
        $param = array();
        $param['table'] = 'feedback_class';
        $param['where'] = $condition_str;
        $param['order'] = empty($condition['order'])?'f_id asc':$condition['order'];
        $result = Db::select($param);
        return $result;
    }

    /**
     * 构造检索条件
     *
     * @param int $id 记录ID
     * @return string 字符串类型的返回结果
     */
    private function _condition($condition){
        $condition_str = '';

        if ($condition['f_parent_id'] != ''){
            $condition_str .= " and f_parent_id = '". intval($condition['f_parent_id']) ."'";
        }
        if ($condition['no_f_id'] != ''){
            $condition_str .= " and f_id != '". intval($condition['no_f_id']) ."'";
        }
        if ($condition['f_name'] != ''){
            $condition_str .= " and f_name = '". $condition['f_name'] ."'";
        }

        return $condition_str;
    }

    /**
     * 取单个分类的内容
     *
     * @param int $id 分类ID
     * @return array 数组类型的返回结果
     */
    public function getOneClass($id){
        if (intval($id) > 0){
            $param = array();
            $param['table'] = 'feedback_class';
            $param['field'] = 'f_id';
            $param['value'] = intval($id);
            $result = Db::getRow($param);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 新增
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function add($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $result = Db::insert('feedback_class',$tmp);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 更新信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function updates($param){
        if (empty($param)){
            return false;
        }
        if (is_array($param)){
            $tmp = array();
            foreach ($param as $k => $v){
                $tmp[$k] = $v;
            }
            $where = " f_id = '". $param['f_id'] ."'";
            $result = Db::update('feedback_class',$tmp,$where);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 删除分类
     *
     * @param int $id 记录ID
     * @return bool 布尔类型的返回结果
     */
    public function del($id){
        if (intval($id) > 0){
            $where = " f_id = '". intval($id) ."'";
            $result = Db::delete('feedback_class',$where);
            return $result;
        }else {
            return false;
        }
    }


}
