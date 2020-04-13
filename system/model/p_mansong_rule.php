<?php
/**
 * 满即送活动规则模型
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class p_mansong_ruleModel extends Model{

    public function __construct(){
        parent::__construct('p_mansong_rule');
    }

    /**
     * 读取满即送规则列表
     * @param array $mansong_id 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 满即送套餐列表
     *
     */
    public function getMansongRuleListByID($mansong_id) {
        $condition = array();
        $condition['mansong_id'] = $mansong_id;
        $mansong_rule_list = $this->where($condition)->order('price asc')->select();
        if(!empty($mansong_rule_list)) {
            $model_goods = Model('goods');

            for($i =0, $j = count($mansong_rule_list); $i < $j; $i++) {
                $goods_id = intval($mansong_rule_list[$i]['goods_id']);
                if(!empty($goods_id)) {
                    $goods_content = $model_goods->getGoodsOnlineInfoByID($goods_id);
                    if(!empty($goods_content)) {
                        if(empty($mansong_rule_list[$i]['mansong_goods_name'])) {
                            $mansong_rule_list[$i]['mansong_goods_name'] = $goods_content['goods_name'];
                        }
                        $mansong_rule_list[$i]['goods_image'] = $goods_content['goods_image'];
                        $mansong_rule_list[$i]['goods_image_url'] = cthumb($goods_content['goods_image'], $goods_content['store_id']);
                        $mansong_rule_list[$i]['goods_storage'] = $goods_content['goods_storage'];
                        $mansong_rule_list[$i]['goods_id'] = $goods_id;
                        $mansong_rule_list[$i]['goods_url'] = urlShop('goods', 'index', array('goods_id' => $goods_id));
                    }
                }
            }
        }
        return $mansong_rule_list;
    }

    /*
     * 增加
     * @param array $param
     * @return bool
     *
     */
    public function addMansongRule($param){
        return $this->insert($param);
    }

    /*
     * 批量增加
     * @param array $array
     * @return bool
     *
     */
    public function addMansongRuleArray($array){
        return $this->insertAll($array);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     *
     */
    public function delMansongRule($condition){
        return $this->where($condition)->delete();
    }
}
