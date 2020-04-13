<?php
/**
 * 默认展示页面
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class storeclassControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('index');
    }
    public function indexWt(){
        //输出管理员信息
       
    }

    //json输出商品分类
    public function josn_scWt() {
        /**
         * 实例化商品分类模型
         */
        $model_class        = Model('store_class');
        $goods_class        = $model_class->getStoreClassList(array('parent_id'=>intval($_GET['sc_id'])),'',false);//getGoodsClassListByParentId(intval($_GET['sc_id']));
        $array              = array();
        if(is_array($goods_class) and count($goods_class)>0) {
            foreach ($goods_class as $val) {
                $array[$val['sc_id']] = array('sc_id'=>$val['sc_id'],'sc_name'=>htmlspecialchars($val['sc_name']),'sc_parent_id'=>$val['sc_parent_id'],'commis_rate'=>$val['commis_rate'],'sc_sort'=>$val['sc_sort']);
            }
        }
        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK'){
            $array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        } else {
            $array = array_values($array);
        }
        echo $_GET['callback'].'('.json_encode($array).')';
    }
}
