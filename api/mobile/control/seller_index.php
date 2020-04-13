<?php
/**
 * 商家首页
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class seller_indexControl extends mobileSellerControl
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 商家中心
     */
    public function indexWt()
    {
        $seller_info = array();
        $seller_info = $this->seller_info;
        $store_info = $this->store_info;

        //最后登陆
        $seller_info['last_login_time_fmt'] = date('Y-m-d H:i:s', $seller_info['last_login_time']);
        //$model_order = Model('order');
        // 待付款
        //$seller_info['order_nopay_count'] = $model_order->getOrderCountByID('store',$store_info['store_id'],'NewCount');
        // 待发货
        //$seller_info['order_noreceipt_count'] = $model_order->getOrderCountByID('store',$store_info['store_id'],'PayCount');

        $store_temp = Model('store')->getStoreInfoByID($this->store_info['store_id']);

        $store_info['a_predeposit'] = $store_temp['available_predeposit'];
        $store_info['f_predeposit'] = $store_temp['freeze_predeposit'];
        $store_info['sell_amount'] = $store_temp['sell_amount'];

        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['payment_time'] = array('gt', strtotime(date('Y-m-d', time())));
        $tody_amount = Model('store_order')->getOrderSum($where);
        $store_info['sell_today_amount'] = empty($tody_amount) ? '0.00' : $tody_amount;


        //店铺头像
        $store_info['store_avatar'] = getStoreLogo($store_info['store_avatar'], 'store_avatar');
        //店铺标志
        // $store_info['store_label'] = getStoreLogo($store_info['store_label'], 'store_logo');
        //等级信息
        //$store_info['grade_name'] = $this->store_grade['sg_name'];
        //商品数量限制
        //$store_info['grade_goodslimit'] = $this->store_grade['sg_goods_limit'];
        //图片空间数量限制
        //$store_info['grade_albumlimit'] = $this->store_grade['sg_album_limit'];

        /**
         * 销售情况统计
         */
        /*   $field = 'COUNT(*) as ordernum,SUM(order_amount) as orderamount';
          $where = array();
          $where['store_id'] = $this->store_info['store_id'];
          //有效订单
          $where['order_isvalid'] = 1;
          //昨日销量
          $where['order_add_time'] = array('between',array(strtotime(date('Y-m-d',(time()-3600*24))),strtotime(date('Y-m-d',time()))-1));
          $daily_sales = Model('stat')->getoneByStatorder($where, $field);
          //月销量
          $where['order_add_time'] = array('gt',strtotime(date('Y-m',time())));
          $monthly_sales = Model('stat')->getoneByStatorder($where, $field);
          $store_info['daily_sales'] = $daily_sales;
          $store_info['monthly_sales'] = $monthly_sales;

          //统计
          $statics = $this->getStatics();
          $statnew_arr = $this->general(); */
        /***公告开始***/
        $ac_id = 2;
        $article_class_model = Model('article_class');
        $article_model = Model('article');
        $condition = array();

        $child_class_list = $article_class_model->getChildClass($ac_id);
        $ac_ids = array();
        $art_class = array();
        if (!empty($child_class_list) && is_array($child_class_list)) {
            foreach ($child_class_list as $v) {
                $ac_ids[] = $v['ac_id'];
                $art_class[$v['ac_id']] = $v;
            }
        }
        $ac_ids = implode(',', $ac_ids);
        $condition['ac_ids'] = $ac_ids;
        $condition['article_show'] = '1';
        $_artlist = $article_model->getArticleList($condition, 6);
        $artlist = array();
        if (!empty($_artlist) && is_array($_artlist)) {
            foreach ($_artlist as $v) {
                $data = array();
                $data['article_id'] = $v['article_id'];
                $data['ac_id'] = $v['ac_id'];
                $data['ac_name'] = $art_class[$v['ac_id']]['ac_name'];
                $data['article_title'] = $v['article_title'];
                $data['article_time'] = $v['article_time'];
                $data['article_time_txt'] = date('m-d', $v['article_time']);
                $artlist[] = $data;
            }
        }
        /***公告结束***/
        //提现方式
        $isSet_tx = array();
        $isSet_tx['alipay'] = 0;
        $isSet_tx['wxpay'] = 0;
        $txinfo = Model('txway')->getInfo(array('store_id' => $this->store_info['store_id'], 'bill_type_code' => 'alipay', 'type' => 2));
        if (!empty($txinfo) && is_array($txinfo)) {
            $isSet_tx['alipay'] = 1;
        }
        $txinfo = Model('txway')->getInfo(array('store_id' => $this->store_info['store_id'], 'bill_type_code' => 'wxpay', 'type' => 2));
        if (!empty($txinfo) && is_array($txinfo)) {
            $isSet_tx['wxpay'] = 1;
        }

        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['is_read'] = '0';
        $model_storemsg = Model('store_msg');
        $sysnum = $model_storemsg->getStoreMsgCount($where);

        output_data(array('store_info' => $store_info, 'artlist' => $artlist, 'sysnum' => $sysnum, 'setbill' => $isSet_tx));
    }


    //更改所在地区
    public function changeAreaWt()
    {
        $condition = array('store_id' => $this->store_info['store_id']);
        $param = array();
        $param['province_id'] = intval($_POST['province_id']);
        $param['city_id'] = intval($_POST['city_id']);
        $param['area_id'] = intval($_POST['area_id']);
        $param['area_info'] = $_POST['area_info'];

        $agent = Model('agent_area')->getAgentAreaInfo(array('area_id' => $param['area_id']));
        if (empty($agent) || !is_array($agent)) {
            output_error('请先选择地区！');
        }
        $agent = Model('store')->editStore($param, $condition);

        output_data(array('res' => $agent, 'con' => $condition));
    }

    public function modnameWt()
    {
        $seller_info = $this->seller_info;
        $store_info = $this->store_info;
        $num = 0;
        $state = 0;
        $year = strtotime(date('Y-01-01 00:00:01'));
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['addtime'] = array('egt', $year);
        $info = Model('store_modify')->where($condition)->order(' id desc')->find();
        if (!empty($info) && is_array($info)) {
            $num = intval($info['curr_num']);
            if ($info['state'] == 0) {
                $state = 1;
            }
        }
        $allnum = 4;
        $alownum = 4 - $num;
        output_data(array('store_name' => $store_info['store_name'], 'alownum' => $alownum, 'allnum' => $allnum, 'state' => $state, 'newname' => $info['store_name']));
    }

    public function modname_saveWt()
    {
        $store_id = $this->store_info['store_id'];
        $store_name = $this->store_info['store_name'];
        if (!empty($_POST)) {
            $year = strtotime(date('Y-01-01 00:00:01'));
            $condition = array();
            $condition['store_id'] = $store_id;
            $condition['addtime'] = array('egt', $year);
            $info = Model('store_modify')->where($condition)->order(' id desc')->find();
            $num = 0;
            if (!empty($info) && is_array($info)) {
                $num = intval($info['curr_num']);
                if ($info['state'] == 0) {
                    output_error('店铺正在审核中...', array('state' => 0));
                } else if ($num >= 4) {
                    output_error('本年度最多只能修改4次', array('state' => 0));
                }
            }
            $model_store = Model('store');
            $store_info = $model_store->getStoreInfo(array('store_name' => $_POST['store_name']));
            if (!empty($store_info['store_name']) && $store_info['store_id'] != $store_id) {
                output_error('店铺名称已经存在', array('state' => 0));
            }

            $param = array();
            $param['store_id'] = $store_id;
            $param['store_name'] = $_POST['store_name'];
            $param['zhizhao_pic'] = trim($_POST['zhizhao_pic']);
            $param['pingzheng_pic'] = trim($_POST['pingzheng_pic']);
            $param['addtime'] = time();
            $param['state'] = 0;
            $param['store_name_old'] = $store_name;
            $param['curr_num'] = $num + 1;

            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input" => $param['store_name'], "require" => "true", "validator" => "Length", "min" => "1", "max" => "50", "message" => "店铺名称不能为空且必须小于50个字"),
                array("input" => $param['zhizhao_pic'], "require" => "true", "message" => "执照不能为空"),
                array("input" => $param['pingzheng_pic'], "require" => "true", "message" => "申请凭证不能为空"),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                output_error($error, array('state' => 0));
            }
            $ret = Model('store_modify')->add($param);
            if ($ret) {
                output_data(1);
            } else {
                output_error('申请修改失败', array('state' => 0));
            }

        }

    }


    public function updatemapWt()
    {
        $seller_info = $this->seller_info;
        $store_info = $this->store_info;

        $lat = trim($_POST['lat']);
        $lng = trim($_POST['lng']);
        $address = trim($_POST['address']);
        $geohash = new Geohash();
        $n_geohash = $geohash->encode($lat, $lng);

        Model('store')->editStore(array('map_lng' => $lng, 'map_lat' => $lat, 'map_address' => $address, 'geohash' => $n_geohash), array('store_id' => $store_info['store_id']));


        output_data(1);
    }

    public function updatemapdeatilWt()
    {
        $seller_info = $this->seller_info;
        $store_info = $this->store_info;

        $address_detail = trim($_POST['address_detail']);

        Model('store')->editStore(array('map_address_detail' => $address_detail), array('store_id' => $store_info['store_id']));


        output_data(1);
    }


    public function getstoreintroWt()
    {
        $seller_info = $this->seller_info;


        $store_info = Model('store')->getStoreInfoByID($this->store_info['store_id']);
        if (!empty($store_info) && is_array($store_info)) {
            $data = array();
            $data['store_phone'] = $store_info['store_phone'];
            $data['store_desc'] = $store_info['store_desc'];
            $data['f_ids'] = explode(",", $store_info['f_ids']);
            $data['wtime_start'] = $store_info['wtime_start'];
            $data['wtime_end'] = $store_info['wtime_end'];
            $data['map_address_detail'] = $store_info['map_address_detail'];
            $data['map_address'] = $store_info['map_address'];

            $store_class = Model('store_class')->getStoreClassInfo(array('sc_id' => intval($store_info['scid2'])));
            if (!empty($store_class) && $store_class['facility_ids'] != '') {
                //$fc_list = Model('facility_class')->getFacilityClassList(array('fc_id'=>array('in',$store_class['facility_ids'])));
                $f_list = Model('facility')->getFacilityList(array('fc_id' => array('in', $store_class['facility_ids'])));
                $flist = array();
                if (!empty($f_list) && is_array($f_list)) {
                    foreach ($f_list as $v) {
                        $is_check = 0;
                        if (in_array($v['f_id'], $data['f_ids'])) {
                            $is_check = 1;
                        }
                        $flist[] = array('fid' => $v['f_id'], 'fname' => $v['f_name'], 'ischeck' => $is_check);
                    }
                }
                //$fclist= array();
                /* if(!empty($fc_list)&& is_array($fc_list)){
                    foreach($fc_list as $v){
                        $fclist[$v['fc_id']] = $v;
                        $fclist[$v['fc_id']]['flist'] = $flist[$v['fc_id']];
                    }
                } */
                //Tpl::output('fc_list',$fclist);
            }


            output_data(array('store' => $data, 'flist' => $flist));
        } else {
            output_error(0);
        }
    }

    public function storeintro_saveWt()
    {
        $seller_info = $this->seller_info;

        $data = array();
        $data['store_phone'] = $_POST['store_phone'];
        $data['store_desc'] = $_POST['store_desc'];
        $data['wtime_start'] = $_POST['wtime_start'];
        $data['wtime_end'] = $_POST['wtime_end'];
        $data['f_ids'] = trim($_POST['fids']);
        //$data['f_ids']= implode(",", $_POST['fids']);
        //$data['store_workingtime']= $store_info['store_workingtime'];
        $ret = Model('store')->editStore($data, array('store_id' => $this->store_info['store_id']));

        if ($ret) {
            output_data(1);
        } else {
            output_error(0);
        }
    }

    public function payrqcodeWt()
    {
        $store_id = $this->store_info['store_id'];
        $store_temp = Model('store')->getStoreInfoByID($this->store_info['store_id']);

        $rqcode_src = UPLOAD_SITE_URL . DS . ATTACH_STORE . DS . $store_id . DS . $store_id . '_pay.png';
        $imgfile = BASE_UPLOAD_PATH . DS . ATTACH_STORE . DS . $store_id . DS . $store_id . '_pay.png';
        if (!file_exists($imgfile)) {
            require_once(BASE_STATIC_PATH . DS . 'phpqrcode' . DS . 'index.php');
            //生成店铺支付二维码
            $PhpQRCode = new PhpQRCode();
            $PhpQRCode->set('pngTempDir', BASE_UPLOAD_PATH . DS . ATTACH_STORE . DS . $store_id . DS);
            $PhpQRCode->set('date', WAP_SITE_URL . '/index.php?pay_sid=' . $store_id);
            $PhpQRCode->set('matrixPointSize', 10);
            $PhpQRCode->set('errorCorrectionLevel', 'H');
            $PhpQRCode->set('pngTempName', $store_id . '_pay.png');
            $PhpQRCode->init();
        }
        $data = array();
        $data['rqcode_src'] = $rqcode_src;
        $data['store_name'] = $store_temp['store_name'];
        $data['address'] = $store_temp['area_info'];//.' '.$store_temp['store_address'];
        $data['store_avatar'] = getStoreLogo($store_temp['store_avatar'], 'store_avatar');
        output_data(array('store_info' => $data));
    }

    public function sellerstatWt()
    {
        $seller_info = array();
        $seller_info = $this->seller_info;
        $store_info = $this->store_info;

        //最后登陆
        $seller_info['last_login_time_fmt'] = date('Y-m-d H:i:s', $seller_info['last_login_time']);
        //$model_order = Model('order');
        // 待付款
        //$seller_info['order_nopay_count'] = $model_order->getOrderCountByID('store',$store_info['store_id'],'NewCount');
        // 待发货
        //$seller_info['order_noreceipt_count'] = $model_order->getOrderCountByID('store',$store_info['store_id'],'PayCount');
        $model_order = Model('store_order');
        $store_temp = Model('store')->getStoreInfoByID($this->store_info['store_id']);
        $ordernum = $model_order->getOrderCount(array('store_id' => $this->store_info['store_id'], 'order_state' => array('gt', 10)));
        $membernum = $model_order->getOrderCountByMember(array('store_id' => $this->store_info['store_id'], 'order_state' => array('gt', 10)));

        $store_info['a_predeposit'] = $store_temp['available_predeposit'];
        $store_info['f_predeposit'] = $store_temp['freeze_predeposit'];
        $store_info['sell_amount'] = $store_temp['sell_amount'];
        $store_info['ordernum'] = $ordernum;
        $store_info['membernum'] = $membernum;

        //店铺头像
        $store_info['store_avatar'] = getStoreLogo($store_info['store_avatar'], 'store_avatar');

        /**
         * 销售情况统计
         */
        /*   $field = 'COUNT(*) as ordernum,SUM(order_amount) as orderamount';
          $where = array();
          $where['store_id'] = $this->store_info['store_id'];
          //有效订单
          $where['order_isvalid'] = 1;
          //昨日销量
          $where['order_add_time'] = array('between',array(strtotime(date('Y-m-d',(time()-3600*24))),strtotime(date('Y-m-d',time()))-1));
          $daily_sales = Model('stat')->getoneByStatorder($where, $field);
          //月销量
          $where['order_add_time'] = array('gt',strtotime(date('Y-m',time())));
          $monthly_sales = Model('stat')->getoneByStatorder($where, $field);
          $store_info['daily_sales'] = $daily_sales;
          $store_info['monthly_sales'] = $monthly_sales;

          //统计
          $statics = $this->getStatics();
          $statnew_arr = $this->general(); */

        output_data(array('store_info' => $store_info));
    }

    public function getPhoneWt()
    {

        $store_info = Model('store')->getStoreInfoByID($this->store_info['store_id']);
        $phone_show = encryptShow($store_info['shop_phone'], 4, 4);
        $phone = $store_info['shop_phone'];
        output_data(array('phone_show' => $phone_show, 'phone' => $phone));
    }


    /**
     * 修改绑定手机号码
     */
    public function editPhoneWt()
    {
        $phone = $_POST['phone'];
        $captcha2 = $_POST['yzcode2'];

        $oldphone = $_POST['oldphone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];

        if ($phone == $oldphone) {
            output_error('新修改的号码跟旧号码相同！');
        }

        if (!preg_match('/^0?(13|15|17|18|19|14)[0-9]{9}$/i', $phone)) {
            output_error('手机号码不正确！');
        }
        if (!preg_match('/^0?(13|15|17|18|19|14)[0-9]{9}$/i', $oldphone)) {
            output_error('手机号码不正确！');
        }
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input" => $captcha2, "require" => "true", "validator" => "Length", "min" => "4", "max" => "6", "message" => "验证码不正确"),
            array("input" => $captcha, "require" => "true", "validator" => "Length", "min" => "4", "max" => "6", "message" => "验证码不正确！"),
        );
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error, array('state' => 0));
        }


        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($oldphone, $captcha, $log_type);
        $state_data2 = $handle_connect_api->checkSmsCaptcha($phone, $captcha2, $log_type);

        if ($state_data['state'] && $state_data2['state']) {
            $store_model = Model('store');
            $storeInfo = $store_model->getStoreInfo(array('shop_phone' => $phone));
            if (!empty($storeInfo) && $storeInfo['store_id'] != $this->store_info['store_id']) {
                output_error('该号码已经存在，请换其他号码！');
            }
            $condition = array();
            $condition['store_id'] = $this->store_info['store_id'];
            $condition['shop_phone'] = $oldphone;

            $ret = $store_model->editStore(array('shop_phone' => $phone), $condition);

            if ($ret) {
                output_data(array('state' => 1, 'tk' => $yz));
            } else {
                output_error('验证失败！');
            }

        } else {
            output_error('验证信息错误');
        }
    }


    /**
     * 更改密码
     */
    public function modify_pwdWt()
    {

        if (!$_POST['opwd'] || $_POST['opwd'] == '') {
            output_error('请输入旧密码');
        }
        if (!$_POST['newpwd'] || !$_POST['newpwd2'] || $_POST['newpwd'] != $_POST['newpwd2']) {
            output_error('两次输入的新密码不一致');
        }

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('store_id' => $this->store_info['store_id']));

        if ($seller_info['seller_pwd'] != md5($_POST['opwd'])) {
            output_error('输入的旧密码不正确!');
        }
        $update = $model_seller->editSeller(array('seller_pwd' => md5($_POST['newpwd'])), array('store_id' => $this->store_info['store_id']));
        if (!$update) {
            output_error('新密码修改失败');
        }

        output_data('1');
    }

    /**
     * 验证旧支付密
     */
    public function check_ppwdWt()
    {

        if (!$_POST['oppwd'] || $_POST['oppwd'] == '') {
            output_error('请输入旧支付密码');
        }

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('store_id' => $this->store_info['store_id']));

        if ($seller_info['seller_ppwd'] != md5($_POST['oppwd'])) {
            output_error('输入的旧密码不正确!');
        }


        output_data('1');

    }

    /**
     * 更改支付密码
     */
    public function modify_ppwdWt()
    {

        if (!$_POST['opwd'] || $_POST['opwd'] == '') {
            output_error('请输入旧密码');
        }
        if (!$_POST['newpwd'] || !$_POST['newpwd2'] || $_POST['newpwd'] != $_POST['newpwd2']) {
            output_error('两次输入的新密码不一致');
        }

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('store_id' => $this->store_info['store_id']));

        if ($seller_info['seller_ppwd'] != md5($_POST['opwd'])) {
            output_error('输入的旧密码不正确!');
        }
        $update = $model_seller->editSeller(array('seller_ppwd' => md5($_POST['newpwd'])), array('store_id' => $this->store_info['store_id']));
        if (!$update) {
            output_error('新密码修改失败');
        }

        output_data('1');

    }

    public function getsellersetWt()
    {

        $store_info = Model('store')->getStoreInfoByID($this->store_info['store_id']);
        $phone_show = encryptShow($store_info['shop_phone'], 4, 4);
        $phone = $store_info['shop_phone'];

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('store_id' => $this->store_info['store_id']));
        $ppset = 1;
        if ($seller_info['seller_ppwd'] == '' || $seller_info['seller_ppwd'] == 'dd') {
            $ppset = 0;
        }

        output_data(array('phone_show' => $phone_show, 'phone' => $phone, 'ppset' => $ppset));
    }


    /**
     * 新设置或手机设置更改支付密码
     */
    public function find_sellerppwdWt()
    {

        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];

        if (empty($_POST['yzcode']) || $_POST['yzcode'] == '') {
            output_error('安全验证失败！');
        }
        if (!preg_match('/^0?(13|15|17|18|19|14)[0-9]{9}$/i', $phone)) {
            output_error('手机安全验证失败！');
        }
        if (empty($_POST['ppwd']) || $_POST['ppwd'] == '') {
            output_error('请输入支付密码');
        }
        if (!$_POST['ppwd'] || !$_POST['ppwd2'] || $_POST['ppwd'] != $_POST['ppwd2']) {
            output_error('两次输入的支付密码不一致');
        }

        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        if ($state_data['state']) {
            $model_seller = Model('seller');
            $seller_info = $model_seller->getSellerInfo(array('store_id' => $this->store_info['store_id']));

            $update = $model_seller->editSeller(array('seller_ppwd' => md5($_POST['ppwd'])), array('store_id' => $this->store_info['store_id']));

            if (!$update) {
                output_error('新支付密码设置失败');
            }
            output_data('1');
        } else {
            output_error('验证信息错误');
        }

    }

    /*
    *店铺上线/下线操作
    */
    public function editshowWt()
    {
        $store_id = $this->store_info['store_id'];
        if ($_POST['isshow'] == 1) {

            $store_temp = Model('store')->getStoreInfoByID($store_id);
            if ($store_temp['map_lng'] == '' || $store_temp['map_lat'] == '' || $store_temp['store_phone'] == '' || $store_temp['store_desc'] == '' || $store_temp['wtime_start'] == '' || $store_temp['wtime_end'] == '' || $store_temp['f_ids'] == '') {
                output_error('请先设置店铺简介、店铺号码、营业时间、配套设置、地理位置及图片');
                return false;
            }
            //提现方式
            $txinfo = Model('txway')->getInfo(array('store_id' => $store_id, 'bill_type_code' => 'alipay', 'type' => 2));
            if (empty($txinfo) || !is_array($txinfo)) {
                output_error('请先设置支付宝提现方式');
                return false;
            }
            $txinfo = Model('txway')->getInfo(array('store_id' => $store_id, 'bill_type_code' => 'wxpay', 'type' => 2));
            if (empty($txinfo) || !is_array($txinfo)) {
                output_error('请先设置微信提现方式');
                return false;
            }

            Model('store')->editStore(array('isshow' => 1), array('store_id' => $store_id));
            output_data(array('status' => 1, 'info' => '操作成功!'));

        } else if ($_POST['isshow'] == 0) {

            Model('store')->editStore(array('isshow' => 0), array('store_id' => $store_id));
            output_data(array('status' => 1, 'info' => '操作成功!'));

        }
    }


    /*================================================================*/
    public function order_listWt()
    {
        $model_order = Model('order');

        $order_list = $model_order->getStoreOrderList(
            $this->store_info['store_id'],
            $_POST['order_sn'],
            $_POST['buyer_name'],
            $_POST['state_type'],
            $_POST['query_start_date'],
            $_POST['query_end_date'],
            $_POST['skip_off'],
            '*',
            array('order_goods')
        );

        $page_count = $model_order->gettotalpage();

        output_data(array('order_list' => $order_list), mobile_page($page_count));
    }

    /**
     * 取消订单
     */
    public function order_cancelWt()
    {
        $order_id = intval($_POST['order_id']);
        $reason = $_POST['reason'];
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition);

        $if_allow = $model_order->getOrderOperateState('store_cancel', $order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }

        if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
            $_hour = ceil(($order_info['api_pay_time'] + 86400 - TIMESTAMP) / 3600);
            output_error('该订单曾尝试使用第三方支付平台支付，须在' . $_hour . '小时以后才可取消');
        }

        if ($order_info['order_type'] == 2) {
            //预定订单
            $result = Handle('order_book')->changeOrderStateCancel($order_info, 'seller', $this->seller_info['seller_name'], $reason);
        } else {
            $cancel_condition = array();
            if ($order_info['payment_code'] != 'offline') {
                $cancel_condition['order_state'] = ORDER_STATE_NEW;
            }
            $result = Handle('order')->changeOrderStateCancel($order_info, 'seller', $this->seller_info['seller_name'], $reason, true, $cancel_condition);
        }

        if (!$result['state']) {
            output_error($result['msg']);
        }
        output_data('1');
    }

    /**
     * 修改运费
     */
    public function order_ship_priceWt()
    {
        $order_id = intval($_POST['order_id']);
        $shipping_fee = wtPriceFormat($_POST['shipping_fee']);
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition);

        $if_allow = $model_order->getOrderOperateState('modify_price', $order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }
        $result = Handle('order')->changeOrderShipPrice($order_info, 'seller', $this->seller_info['seller_name'], $shipping_fee);

        if (!$result['state']) {
            output_error($result['msg']);
        }
        output_data('1');
    }

    /**
     * 发货
     */
    public function order_deliver_sendWt()
    {
        $order_id = intval($_POST['order_id']);
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition, array('order_common', 'order_goods'));
        $if_allow_send = intval($order_info['lock_state']) || !in_array($order_info['order_state'], array(ORDER_STATE_PAY, ORDER_STATE_SEND));
        if ($if_allow_send) {
            output_error('无权操作');
        }

        $result = Handle('order')->changeOrderSend($order_info, 'seller', $this->seller_info['seller_name'], $_POST);
        if (!$result['state']) {
            output_error($result['msg']);
        }
        output_data('1');
    }

    /**
     * 取得卖家统计类信息
     *
     */
    private function getStatics()
    {
        $add_time_to = strtotime(date("Y-m-d") + 60 * 60 * 24);   //当前日期 ,从零点来时
        $add_time_from = strtotime(date("Y-m-d", (strtotime(date("Y-m-d")) - 60 * 60 * 24 * 30)));   //30天前
        $goods_online = 0;      // 出售中商品
        $goods_waitverify = 0;  // 等待审核
        $goods_verifyfail = 0;  // 审核失败
        $goods_offline = 0;     // 仓库待上架商品
        $goods_lockup = 0;      // 违规下架商品
        $consult = 0;           // 待回复商品咨询
        $no_payment = 0;        // 待付款
        $no_delivery = 0;       // 待发货
        $no_receipt = 0;        // 待收货
        $refund_lock = 0;      // 售前退款
        $refund = 0;            // 售后退款
        $return_lock = 0;      // 售前退货
        $return = 0;            // 售后退货
        $complain = 0;          //进行中投诉

        $model_goods = Model('goods');
        // 全部商品数
        $goodscount = $model_goods->getGoodsCommonCount(array('store_id' => $this->store_info['store_id']));
        // 出售中的商品
        $goods_online = $model_goods->getGoodsCommonOnlineCount(array('store_id' => $this->store_info['store_id']));
        if (C('goods_verify')) {
            // 等待审核的商品
            $goods_waitverify = $model_goods->getGoodsCommonWaitVerifyCount(array('store_id' => $this->store_info['store_id']));
            // 审核失败的商品
            $goods_verifyfail = $model_goods->getGoodsCommonVerifyFailCount(array('store_id' => $this->store_info['store_id']));
        }
        // 仓库待上架的商品
        $goods_offline = $model_goods->getGoodsCommonOfflineCount(array('store_id' => $this->store_info['store_id']));
        // 违规下架的商品
        $goods_lockup = $model_goods->getGoodsCommonLockUpCount(array('store_id' => $this->store_info['store_id']));
        // 等待回复商品咨询
        if (C('dbdriver') == 'mysql') {
            $consult = Model('consult')->getConsultCount(array('store_id' => $this->store_info['store_id'], 'consult_reply' => ''));
        } else {
            $consult = Model('consult')->getConsultCount(array('store_id' => $this->store_info['store_id'], 'consult_reply' => array('exp', 'consult_reply IS NULL')));
        }

        // 商品图片数量
        $imagecount = Model('album')->getAlbumPicCount(array('store_id' => $this->store_info['store_id']));

        $model_order = Model('order');
        // 交易中的订单
        $progressing = $model_order->getOrderCountByID('store', $this->store_info['store_id'], 'TradeCount');
        // 待付款
        $no_payment = $model_order->getOrderCountByID('store', $this->store_info['store_id'], 'NewCount');
        // 待发货
        $no_delivery = $model_order->getOrderCountByID('store', $this->store_info['store_id'], 'PayCount');
        // 待收货
        $no_receipt = $model_order->getOrderCountByID('store', $this->store_info['store_id'], 'SendCount');
        //完成的订单
        $orderok = $model_order->getOrderCount(array('store_id' => $this->store_info['store_id'], 'order_state' => 40));

        $model_refund_return = Model('refund_return');
        // 售前退款
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_type'] = 1;
        $condition['order_lock'] = 2;
        $condition['refund_state'] = array('lt', 3);
        $refund_lock = $model_refund_return->getRefundReturnCount($condition);
        // 售后退款
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_type'] = 1;
        $condition['order_lock'] = 1;
        $condition['refund_state'] = array('lt', 3);
        $refund = $model_refund_return->getRefundReturnCount($condition);
        // 售前退货
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_type'] = 2;
        $condition['order_lock'] = 2;
        $condition['refund_state'] = array('lt', 3);
        $return_lock = $model_refund_return->getRefundReturnCount($condition);
        // 售后退货
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_type'] = 2;
        $condition['order_lock'] = 1;
        $condition['refund_state'] = array('lt', 3);
        $return = $model_refund_return->getRefundReturnCount($condition);

        $condition = array();
        $condition['accused_id'] = $this->store_info['store_id'];
        $condition['complain_state'] = array(array('gt', 10), array('lt', 90), 'and');
        $complain = Model()->table('complain')->where($condition)->count();

        //待确认的结算账单
        $model_bill = Model('bill');
        $condition = array();
        $condition['ob_store_id'] = $this->store_info['store_id'];
        $condition['ob_state'] = BILL_STATE_CREATE;
        $bill_confirm_count = $model_bill->getOrderBillCount($condition);

        //统计数组
        $statistics = array(
            'goodscount' => $goodscount,
            'online' => $goods_online,
            'waitverify' => $goods_waitverify,
            'verifyfail' => $goods_verifyfail,
            'offline' => $goods_offline,
            'lockup' => $goods_lockup,
            'imagecount' => $imagecount,
            'consult' => $consult,
            'progressing' => $progressing,
            'payment' => $no_payment,
            'delivery' => $no_delivery,
            'no_receipt' => $no_receipt,
            'orderok' => $orderok,
            'refund_lock' => $refund_lock,
            'refund' => $refund,
            'return_lock' => $return_lock,
            'return' => $return,
            'complain' => $complain,
            'bill_confirm' => $bill_confirm_count
        );

        return $statistics;
    }

    private function general()
    {
        $model = Model('stat');
        //统计的日期0点
        $stat_time = strtotime(date('Y-m-d', time())) - 86400;
        /*
         * 近30天
         */
        $stime = $stat_time - (86400 * 29);//30天前
        $etime = $stat_time + 86400 - 1;//昨天23:59

        $statnew_arr = array();

        //查询订单表下单量、下单金额、下单客户数
        $where = array();
        $where['order_isvalid'] = 1;//计入统计的有效订单
        $where['store_id'] = $_SESSION['store_id'];
        $where['order_add_time'] = array('between', array($stime, $etime));
        $field = ' COUNT(*) as ordernum, SUM(order_amount) as orderamount, COUNT(DISTINCT buyer_id) as ordermembernum, AVG(order_amount) as avgorderamount ';
        $stat_order = $model->getoneByStatorder($where, $field);
        $statnew_arr['ordernum'] = ($t = $stat_order['ordernum']) ? $t : 0;
        $statnew_arr['orderamount'] = wtPriceFormat(($t = $stat_order['orderamount']) ? $t : (0));
        $statnew_arr['ordermembernum'] = ($t = $stat_order['ordermembernum']) > 0 ? $t : 0;
        $statnew_arr['avgorderamount'] = wtPriceFormat(($t = $stat_order['avgorderamount']) ? $t : (0));
        return $statnew_arr;
    }

    public function sellerinfoWt()
    {
        $seller_info = array();
        $seller_info = $this->seller_info;
        $store_info = $this->store_info;

        //最后登陆
        //$seller_info['last_login_time_fmt'] = date('Y-m-d H:i:s', $seller_info['last_login_time']);

        $store_temp = Model('store')->getStoreInfoByID($this->store_info['store_id']);

        //店铺分类
        $model_store_class = Model('store_class');
        $default_rate = '';
        if (!empty($store_temp['scid1']) && $store_temp['scid1'] > 0) {
            $sc_info1 = $model_store_class->getStoreClassInfo(array('sc_id' => $store_temp['scid1']));
            $showsc_info = $sc_info1['sc_name'];
            $default_rate = $sc_info1['commis_rate'];
        }
        if (!empty($store_temp['scid2']) && $store_temp['scid2'] > 0) {
            $sc_info2 = $model_store_class->getStoreClassInfo(array('sc_id' => $store_temp['scid2']));
            $showsc_info = $showsc_info . ' -> ' . $sc_info2['sc_name'];
            $default_rate = $sc_info2['commis_rate'];
        }
        $store_info2['store_scinfo'] = $showsc_info . '(佣金比列:' . $default_rate . '%)';

        /*
        $store_info['a_predeposit'] = $store_temp['available_predeposit'];
        $store_info['f_predeposit'] = $store_temp['freeze_predeposit'];
        $store_info['sell_amount'] = $store_temp['sell_amount']; */

        //店铺头像
        $store_info2['store_avatar'] = getStoreLogo($store_temp['store_avatar'], 'store_avatar');
        $store_info2['isshow'] = $store_temp['isshow'];
        $store_info2['area_info'] = $store_temp['area_info'];


        output_data(array('store_info' => $store_info2));
    }

    /*
    *店铺头像修改操作
    */
    public function ajax_update_avatarWt()
    {
        $store_id = $this->store_info['store_id'];

        //上传图片
        $upload = new UploadFile();
        //$upload->set('thumb_width', 500);
        //$upload->set('thumb_height',499);
        $ext = 'jpg';//strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
        //$upload->set('file_name',"avatar_$member_id.$ext");
        $upload->set('thumb_ext', '_new');
        //$upload->set('ifremove',true);
        $upload->set('default_dir', ATTACH_STORE);
        if (!empty($_FILES['pic']['name'])) {
            $result = $upload->upfile('pic');
            if ($result) {
                //$img_path = $upload->getSysSetPath().$upload->file_name;
                //$img_path = getMemberAvatarForID($member_id).'?rd='.time();

                Model('store')->editStore(array('store_avatar' => $upload->file_name), array('store_id' => $store_id));
                $img_path = getStoreLogo($upload->file_name, 'store_avatar');
                output_data(array('status' => 1, 'info' => '头像上传成功!', 'avatar' => $img_path));
            }
            output_error(array('status' => 0, 'info' => '头像上传失败!!'));
        }
        output_error(array('status' => 0, 'info' => '头像上传失败!'));


    }

}
