<?php
/**
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

class seller_fundControl extends mobileSellerControl
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexWt()
    {

    }

    public function phoneWt()
    {
        $storeInfo = Model('store')->getStoreInfoByID($this->store_info['store_id']);
        //$m_state = $memberInfo['member_mobile_bind'] ? true : false;
        $data = array();
        $data['mobiletxt'] = encryptShow($storeInfo['shop_phone'], 4, 4);
        $data['shop_phone'] = $storeInfo['shop_phone'];
        output_data($data);
    }

    /**
     * 添加提现方式
     */
    public function txway_addWt()
    {

        $obj_validate = new Validate();
        $validate_arr[] = array("input" => $_POST["bill_user_name"], "require" => "true", "message" => '收款人姓名不能为空');
        $validate_arr[] = array("input" => $_POST["bill_type_code"], "require" => "true", "message" => '请选择账户类型');
        $validate_arr[] = array("input" => $_POST["bill_type_number"], "require" => "true", "message" => '收款账号不能为空');
        //$validate_arr[] = array("input"=>$_POST["bill_bank_name"], "require"=>"true","message"=>'请输入开户行');
        $obj_validate->validateparam = $validate_arr;
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error);
        }
        if ($_POST['bill_type_code'] == 'wxpay' && $_POST['openid'] == '') {
            output_error('微信账号参数错误！');
        }
        $id = intval($_POST['id']);
        if ($id > 0) {
            $model_txway = Model('txway');
            $txwayInfo = $model_txway->getInfo(array('store_id' => $this->store_info['store_id'], 'type' => 2, 'id' => $id, 'bill_type_code' => $_POST['bill_type_code']));
            if (!empty($txwayInfo) && is_array($txwayInfo)) {

                $data = array();
                $where = array();
                $where['store_id'] = $this->store_info['store_id'];
                $where['type'] = 2;
                $where['id'] = $id;
                $where['bill_type_code'] = $_POST['bill_type_code'];

                $data['bill_user_name'] = $_POST['bill_user_name'];
                $data['bill_type_number'] = $_POST['bill_type_number'];
                $data['bill_openid'] = $_POST['openid'];
                $insert = Model('txway')->edit($data, $where);

                if ($insert) {
                    output_data(array('state' => 1));
                } else {
                    output_error('修改失败');
                }
            }

        } else {
            $model_txway = Model('txway');
            $txwayInfo = $model_txway->getInfo(array('store_id' => $this->store_info['store_id'], 'type' => 2, 'bill_type_code' => $_POST['bill_type_code']));
            if (!empty($txwayInfo) && is_array($txwayInfo)) {
                output_error('添加失败,已存在');
            }

            $data = array();
            $data['store_id'] = $this->store_info['store_id'];
            $data['store_name'] = $this->store_info['store_name'];
            $data['bill_user_name'] = $_POST['bill_user_name'];
            $data['bill_type_code'] = $_POST['bill_type_code'];
            $data['bill_type_number'] = $_POST['bill_type_number'];
            $data['bill_openid'] = $_POST['openid'];
            //$data['bill_bank_name'] = $_POST['bill_bank_name'];
            //$data['add_time'] = TIMESTAMP;
            $data['type'] = 2;
            $insert = Model('txway')->add($data);

            if ($insert) {
                output_data(array('state' => 1));
            } else {
                output_error('添加失败');
            }
        }

    }

    /**
     * 微信小程序获得openid
     */
    public function getwxinfoWt()
    {
        $handle_connect_api = Handle('connect_api');
        if (!empty($_POST['code'])) {
            $code = $_POST['code'];
            $client = 'wap';
            $user_info = $handle_connect_api->getWxMiniUserInfo($code);

            if (!empty($user_info['openid']) && $user_info['openid'] != '') {
                $openid = $user_info['openid'];
                /*  $model_txway = Model('txway');
                 $txwayInfo = $model_txway->getInfo(array('bill_openid'=> $openid,'type'=>2));

                 if(!empty($txwayInfo)){ //||$txwayInfo['store_id']==$this->store_info['store_id']) {

                     output_error('当前微信号已存在',array('state' => 0));

                 } else
                 { */
                output_data(array('state' => 10, 'openid' => $user_info['openid']));
                // }
            } else {
                output_error('获取信息失败1', array('state' => 0));
            }
        } else {
            output_error('获取信息失败2', array('state' => 0));
        }
    }

    /**
     * 获取一条提现方式
     */
    public function gettxwayoneWt()
    {
        $data = array();
        $txinfo = array();
        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['type'] = 2;
        $where['id'] = intval($_POST['id']);
        $tx = Model('txway')->getInfo($where);
        if (!empty($tx) && is_array($tx)) {
            $txinfo['id'] = $tx['id'];
            $txinfo['bill_user_name'] = $tx['bill_user_name'];
            $txinfo['bill_type_code'] = $tx['bill_type_code'];
            $txinfo['paytxt'] = $tx['bill_type_code'] == 'wxpay' ? '微信支付账号' : '支付宝账号';
            $txinfo['bill_type_number'] = $tx['bill_type_number'];
            //$txinfo['bill_type_number'] = $tx['bill_type_number'];
            $txinfo['bill_type_number_txt'] = encryptShow($tx['bill_type_number'], 4, 4);
            if ($tx['bill_type_code'] == 'wxpay') {
                $txinfo['bill_type_number_txt'] = $tx['bill_type_number'];
            }
            $data['notxway'] = 0;
            $data['txway'] = $txinfo;
        } else {
            $data['notxway'] = 1;
        }
        output_data($data);

    }

    /**
     * 提现方式列表
     */
    public function txwaylistWt()
    {
        $model_txway = Model('txway');
        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['type'] = 2;

        $list = $model_txway->getList($where, 4, '*', 'id desc');
        $page_count = $model_txway->gettotalpage();
        $have_wx = 0;
        $have_alipay = 0;
        if ($list) {
            foreach ($list as $k => $v) {
                $data = array();
                $data['id'] = $v['id'];
                $data['bill_user_name'] = $v['bill_user_name'];
                $data['bill_type_code'] = $v['bill_type_code'];
                //$data['bill_type_number'] = $v['bill_type_number'];
                $data['bill_type_number_txt'] = encryptShow($v['bill_type_number'], 4, 4);
                if ($v['bill_type_code'] == 'wxpay') {
                    $data['bill_type_number_txt'] = $v['bill_type_number'];
                    $have_wx = 1;
                }
                if ($v['bill_type_code'] == 'alipay') {
                    $have_alipay = 1;
                }
                $data['is_default'] = $v['is_default'];
                $list[$k] = $data;
            }
        }
        output_data(array('list' => $list, 'wx' => $have_wx, 'alipay' => $have_alipay), mobile_page($page_count));
    }
    /**
     * 设置提现方式为默认1
     */
    /*    public function isdefautWt(){

           $where = array();
           $where['store_id'] = $this->store_info['store_id'];
           $where['type'] = 2;
           $insert = Model('txway')->edit(array('is_default'=>0),$where);
           $where = array();
           $where['store_id'] = $this->store_info['store_id'];
           $where['id'] = intval($_POST['id']);
           $where['type'] = 2;
           $insert = Model('txway')->edit(array('is_default'=>1),$where);
           if($insert) {
               output_data(array('state'=>1));
           }else{
               output_error('设置失败');
           }

       } */
    /**
     * 获取提现方式
     */
    public function gettxwayWt()
    {
        $data = array();
        $txinfo = array();
        $storeInfo = Model('store')->getStoreInfoByID($this->store_info['store_id']);
        $data['predepoit'] = $storeInfo['available_predeposit'];
        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        $where['type'] = 2;
        $where['is_default'] = 1;
        $tx = Model('txway')->getInfo($where);
        if (empty($tx)) {
            $where = array();
            $where['store_id'] = $this->store_info['store_id'];
            $where['type'] = 2;
            $tx = Model('txway')->getInfo($where);
        }
        if (!empty($tx) && is_array($tx)) {
            $txinfo['id'] = $tx['id'];
            $txinfo['bill_user_name'] = $tx['bill_user_name'];
            $txinfo['bill_type_code'] = $tx['bill_type_code'];
            $txinfo['paytxt'] = $tx['bill_type_code'] == 'wxpay' ? '微信支付账号' : '支付宝账号';
            //$txinfo['bill_type_number'] = $tx['bill_type_number'];
            $txinfo['bill_type_number_txt'] = encryptShow($tx['bill_type_number'], 4, 4);
            $data['notxway'] = 0;
            $data['txway'] = $txinfo;
        } else {
            $data['notxway'] = 1;
        }
        output_data($data);

    }
    /**
     * 申请提现
     */
    /*    public function pd_cash_addWt(){
           $store_id = $this->store_info['store_id'];
           $obj_validate = new Validate();
           $pdc_amount = abs(floatval($_POST['pdc_amount']));
           $validate_arr[] = array("input"=>$pdc_amount, "require"=>"true",'validator'=>'Compare','operator'=>'>=',"to"=>'0.01',"message"=>'提现金额错误');
           $validate_arr[] = array("input"=>$_POST["id"], "require"=>"true","message"=>'参数错误！');
           $validate_arr[] = array("input"=>$_POST["ppwd"], "require"=>"true","message"=>'请输入支付密码！');
           $obj_validate -> validateparam = $validate_arr;
           $error = $obj_validate->validate();
           if ($error != ''){
               output_error($error);
           }
           $txwayInfo = Model('txway')->getInfo(array('store_id'=>$store_id,'id'=>intval($_POST["id"])));
           if(empty($txwayInfo)){
               output_error('提现参数错误！');
           }

           $model_pd = Model('store_predeposit');
           $model_store = Model('store');
           $store_info = $model_store->table('store')->where(array('store_id'=> $store_id))->master(true)->lock(true)->find();//锁定当前记录

           $sellerInfo = Model('seller')->getSellerInfo(array('store_id'=>$store_id,'seller_id'=>$this->seller_info['seller_id']));
           //验证支付密码
           if (md5($_POST['ppwd']) != $sellerInfo['seller_ppwd']) {
               output_error('支付密码错误');
           }
           //验证金额是否足够
           if (floatval($store_info['available_predeposit']) < $pdc_amount){
               output_error('提现金额不足');
           }
           try {
               $model_pd->beginTransaction();
               $pdc_sn = $model_pd->makeSn();
               $data = array();
               $data['pdc_sn'] = $pdc_sn;
               $data['pdc_store_id'] = $this->store_info['store_id'];
               $data['pdc_store_name'] = $this->store_info['store_name'];
               $data['pdc_amount'] = $pdc_amount;
               $data['pdc_bank_name'] = $txwayInfo['bill_type_code']=='wxpay'?'微信支付账号':'支付宝账号';//$txwayInfo['bill_bank_name'];
               $data['pdc_bank_no'] = $txwayInfo['bill_type_number'];
               $data['pdc_bank_user'] = $txwayInfo['bill_user_name'];
               $data['pdc_add_time'] = TIMESTAMP;
               $data['pdc_payment_state'] = 0;
               $insert = $model_pd->addPdCash($data);
               if (!$insert) {
                   throw new Exception('写入数据失败');
               }
               //冻结可用预存款
               $data = array();
               $data['store_id'] = $store_info['store_id'];
               $data['store_name'] = $store_info['store_name'];
               $data['amount'] = $pdc_amount;
               $data['order_sn'] = $pdc_sn;
               $model_pd->changePd('cash_apply',$data);
               $model_pd->commit();
               output_data(array('state'=>1,'msg'=>'提交成功'));
           } catch (Exception $e) {
               $model_pd->rollback();
               output_error($e->getMessage());
           }

       }
    */

    /*================================================================*/


    /**
     * 商家中心
     */
    public function index0Wt()
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

        output_data(array('store_info' => $store_info, 'artlist' => $artlist));
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
        $membernum = $model_order->getOrderCountByMember(array('store_id' => $this->store_info['store_id'], 'order_state1' => array('gt', 10)));

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


    /*================================================================*/

}
