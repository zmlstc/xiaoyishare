<?php
/**
 * 文章管理
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class articleModel extends Model {
    /**
     * 列表
     *
     * @param array $condition 检索条件
     * @param obj $page 分页
     * @return array 数组结构的返回结果
     */
    public function getArticleList($condition,$page=''){
        $condition_str = $this->_condition($condition);
        $param = array();
        $param['table'] = 'article';
        $param['where'] = $condition_str;
        $param['limit'] = $condition['limit'];
        $param['order'] = (empty($condition['order'])?'article_sort asc,article_id desc':$condition['order']);
        $result = Db::select($param,$page);
        return $result;
    }

    /**
     * 连接查询列表
     *
     * @param array $condition 检索条件
     * @param obj $page 分页
     * @return array 数组结构的返回结果
     */
    public function getJoinList($condition,$page=''){
        $result = array();
        $condition_str  = $this->_condition($condition);
        $param  = array();
        $param['table'] = 'article,article_class';
        $param['field'] = empty($condition['field'])?'*':$condition['field'];;
        $param['join_type'] = empty($condition['join_type'])?'left join':$condition['join_type'];
        $param['join_on']   = array('article.ac_id=article_class.ac_id');
        $param['where'] = $condition_str;
        $param['limit'] = $condition['limit'];
        $param['order'] = empty($condition['order'])?'article.article_sort':$condition['order'];
        $result = Db::select($param,$page);
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

        if ($condition['article_show'] != ''){
            $condition_str .= " and article.article_show = '". $condition['article_show'] ."'";
        }
        if ($condition['ac_id'] != ''){
            $condition_str .= " and article.ac_id = '". $condition['ac_id'] ."'";
        }
        if ($condition['ac_ids'] != ''){
            $condition_str .= " and article.ac_id in(". $condition['ac_ids'] .")";
        }
        if ($condition['article_position_in'] != ''){
            $condition_str .= " and article.article_position in(". $condition['article_position_in'] .")";
        }
        if ($condition['like_title'] != ''){
            $condition_str .= " and article.article_title like '%". $condition['like_title'] ."%'";
        }
        if ($condition['home_index'] != ''){
            $condition_str .= " and (article_class.ac_id <= 7 or (article_class.ac_parent_id > 0 and article_class.ac_parent_id <= 7))";
        }

        return $condition_str;
    }

    /**
     * 取单个内容
     *
     * @param int $id ID
     * @return array 数组类型的返回结果
     */
    public function getOneArticle($id){
        if (intval($id) > 0){
            $param = array();
            $param['table'] = 'article';
            $param['field'] = 'article_id';
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
            $result = Db::insert('article',$tmp);
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
            $where = " article_id = '". $param['article_id'] ."'";
            $result = Db::update('article',$tmp,$where);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 删除
     *
     * @param int $id 记录ID
     * @return bool 布尔类型的返回结果
     */
    public function del($id){
        if (intval($id) > 0){
            $where = " article_id = '". intval($id) ."'";
            $result = Db::delete('article',$where);
            return $result;
        }else {
            return false;
        }
    }

    /**
     * 取得文章数量
     * @param unknown $condition
     */
    public function getCount($condition = array()) {
        return $this->table('article')->where($condition)->count();
    }
}
