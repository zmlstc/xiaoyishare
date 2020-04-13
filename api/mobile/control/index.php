<?php
/**
 * 手机端首页控制
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh op wt.c om
 * @link
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 *
 */

defined('ShopWT') or exit('Access Denied By ShopWT');

class indexControl extends mobileHomeControl {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function indexWt() {

        //积分商品
        /* $pointprod_model = Model('pointprod');
        $pointGoodsList =$pointprod_model->getRecommendPointProd(8); */
        //banner 广告
        $aplist = $this->getApList(1050);
        //店铺分类
        $storeclass_list = Model('store_class')->getStoreClassList(array('parent_id' => 0), 10);
        $sc_list = array();
        if (!empty($storeclass_list) && is_array($storeclass_list)) {
            foreach ($storeclass_list as $key => $sc) {
                $sc_info = array();
                $sc_info['sc_id'] = $sc['sc_id'];
                $sc_info['sc_name'] = $sc['sc_name'];
                $sc_list[] = $sc_info;
            }
        }

        /*
            $model_mb_special = Model('mb_special');
            $data = $model_mb_special->getMbSpecialIndex();
            $seo = Model('seo')->type('index')->showwap();
            //分享用户id
            $share_member_info = '';
            $share_member_id = $this->getMemberIdIfExists();
            if($share_member_id>0){
                $encode_member_id = base64_encode(intval($share_member_id)*1);
                $share_member_info = "?smid=".$encode_member_id;
                if(strpos($url,'?') !==false){
                    $share_member_info = "&smid=".$encode_member_id;
                }
            } */


        output_data(array('pglist' => $pointGoodsList, 'aplist' => $aplist, 'sc_list' => $sc_list));
    }

    public function pglistWt() {

        $city_id = intval($_POST['cityid']);
        $pointprod_model = Model('pointprod');
        //积分商品
        $where = array();
        $where['pgoods_show'] = 1;
        $where['pgoods_state'] = 0;
        $where['pgoods_commend'] = 1;
        $where['pgoods_endtime'] = array('gt', time());
        $where['city_id'] = $city_id;
        $recommend_pointsprod = $pointprod_model->getPointProdList($where, '*', 'pgoods_sort asc,pgoods_id desc', '', 8);
        $page_count = $pointprod_model->gettotalpage();
        output_data(array('pglist' => $recommend_pointsprod), mobile_page($page_count));
    }

    /**
     * 专题
     */
    public function specialWt() {
        $model_mb_special = Model('mb_special');
        $info = $model_mb_special->getMbSpecialInfoByID($_GET['special_id']);
        $list = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);
        $data = array_merge($info, array('list' => $list));
        $this->_output_special($data, $_GET['type'], $_GET['special_id']);
    }

    /**
     * 输出专题
     */
    private function _output_special($data, $type = 'json', $special_id = 0) {
        $model_special = Model('mb_special');
        if ($_GET['type'] == 'html') {
            $html_path = $model_special->getMbSpecialHtmlPath($special_id);
            if (!is_file($html_path)) {
                ob_start();
                Tpl::output('list', $data);
                Tpl::showpage('mb_special');
                file_put_contents($html_path, ob_get_clean());
            }
            header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
            die;
        } else {
            output_data($data);
        }
    }

    /**
     * android客户端版本号
     */
    public function apk_versionWt() {
        $version = C('mobile_apk_version');
        $url = C('mobile_apk');
        if (empty($version)) {
            $version = '';
        }
        if (empty($url)) {
            $url = '';
        }

        output_data(array('version' => $version, 'url' => $url));
    }

    /**
     * 默认搜索词列表
     */
    public function search_key_listWt() {
        $list = @explode(',', C('hot_search'));
        if (!$list || !is_array($list)) {
            $list = array();
        }
        if ($_COOKIE['hisSearch'] != '') {
            $his_search_list = explode('~', $_COOKIE['hisSearch']);
        }
        if (!$his_search_list || !is_array($his_search_list)) {
            $his_search_list = array();
        }
        output_data(array('list' => $list, 'his_list' => $his_search_list));
    }

    /**
     * 热门搜索列表
     */
    public function search_hot_infoWt() {
        if (C('rec_search') != '') {
            $rec_search_list = @unserialize(C('rec_search'));
        }
        $rec_search_list = is_array($rec_search_list) ? $rec_search_list : array();
        $result = $rec_search_list[array_rand($rec_search_list)];
        output_data(array('hot_info' => $result ? $result : array()));
    }

    /**
     * 高级搜索
     */
    public function search_showWt() {
        $area_list = Model('area')->getAreaList(array('area_deep' => 1), 'area_id,area_name');
        if (C('contract_allow') == 1) {
            $contract_list = Model('contract')->getContractItemByCache();
            $_tmp = array();
            $i = 0;
            foreach ($contract_list as $k => $v) {
                $_tmp[$i]['id'] = $v['cti_id'];
                $_tmp[$i]['name'] = $v['cti_name'];
                $i++;
            }
        }
        $max_goods_class = Model('goods_class')->getGoodsClassListByParentId(0);
        if (!empty($max_goods_class) && is_array($max_goods_class)) {
            foreach ($max_goods_class as $key => $val) {
                if ($val['is_show'] == 0) {
                    $goods_list[] = array('gc_id' => $val['gc_id'], 'gc_name' => $val['gc_name']);
                }
            }
        }
        output_data(array('area_list' => $area_list ? $area_list : array(), 'contract_list' => $_tmp, 'gclist' => $goods_list));
    }

    /**
     * 公告列表 shopwt 5.2
     */
    public function getggWt() {
        if (!empty($_GET['ac_id']) && intval($_GET['ac_id']) > 0) {
            $article_class_model = Model('article_class');
            $article_model = Model('article');
            $condition = array();

            $child_class_list = $article_class_model->getChildClass(intval($_GET['ac_id']));
            $ac_ids = array();
            if (!empty($child_class_list) && is_array($child_class_list)) {
                foreach ($child_class_list as $v) {
                    $ac_ids[] = $v['ac_id'];
                }
            }
            $ac_ids = implode(',', $ac_ids);
            $condition['ac_ids'] = $ac_ids;
            $condition['article_show'] = '1';
            $article_list = $article_model->getArticleList($condition, 5);
            //$article_type_name = $this->article_type_name($ac_ids);
            //output_data(array('article_list' => $article_list, 'article_type_name'=> $article_type_name));
            output_data(array('article_list' => $article_list));
        } else {
            output_error('缺少参数:文章类别编号');
        }
    }

    /**
     * 分销商品详情
     */
    public function goods_detailWt() {
        $goods_commonid = intval($_GET['goods_id']);
        $model_goods = Model('goods');
        $condition = array();
        $condition['goods_commonid'] = $goods_commonid;
        $goods = $model_goods->getGoodsInfo($condition);
        $goods_id = $goods['goods_id'];
        @header('Location: ' . WAP_SITE_URL . '/html/product_detail.html?goods_id=' . $goods_id);
        exit;
    }


    /**
     * 取广告内容
     *
     * @param unknown_type $ap_id
     *
     */
    public function getApList($ap_id) {
        if ($ap_id < 1) return array();
        $time = time();

        $ap_info = Model('show')->getApById($ap_id);
        if (!$ap_info)
            return array();

        $list = $ap_info['show_list'];
        unset($ap_info['show_list']);
        extract($ap_info);
        if ($is_use !== '1') {
            return array();
        }
        $show_list = array();

        foreach ((array)$list as $k => $v) {
            if ($v['show_start_date'] < $time && $v['show_end_date'] > $time && $v['is_allow'] == '1') {
                $show_list[] = $v;
            }
        }
        if (empty($show_list)) {
            return array();
        } else {
            $aplist = array();
            foreach ($show_list as $key => $ap) {
                $show_info = array();
                if ($ap_class == '0') {
                    //$width   = $ap_width;
                    //$height  = $ap_height;
                    $pic_content = unserialize($ap['show_content']);
                    $pic = $pic_content['show_pic'];
                    $url = $pic_content['show_pic_url'];
                    /* $content .= "<a href='http://".$pic_content['show_pic_url']."' target='_blank' title='".$show_title."'>";
                    $content .= "<img style='width:{$width}px;height:{$height}px' border='0' src='".UPLOAD_SITE_URL."/shop/common/loading.gif' data-url='";
                    $content .= UPLOAD_SITE_URL."/".ATTACH_SHOW."/".$pic;
                    $content .= "' alt='".$show_title."' rel='lazy'/>";
                    $content .= "</a>"; */
                    $show_info['show_title'] = $ap['show_title'];
                    $show_info['show_img'] = UPLOAD_SITE_URL . "/" . ATTACH_SHOW . "/" . $pic;
                    $show_info['show_url'] = 'http://' . $pic_content['show_pic_url'];
                    if ($ap['url_type'] == 1) {
                        $show_info['url'] = '/pages/index/newsdetail?aid=' . $ap['url_id'];
                    }
                    if ($ap['url_type'] == 2) {
                        $show_info['url'] = '/pages/store/index?store_id=' . $ap['url_id'];
                    }
                    if ($ap['url_type'] == 3) {
                        $show_info['url'] = '/pages/member/points/pinfo?id=' . $ap['url_id'];
                    }
                    $aplist[] = $show_info;
                }
            }
            return $aplist;

        }

    }


}
