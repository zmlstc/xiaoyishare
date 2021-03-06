<?php
/**
 * 买什么推荐商品分类模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class what_personal_classModel extends Model{

    public function __construct(){

        parent::__construct('what_personal_class');

    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getList($condition,$page=null,$order='',$field='*'){

        $result = $this->field($field)->where($condition)->page($page)->order($order)->select();
        return $result;

    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getOne($condition){

        $result = $this->where($condition)->find();
        return $result;

    }

    /*
     *  判断是否存在
     *  @param array $condition
     *
     */
    public function isExist($condition) {

        $result = $this->getOne($condition);
        if(empty($result)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    /*
     * 增加
     * @param array $param
     * @return bool
     */
    public function save($param){

        return $this->insert($param);

    }

    /*
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     */
    public function modify($update, $condition){

        return $this->where($condition)->update($update);

    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     */
    public function drop($condition){

        return $this->where($condition)->delete();

    }

}
