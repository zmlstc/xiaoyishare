<?php
/**
 * 交易快照
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class order_vr_snapshotModel extends Model {

    public function __construct() {
        parent::__construct('order_vr_snapshot');
    }

    /**
     * 由订单商品表主键取得交易快照信息
     * @param int $order_id
     * @param int $goods_id
     * @return array
     */
    public function getSnapshotInfoByOrderid($order_id,$goods_id) {
        $info = $this->where(array('order_id'=>$order_id))->find();
        if (empty($info['file_dir'])) {
            return $this->createSphot($order_id, $goods_id,$info);
        }
        return $info;
    }

    public function createSphot($order_id,$goods_id,$snapshot_info = array()) {
        $model_goods = Model('goods');
        $goods_content = $model_goods->getGoodsInfo(array('goods_id'=>$goods_id),'goods_serial,goods_body,goods_commonid');
        $goods_common_info = $model_goods->getGoodsCommonInfo(array('goods_commonid'=>$goods_content['goods_commonid']),'brand_name,goods_attr,goods_body,plateid_top,plateid_bottom');
        $goods_common_info['goods_attr'] = unserialize($goods_common_info['goods_attr']);
        $_attr = array();
        $_attr['货号'] = $goods_content['goods_serial'];
        $_attr['品牌'] = $goods_common_info['brand_name'];
        if (is_array($goods_common_info['goods_attr']) && !empty($goods_common_info['goods_attr'])) {
            foreach($goods_common_info['goods_attr'] as $v) {
                $_attr[$v['name']] = end($v);
            }            
        }

        $info = array();
        $info['order_id'] = $order_id;
        $info['goods_id'] = $goods_id;
        $info['create_time'] = time();
        $info['goods_attr'] = serialize($_attr);
        
        $dir = BASE_UPLOAD_PATH.DS.ATTACH_PATH.DS.'snapshot'.DS.date('Y-m-d').DS;
        if(!is_dir($dir)){
            mkdir($dir,0755,true);
        }
        $file_name = $goods_id.'-'.$order_id.'-v-'.md5(rand(100,999)).'.php';
        
        $web_html ="<?php defined('ShopWT') or exit('Access Invalid!');?>";
        $model_plate = Model('store_plate');
        // 顶部关联版式
        if ($goods_common_info['plateid_top'] > 0) {
            $plate_top = $model_plate->getStorePlateInfoByID($goods_common_info['plateid_top']);
            $web_html .='<div class="top-template">'.$plate_top['plate_content'].'</div>';
        }
        $goods_body = $goods_content['goods_body'] == '' ? $goods_common_info['goods_body'] : $goods_content['goods_body'];
        $web_html .='<div class="default">'.$goods_body.'</div>';
        // 底部关联版式
        if ($goods_common_info['plateid_bottom'] > 0) {
            $plate_bottom = $model_plate->getStorePlateInfoByID($goods_common_info['plateid_bottom']);
            $web_html .='<div class="bottom-template">'.$plate_bottom['plate_content'].'</div>';
        }
        file_put_contents($dir.$file_name,$web_html);
        if (empty($snapshot_info)) {
            $info['file_dir'] = date('Y-m-d').DS.$file_name;
            $this->insert($info);
            return $info;
        } else {
            $file_dir = date('Y-m-d').DS.$file_name;
            $this->where(array('order_id'=> $snapshot_info['order_id']))->update(array('file_dir'=> $file_dir));
            $snapshot_info['file_dir'] = date('Y-m-d').DS.$file_name;
            return $snapshot_info;
        }
    }

}
