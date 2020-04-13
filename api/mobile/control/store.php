<?php
/**
 * 店铺
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class storeControl extends mobileHomeControl {
    public function __construct() {
        parent::__construct();
    }

    //文章状态草稿箱
    const ARTICLE_STATE_DRAFT = 1;
    //文章状态待审核
    const ARTICLE_STATE_VERIFY = 2;
    //文章状态已发布
    const ARTICLE_STATE_PUBLISHED = 3;
    //文章状态回收站
    const ARTICLE_STATE_RECYCLE = 4;
    //推荐
    const COMMEND_FLAG_TRUE = 1;
    //文章评论类型
    const ARTICLE = 1;
    const PICTURE = 2;

    /**
     * 处理文章信息
     */
    private function _news_list_extend($news_id) {
        $model_article = Model('news_article');
        $article_detail = $model_article->getOne(array('article_id' => $news_id));
        return $article_detail;
    }

    /**
     * 店铺信息
     */
    public function store_infoWt() {

        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        } else {
            //$store_info = array($store_online_info);
        }
        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        $store_info['member_id'] = $store_online_info['member_id'];
        //shopwt添加QQ IM
        //$store_info['store_qq'] = $store_online_info['store_qq'];
        //$store_info['node_chat'] = C('node_chat');
        //$store_info['mb_store_decoration_switch'] = $store_online_info['mb_store_decoration_switch'];

        //是否开启装修


        //店铺导航
        $model_store_decoration = Model('mb_store_decoration');
        $store_decoration_info = $model_store_decoration->getStoreDecorationInfo(array('store_id' => $store_id));
        $store_info['store_imgname']['on'] = $store_decoration_info['mb_store_navigation'];
        //
        $store_nav = @unserialize($store_decoration_info['decoration_nav']);
        if (is_array($store_nav)) {
            foreach ($store_nav as $kt => $vt) {
                if ($vt['img']) {
                    $cca[$kt]['img'] = UPLOAD_SITE_URL . DS . ATTACH_STORE . DS . $vt['img'];
                    $cca[$kt]['name'] = $vt['name'];
                    $cca[$kt]['links'] = $vt['links'];
                } else {
                    $cca[$kt]['img'] = '';
                    $cca[$kt]['name'] = $vt['name'];
                    $cca[$kt]['links'] = $vt['links'];
                }

            }
        }


        $store_info['store_imgname']['data'] = $cca;


        // 店铺头像
        $store_info['store_avatar'] = $store_online_info['store_avatar']
            ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['store_avatar']
            : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');

        // 商品数
        //$store_info['goods_count'] = (int) $store_online_info['goods_count'];
        //评分
        $scores = Model('evaluate_store')->getEvaluateStoreInfoByStoreID($store_online_info['store_id']);
        $store_info['seval_scores'] = $scores['seval_scores'];

        if ($store_online_info['map_address_detail'] != '') {
            $store_info['map_address'] = $store_online_info['map_address_detail'];
        } else {
            $store_info['map_address'] = $store_online_info['map_address'];
        }
        $store_info['store_phone'] = $store_online_info['store_phone'];
        $store_info['consume_num'] = $store_online_info['consume_num'];
        $store_info['map_lng'] = $store_online_info['map_lng'];
        $store_info['map_lat'] = $store_online_info['map_lat'];
        //代金券
        $vcondition = array();
        $vcondition['voucher_t_end_date'] = array('gt', TIMESTAMP);
        $vcondition['voucher_t_state'] = 1;
        $vcondition['voucher_t_store_id'] = $store_online_info['store_id'];

        $vlist = Model('voucher')->getVoucherTemplateList($vcondition, '*', 2);
        //$voucher_list = $model_voucher->getVoucherTemplateList($where, '*', 20, 0, $order);
        $_vlist = array();
        if (!empty($vlist) && is_array($vlist)) {
            foreach ($vlist as $key => $val) {
                $v = array();
                $v['mtitle'] = $val['voucher_t_title'] . '  满' . floatval($val['voucher_t_limit']) . '减' . floatval($val['voucher_t_price']);
                $_vlist[] = $v;
            }
        }
        $store_info['vlist'] = $_vlist;
        $vlist2 = Model('voucher')->getVoucherTemplateList($vcondition, '*', 10);
        $_vlist = array();
        if (!empty($vlist2) && is_array($vlist2)) {
            foreach ($vlist2 as $key => $val) {
                $v = array();
                $v['voucher_t_title'] = $val['voucher_t_title'];
                $v['voucher_t_price'] = floatval($val['voucher_t_price']);
                $v['voucher_t_limit'] = floatval($val['voucher_t_limit']);
                $v['voucher_t_id'] = $val['voucher_t_id'];
                $v['valid_time'] = date('Y-m-d', $val['voucher_t_end_date']);
                $_vlist2[] = $v;
            }
        }
        $store_info['vtlist'] = $_vlist2;
        //配套设施
        $f_list = Model('facility')->getFacilityList(array('f_id' => array('in', $store_online_info['f_ids'])));
        if (!empty($f_list) && is_array($f_list)) {
            foreach ($f_list as $k => $val) {
                $val['img'] = UPLOAD_SITE_URL . DS . ATTACH_MOBILE . '/facility/' . $val['f_img'];
                $f_list[$k] = $val;
            }
        }
        $store_info['flist'] = $f_list;
        //营业时间
        $s_time = strtotime(date("y-m-d") . ' ' . $store_online_info['wtime_start']);
        $e_time = strtotime(date("y-m-d") . ' ' . $store_online_info['wtime_end']);
        $store_info['yy_state'] = false;
        if ($s_time < time() && time() < $e_time) {
            $store_info['yy_state'] = true;
        }
        //相册封面
        $plist = Model('photos')->getPhotosList(array('store_id' => $store_online_info['store_id']));
        if (!empty($plist) && is_array($plist)) {
            foreach ($plist as $k => $val) {
                if (!empty($val['ac_cover']) && $val['ac_cover'] != '') {
                    $val['img'] = cthumb($val['ac_cover'], 640, $store_online_info['store_id']);
                    $plist[$k] = $val;
                } else {
                    unset($plist[$k]);
                }
            }
        }
        $store_info['plist'] = $plist;

        // 店铺被收藏次数
        $store_info['store_collect'] = (int)$store_online_info['store_collect'];

        $is_favorate = false;
        // 如果已登录 判断该店铺是否已被收藏
        if ($memberId = $this->getMemberIdIfExists()) {
            $c = (int)Model('favorites')->getStoreFavoritesCountByStoreId($store_id, $memberId);
            $store_info['is_favorate'] = $c > 0;
            $is_favorate = $c > 0;
            //浏览历史存入数据库
            Model('store_browse')->saveViewedStore($memberId, $store_id);
        } else {
            $store_info['is_favorate'] = false;
        }


        // 页头背景图
        $store_info['mb_title_img'] = $store_online_info['mb_title_img']
            ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['mb_title_img']
            : '';

        // 轮播
        $store_info['mb_sliders'] = array();
        $mbSliders = @unserialize($store_online_info['mb_sliders']);
        if ($mbSliders) {
            foreach ((array)$mbSliders as $s) {
                if ($s['img']) {
                    $s['imgUrl'] = UPLOAD_SITE_URL . DS . ATTACH_STORE . DS . $s['img'];
                    $store_info['mb_sliders'][] = $s;
                }
            }
        }

        $eval_list = $this->evlist($store_id);
        $eval_num = Model('evaluate_store')->getEvaluateStoreCount(array('seval_storeid' => $store_id));
        $store_info['eval_num'] = $eval_num;
        $store_info['eval_manyi'] = floatval($store_info['seval_scores']) * 20.0;

        output_data(array('is_favorate' => $is_favorate, 'store_info' => $store_info, 'evaList' => $eval_list));
    }

    /**
     * 评价列表
     */
    protected function evlist($store_id) {
        $model_evaluate = Model('evaluate_store');
        //$is_reply = intval($_POST['type']);
        $condition = array();
        //$condition['is_reply'] = ($is_reply==1? 1:0);//0未回复，1已回复

        $condition['member_del'] = 0;
        $condition['seval_storeid'] = $store_id;
        $goodsevallist = $model_evaluate->getEvaluateStoreList($condition, 10);

        //$page_count = $model_evaluate->gettotalpage();
        $memberids = array();
        if (!empty($goodsevallist) && is_array($goodsevallist)) {
            foreach ($goodsevallist as $key => $val) {
                $memberids[] = $val['seval_memberid'];

            }

        }
        $memberList = array();
        if (!empty($memberids) && is_array($memberids)) {
            $member_list = Model('member')->getMemberList(array('member_id' => array('in', $memberids)));
            if (!empty($member_list) && is_array($member_list)) {
                foreach ($member_list as $val) {
                    $memberList[$val['member_id']] = $val;
                }
            }
        }
        $eval_list = array();
        if (!empty($goodsevallist) && is_array($goodsevallist)) {
            foreach ($goodsevallist as $key => $val) {
                $data = array();
                $data['seval_id'] = $val['seval_id'];
                $data['member_name'] = $memberList[$val['seval_memberid']]['nickname'];
                if ($val['seval_isanonymous'] == '1') {
                    $data['member_name'] = substr_cut_Show($data['member_name']);
                }
                //$data['store_id'] = $val['store_id'];
                $data['seval_scores'] = $val['seval_scores'];
                $data['seval_content'] = $val['seval_content'];
                $data['seval_explain'] = $val['seval_explain'] === null ? '' : $val['seval_explain'];
                // 头像
                $data['member_avatar'] = getMemberAvatarForID($memberList[$val['seval_memberid']]['member_id']);
                $data['date_txt'] = date('m月d日', $val['seval_addtime']);
                $data['time_txt'] = date('H:i:s', $val['seval_addtime']);

                $data['is_reply'] = $val['is_reply'];
                $data['seval_explain'] = $val['seval_explain'];
                $data['reply_date_txt'] = date('m月d日', $val['seval_explain_time']);
                $data['reply_time_txt'] = date('H:i:s', $val['seval_explain_time']);
                $data['reply_del'] = $val['reply_del'];

                // 评价晒图
                $geval_image_240 = array();
                $geval_image_1024 = array();
                if (!empty($val['seval_image'])) {
                    $image_array = explode(',', $val['seval_image']);
                    foreach ($image_array as $value) {
                        $geval_image_240[] = snsThumb($value, 240);
                        $geval_image_1024[] = snsThumb($value, 1024);
                    }
                }
                $data['geval_image_240'] = $geval_image_240;
                $data['geval_image_1024'] = $geval_image_1024;

                $eval_list[] = $data;
            }

        }

        return $eval_list;
    }


    /**
     * 所有评价列表
     */
    public function evallistWt() {
        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $model_evaluate = Model('evaluate_store');
        $condition = array();
        //$condition['is_reply'] = ($is_reply==1? 1:0);//0未回复，1已回复

        $condition['member_del'] = 0;
        $condition['seval_storeid'] = $store_id;
        $goodsevallist = $model_evaluate->getEvaluateStoreList($condition, 10);
        $page_count = $model_evaluate->gettotalpage();

        $memberids = array();
        if (!empty($goodsevallist) && is_array($goodsevallist)) {
            foreach ($goodsevallist as $key => $val) {
                $memberids[] = $val['seval_memberid'];

            }

        }
        $memberList = array();
        if (!empty($memberids) && is_array($memberids)) {
            $member_list = Model('member')->getMemberList(array('member_id' => array('in', $memberids)));
            if (!empty($member_list) && is_array($member_list)) {
                foreach ($member_list as $val) {
                    $memberList[$val['member_id']] = $val;
                }
            }
        }
        $eval_list = array();
        if (!empty($goodsevallist) && is_array($goodsevallist)) {
            foreach ($goodsevallist as $key => $val) {
                $data = array();
                $data['seval_id'] = $val['seval_id'];
                $data['member_name'] = $memberList[$val['seval_memberid']]['nickname'];
                if ($val['seval_isanonymous'] == '1') {
                    $data['member_name'] = substr_cut_Show($data['member_name']);
                }
                //$data['store_id'] = $val['store_id'];
                $data['seval_scores'] = $val['seval_scores'];
                $data['seval_content'] = $val['seval_content'];
                $data['seval_explain'] = $val['seval_explain'] === null ? '' : $val['seval_explain'];
                // 头像
                $data['member_avatar'] = getMemberAvatarForID($memberList[$val['seval_memberid']]['member_id']);
                $data['date_txt'] = date('m月d日', $val['seval_addtime']);
                $data['time_txt'] = date('H:i:s', $val['seval_addtime']);

                $data['is_reply'] = $val['is_reply'];
                $data['seval_explain'] = $val['seval_explain'];
                $data['reply_date_txt'] = date('m月d日', $val['seval_explain_time']);
                $data['reply_time_txt'] = date('H:i:s', $val['seval_explain_time']);
                $data['reply_del'] = $val['reply_del'];

                // 评价晒图
                $geval_image_240 = array();
                $geval_image_1024 = array();
                if (!empty($val['seval_image'])) {
                    $image_array = explode(',', $val['seval_image']);
                    foreach ($image_array as $value) {
                        $geval_image_240[] = snsThumb($value, 240);
                        $geval_image_1024[] = snsThumb($value, 1024);
                    }
                }
                $data['geval_image_240'] = $geval_image_240;
                $data['geval_image_1024'] = $geval_image_1024;

                $eval_list[] = $data;
            }

        }

        output_data(array('list' => $eval_list), mobile_page($page_count));
    }


    /**
     * 图片列表
     */
    public function album_pic_listWt() {
//        if (empty($_POST['id'])) {
//            showMessage(Language::get('album_parameter_error'), '', 'html', 'error');
//        }

        /**
         * 分页类
         */
        $page = new Page();
        $page->setEachNum(15);
        $page->setStyle('admin');

        /**
         * 实例化相册类
         */
        $model_album = Model('photos');

        $param = array();
        $param['ac_id'] = intval($_POST['id']);
        $param['photos_pic.store_id'] = intval($_POST['store_id']);
        if ($_GET['sort'] != '') {
            switch ($_GET['sort']) {
                case '0':
                    $param['order'] = 'add_time desc';
                    break;
                case '1':
                    $param['order'] = 'add_time asc';
                    break;
                case '2':
                    $param['order'] = 'ap_size desc';
                    break;
                case '3':
                    $param['order'] = 'ap_size asc';
                    break;
                case '4':
                    $param['order'] = 'ap_name desc';
                    break;
                case '5':
                    $param['order'] = 'ap_name asc';
                    break;
            }
        }
        $pic_list = $model_album->getPicList($param, $page);
        if ($pic_list) {
            foreach ($pic_list as $key => $value) {
                $pic_list[$key]['ap_cover'] = 'https://www.fhlego.com/system/upfiles/shop/store/goods/' . $value['store_id'] . '/' . $value['ap_cover'];
            }
        }
        //Tpl::output('pic_list', $pic_list);
        //Tpl::output('show_page', $page->show());

        /**
         * 相册列表，移动
         */
        $param = array();
        $param['photos_class.un_ac_id'] = intval($_POST['id']);
        $param['photos_aclass.store_id'] = intval($_POST['store_id']);
        $class_list = $model_album->getClassList($param);
//        Tpl::output('class_list', $class_list);
        /**
         * 相册信息
         */
        $param = array();
        $param['field'] = array('ac_id', 'store_id');
        $param['value'] = array(intval($_POST['id']), intval($_POST['store_id']));
        $class_info = $model_album->getOneClass($param);

        output_data(array('pic_list' => $pic_list, 'class_info' => $class_info));
//        Tpl::output('class_info', $class_info);
//
//        Tpl::output('PHPSESSID', session_id());
//        self::profile_menu('album_pic', 'pic_list');
//        Tpl::showpage('photos.pic_list');
    }

    public function eval_infoWt() {

        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }
        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        // 店铺头像
        $store_info['store_avatar'] = $store_online_info['store_avatar']
            ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['store_avatar']
            : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');

        //评分
        $scores = Model('evaluate_store')->getEvaluateStoreInfoByStoreID($store_online_info['store_id']);
        $store_info['seval_scores'] = $scores['seval_scores'];


        $eval_num = Model('evaluate_store')->getEvaluateStoreCount(array('seval_storeid' => $store_id));
        $store_info['eval_num'] = $eval_num;
        $store_info['eval_manyi'] = floatval($store_info['seval_scores']) * 20.0;

        output_data(array('store_info' => $store_info));
    }

    /**
     * 店铺简介
     */
    public function store_introWt() {

        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }
        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        $store_info['map_address'] = $store_online_info['map_address'];
        $store_info['store_phone'] = $store_online_info['store_phone'];
        $store_info['consume_num'] = $store_online_info['consume_num'];
        $store_info['map_lng'] = $store_online_info['map_lng'];
        $store_info['map_lat'] = $store_online_info['map_lat'];
        $store_info['store_desc'] = $store_online_info['store_desc'];

        //配套设施
        $f_list = Model('facility')->getFacilityList(array('f_id' => array('in', $store_online_info['f_ids'])));
        if (!empty($f_list) && is_array($f_list)) {
            foreach ($f_list as $k => $val) {
                $val['img'] = UPLOAD_SITE_URL . DS . ATTACH_MOBILE . '/facility/' . $val['f_img'];
                $f_list[$k] = $val;
            }
        }
        $store_info['flist'] = $f_list;
        //营业时间
        $store_info['yy_time_txt'] = $store_online_info['wtime_start'] . '--' . $store_online_info['wtime_end'];


        output_data(array('store_info' => $store_info));
    }


    /**
     * 获取文章相关文章
     */
    protected function get_article_link_list($article_link) {
        $article_link_list = array();
        if (!empty($article_link)) {
            $model_article = Model('news_article');
            $condition = array();
            $condition['article_id'] = array('in', $article_link);
            $condition['article_state'] = self::ARTICLE_STATE_PUBLISHED;
            $article_link_list = $model_article->getList($condition, NULL, 'article_id desc');
        }
        return $article_link_list;
    }

    /**
     * 文章处理
     */
    public function store_detailWt() {
        $article_id = intval($_GET['article_id']);

        $model_article = Model('news_article');
        $article_detail = $model_article->getOne(array('article_id' => $article_id));
        $article_detail['article_publish_time'] = date("Y-m-d ", time($article_detail['article_publish_time']));

        //相关文章 
        $article_link_list = $this->get_article_link_list($article_detail['article_link']);

        foreach ($article_link_list as $ke => $ve) {
            if ($ve['article_image']) {
                $cc = unserialize($ve['article_image']);
            } else {
                $cc['article_image'] = '';
            }
            $article_link_list[$ke]['article_publish_time'] = date("Y-m-d ", time($ve['article_publish_time']));
            if ($cc['article_image']) {
                $article_link_list[$ke]['image'] = getNEWSArticleImageUrl($ve['article_attachment_path'], $cc['article_image']['name']);
            } else {
                $article_link_list[$ke]['image'] = getNEWSArticleImageUrl($ve['article_attachment_path'], $ve['article_image']);
            }

        }

        //相关商品 
        $article_goods_list = unserialize($article_detail['article_goods']);


        //计数加1
        $model_article->modify(array('article_click' => array('exp', 'article_click+1')), array('article_id' => $article_id));


        $seo['html_title'] = $article_detail['article_title'] . '-' . C('news_seo_title') . '-' . C('site_name');
        $seo['seo_keywords'] = C('news_seo_keywords');
        $seo['seo_description'] = C('news_seo_description');

        output_data(array(
            'article_detail' => $article_detail,
            'detail_object_id' => $article_id,
            'article_link_list' => $article_link_list,
            'article_goods_list' => $article_goods_list,
            'seo_title' => $seo

        ));
    }

    /**
     * 店铺商品分类
     */
    public function store_goods_classWt() {
        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }

        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];

        $store_goods_class = Model('store_goods_class')->getStoreGoodsClassPlainList($store_id);

        output_data(array(
            'store_info' => $store_info,
            'store_goods_class' => $store_goods_class
        ));
    }

    /**
     * 店铺商品
     */
    public function store_goodsWt() {
        $param = $_REQUEST;

        $store_id = (int)$param['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $stc_id = (int)$param['stc_id'];
        $keyword = trim((string)$param['keyword']);

        $condition = array();
        $condition['store_id'] = $store_id;

        // 默认不显示预订商品
        $condition['is_book'] = 0;

        if ($stc_id > 0) {
            $condition['goods_stcids'] = array('like', '%,' . $stc_id . ',%');
        }
        //促销类型
        if ($param['prom_type']) {
            switch ($param['prom_type']) {
                case 'xianshi':
                    $condition['goods_sale_type'] = 2;
                    break;
                case 'robbuy':
                    $condition['goods_sale_type'] = 1;
                    break;
            }
        }
        if ($keyword != '') {
            $condition['goods_name'] = array('like', '%' . $keyword . '%');
        }
        $price_from = preg_match('/^[\d.]{1,20}$/', $param['price_from']) ? $param['price_from'] : null;
        $price_to = preg_match('/^[\d.]{1,20}$/', $param['price_to']) ? $param['price_to'] : null;
        if ($price_from && $price_from) {
            $condition['goods_sale_price'] = array('between', "{$price_from},{$price_to}");
        } elseif ($price_from) {
            $condition['goods_sale_price'] = array('egt', $price_from);
        } elseif ($price_to) {
            $condition['goods_sale_price'] = array('elt', $price_to);
        }

        // 排序
        $order = (int)$param['order'] == 1 ? 'asc' : 'desc';
        switch (trim($param['key'])) {
            case '1':
                $order = 'goods_id ' . $order;
                break;
            case '2':
                $order = 'goods_sale_price ' . $order;
                break;
            case '3':
                $order = 'goods_salenum ' . $order;
                break;
            case '4':
                $order = 'goods_collect ' . $order;
                break;
            case '5':
                $order = 'goods_click ' . $order;
                break;
            default:
                $order = 'goods_id desc';
                break;
        }

        $model_goods = Model('goods');

        $goods_fields = $this->getGoodsFields();
        $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $goods_fields, $order, $this->page);
        $page_count = $model_goods->gettotalpage();

        $goods_list = $this->_goods_list_extend($goods_list);

        output_data(array(
            'goods_list_count' => count($goods_list),
            'goods_list' => $goods_list,
        ), mobile_page($page_count));
    }

    private function getGoodsFields() {
        return implode(',', array(
            'goods_id',
            'goods_commonid',
            'store_id',
            'store_name',
            'goods_name',
            'goods_price',
            'goods_sale_price',
            'goods_sale_type',
            'goods_marketprice',
            'goods_image',
            'goods_salenum',
            'evaluation_good_star',
            'evaluation_count',
            'is_virtual',
            'is_presell',
            'is_fcode',
            'have_gift',
            'goods_addtime',
        ));
    }

    /**
     * 处理商品列表(抢购、限时折扣、商品图片)
     */
    private function _goods_list_extend($goods_list) {
        //获取商品列表编号数组
        $goodsid_array = array();
        foreach ($goods_list as $key => $value) {
            $goodsid_array[] = $value['goods_id'];
        }

        $sole_array = Model('p_sole')->getSoleGoodsList(array('goods_id' => array('in', $goodsid_array)));
        $sole_array = array_under_reset($sole_array, 'goods_id');

        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['sole_flag'] = false;
            $goods_list[$key]['group_flag'] = false;
            $goods_list[$key]['xianshi_flag'] = false;
            if (!empty($sole_array[$value['goods_id']])) {
                $goods_list[$key]['goods_price'] = $sole_array[$value['goods_id']]['sole_price'];
                $goods_list[$key]['sole_flag'] = true;
            } else {
                $goods_list[$key]['goods_price'] = $value['goods_sale_price'];
                switch ($value['goods_sale_type']) {
                    case 1:
                        $goods_list[$key]['group_flag'] = true;
                        break;
                    case 2:
                        $goods_list[$key]['xianshi_flag'] = true;
                        break;
                }

            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']);

            unset($goods_list[$key]['goods_sale_type']);
            unset($goods_list[$key]['goods_sale_price']);
            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['wt_distinct']);
        }

        return $goods_list;
    }

    /**
     * 商品评价
     */
    public function store_creditWt() {
        $store_id = intval($_GET['store_id']);
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }

        output_data(array('store_credit' => $store_online_info['store_credit']));
    }

    /**
     * 店铺商品排行
     */
    public function store_goods_rankWt() {
        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_data(array());
        }
        $ordertype = ($t = trim($_REQUEST['ordertype'])) ? $t : 'salenumdesc';
        $show_num = ($t = intval($_REQUEST['num'])) > 0 ? $t : 10;

        $where = array();
        $where['store_id'] = $store_id;
        // 默认不显示预订商品
        $where['is_book'] = 0;
        // 排序
        switch ($ordertype) {
            case 'salenumdesc':
                $order = 'goods_salenum desc';
                break;
            case 'salenumasc':
                $order = 'goods_salenum asc';
                break;
            case 'collectdesc':
                $order = 'goods_collect desc';
                break;
            case 'collectasc':
                $order = 'goods_collect asc';
                break;
            case 'clickdesc':
                $order = 'goods_click desc';
                break;
            case 'clickasc':
                $order = 'goods_click asc';
                break;
        }
        if ($order) {
            $order .= ',goods_id desc';
        } else {
            $order = 'goods_id desc';
        }
        $model_goods = Model('goods');
        $goods_fields = $this->getGoodsFields();
        $goods_list = $model_goods->getGoodsListByColorDistinct($where, $goods_fields, $order, 0, $show_num);
        $goods_list = $this->_goods_list_extend($goods_list);
        output_data(array('goods_list' => $goods_list));
    }

    /**
     * 店铺商品上新
     */
    public function store_new_goodsWt() {
        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_data(array('goods_list' => array()));
        }
        $show_day = ($t = intval($_REQUEST['show_day'])) > 0 ? $t : 30;
        $where = array();
        $where['store_id'] = $store_id;
        $where['is_book'] = 0;//默认不显示预订商品
        $stime = strtotime(date('Y-m-d', time() - 86400 * $show_day));
        $etime = $stime + 86400 * ($show_day + 1);
        $where['goods_addtime'] = array('between', array($stime, $etime));
        $order = 'goods_addtime desc, goods_id desc';
        $model_goods = Model('goods');
        $goods_fields = $this->getGoodsFields();
        $goods_list = $model_goods->getGoodsListByColorDistinct($where, $goods_fields, $order, $this->page);
        $page_count = $model_goods->gettotalpage();
        if ($goods_list) {
            $goods_list = $this->_goods_list_extend($goods_list);
            foreach ($goods_list as $k => $v) {
                $v['goods_addtime_text'] = $v['goods_addtime'] ? @date('Y年m月d日', $v['goods_addtime']) : '';
                $goods_list[$k] = $v;
            }
        }
        output_data(array('goods_list' => $goods_list), mobile_page($page_count));
    }

    /**
     * 店铺简介
     */
    public function store_intro_Wt() {
        $store_id = (int)$_REQUEST['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        if (empty($store_online_info)) {
            output_error('店铺不存在或未开启');
        }
        $store_info = $store_online_info;
        //开店时间
        $store_info['store_time_text'] = $store_info['store_time'] ? @date('Y-m-d', $store_info['store_time']) : '';
        // 店铺头像
        $store_info['store_avatar'] = $store_online_info['store_avatar']
            ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['store_avatar']
            : UPLOAD_SITE_URL . '/' . ATTACH_COMMON . DS . C('default_store_avatar');
        //商品数
        $store_info['goods_count'] = (int)$store_online_info['goods_count'];
        //店铺被收藏次数
        $store_info['store_collect'] = (int)$store_online_info['store_collect'];
        //店铺所属分类
        $store_class = Model('store_class')->getStoreClassInfo(array('sc_id' => $store_info['sc_id']));
        $store_info['sc_name'] = $store_class['sc_name'];
        //如果已登录 判断该店铺是否已被收藏
        if ($member_id = $this->getMemberIdIfExists()) {
            $c = (int)Model('favorites')->getStoreFavoritesCountByStoreId($store_id, $member_id);
            $store_info['is_favorate'] = $c > 0 ? true : false;
        } else {
            $store_info['is_favorate'] = false;
        }
        // 是否官方店铺
        $store_info['is_own_shop'] = (bool)$store_online_info['is_own_shop'];
        // 页头背景图
        $store_info['mb_title_img'] = $store_online_info['mb_title_img'] ? UPLOAD_SITE_URL . '/' . ATTACH_STORE . '/' . $store_online_info['mb_title_img'] : '';
        // 轮播
        $store_info['mb_sliders'] = array();
        $mbSliders = @unserialize($store_online_info['mb_sliders']);
        if ($mbSliders) {
            foreach ((array)$mbSliders as $s) {
                if ($s['img']) {
                    $s['imgUrl'] = UPLOAD_SITE_URL . DS . ATTACH_STORE . DS . $s['img'];
                    $store_info['mb_sliders'][] = $s;
                }
            }
        }
        output_data(array('store_info' => $store_info));
    }

    /**
     * 店铺促销活动
     */
    public function store_saleWt() {
        $param = $_REQUEST;
        $store_id = (int)$param['store_id'];
        if ($store_id <= 0) {
            output_error('参数错误');
        }
        $fields_arr = array('mansong', 'xianshi');
        $fields_str = trim($param['fields']);
        if ($fields_str) {
            $fields_arr = explode(',', $fields_str);
        }
        $sale_arr = array();
        if (in_array('mansong', $fields_arr)) {
            //满就送
            $mansong_info = Model('p_mansong')->getMansongInfoByStoreID($store_id);
            if ($mansong_info) {
                $mansong_info['start_time_text'] = date('Y-m-d', $mansong_info['start_time']);
                $mansong_info['end_time_text'] = date('Y-m-d', $mansong_info['end_time']);
                foreach ($mansong_info['rules'] as $rules_k => $rules_v) {
                    $rules_v['goods_image_url'] = cthumb($rules_v['goods_image'], 60);
                    $rules_v['price'] = wtPriceFormat($rules_v['price']);
                    if ($rules_v['discount']) {
                        $rules_v['discount'] = wtPriceFormat($rules_v['discount']);
                    }
                    $mansong_info['rules'][$rules_k] = $rules_v;
                }
                $sale_arr['mansong'] = $mansong_info;
            }
        }
        if (in_array('xianshi', $fields_arr)) {
            //限时折扣
            $where = array();
            $where['store_id'] = $store_id;
            $where['state'] = 1;
            $where['start_time'] = array('elt', TIMESTAMP);
            $where['end_time'] = array('egt', TIMESTAMP);
            $xianshi_list = Model('p_xianshi')->getXianshiList($where, 0, 'xianshi_id asc', '*', 1);
            if ($xianshi_list) {
                $xianshi_info = $xianshi_list[0];
                $xianshi_info['start_time_text'] = date('Y-m-d', $xianshi_info['start_time']);
                $xianshi_info['end_time_text'] = date('Y-m-d', $xianshi_info['end_time']);
                $sale_arr['xianshi'] = $xianshi_info;
            }
        }
        output_data(array('sale' => $sale_arr));
    }
}
