<?php
/**
 * mobile父类
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */


defined('ShopWT') or exit('Access Denied By ShopWT');

/********************************** 前台control父类 **********************************************/
class mobileControl
{

    //客户端类型
    protected $client_type_array = array('android', 'wap', 'wechat', 'ios', 'windows');
    //列表默认分页数
    protected $page = 5;


    public function __construct()
    {
        Language::read('mobile');

        if (!empty($_GET['page'])) {
            //分页数处理
            $page = intval($_GET['page']);
            if ($page > 0) {
                $this->page = $page;
            }
        }
    }

    /**
     * 设置或者获取当前的Header
     * @access public
     * @param string|array $name header名称
     * @param string $default 默认值
     * @return string
     */
    public function getHeader($name = '', $default = null)
    {
        if (empty($this->header)) {
            $header = [];
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $server = $this->server ?: $_SERVER;
                foreach ($server as $key => $val) {
                    if (0 === strpos($key, 'HTTP_')) {
                        $key = str_replace('_', '-', strtolower(substr($key, 5)));
                        $header[$key] = $val;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
            }
            $this->header = array_change_key_case($header);
        }
        if (is_array($name)) {
            return $this->header = array_merge($this->header, $name);
        }
        if ('' === $name) {
            return $this->header;
        }
        $name = str_replace('_', '-', strtolower($name));
        return isset($this->header[$name]) ? $this->header[$name] : $default;
    }


}

class mobileHomeControl extends mobileControl
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getMemberIdIfExists()
    {
        /*  $key = $_POST['key'];
         if (empty($key)) {
             $key = $_GET['key'];
         }
  */
        $headerInfo = $this->getHeader();//echo json_encode($headerInfo);
        $key = $headerInfo['xtoken'];

        $model_mb_user_token = Model('mb_user_token');
        $mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);
        if (empty($mb_user_token_info)) {
            return 0;
        }

        return $mb_user_token_info['member_id'];
    }

    protected function getStoreIdIsLogin()
    {

        $model_mb_seller_token = Model('mb_seller_token');
        $headerInfo = $this->getHeader();
        $key = $headerInfo['stoken'];

        if (empty($key)) {
            return 0;
        }

        $mb_seller_token_info = $model_mb_seller_token->getSellerTokenInfoByToken($key);
        if (empty($mb_seller_token_info)) {
            return 0;
        }

        $model_seller = Model('seller');
        $model_store = Model('store');
        $seller_info = $model_seller->getSellerInfo(array('seller_id' => $mb_seller_token_info['seller_id']));
        $store_info = $model_store->getStoreInfoByID($seller_info['store_id']);
        if (!empty($seller_info) && !empty($store_info)) {
            return $seller_info['store_id'];
        } else {
            return 0;
        }

    }
}

class mobileMemberControl extends mobileControl
{

    protected $member_info = array();
    protected $header = [];

    public function __construct()
    {
        parent::__construct();
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, "MicroMessenger") && $_GET["w"] == 'auto') {
            $this->appId = C('app_weixin_appid');
            $this->appSecret = C('app_weixin_secret');;
        } else {
            $model_mb_user_token = Model('mb_user_token');
            /* $key = $_POST['key'];
            if(empty($key)) {
                $key = $_GET['key'];
            } */

            $headerInfo = $this->getHeader();//echo json_encode($headerInfo);
            $key = $headerInfo['xtoken'];
//            $key = 'a0e96394c8e47ab233e2f17f6f63ddb7';

            if (empty($key)) {
                output_error('请登录', array('login' => '0'));
            }
            $mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);
            if (empty($mb_user_token_info)) {
                output_error('请登录!', array('login' => '0'));
            }

            $model_member = Model('member');
            $this->member_info = $model_member->getMemberInfoByID($mb_user_token_info['member_id']);

            if (empty($this->member_info)) {
                output_error('请登录!!', array('login' => '0'));
            } else {
                $this->member_info['client_type'] = $mb_user_token_info['client_type'];
                $this->member_info['openid'] = $mb_user_token_info['openid'];
                $this->member_info['token'] = $mb_user_token_info['token'];
                $level_name = $model_member->getOneMemberGrade($mb_user_token_info['member_id']);
                $this->member_info['level_name'] = $level_name['level_name'];
                //读取卖家信息
                $seller_info = Model('seller')->getSellerInfo(array('member_id' => $this->member_info['member_id']));
                $this->member_info['store_id'] = $seller_info['store_id'];
            }
        }
    }

    public function getOpenId()
    {
        return $this->member_info['openid'];
    }

    public function setOpenId($openId)
    {
        $this->member_info['openid'] = $openId;
        Model('mb_user_token')->updateMemberOpenId($this->member_info['token'], $openId);
    }


}

class mobileSellerControl extends mobileControl
{

    protected $seller_info = array();
    protected $seller_group_info = array();
    protected $member_info = array();
    protected $store_info = array();
    protected $store_grade = array();

    public function __construct()
    {
        parent::__construct();

        $model_mb_seller_token = Model('mb_seller_token');

        //$key = $_POST['key']?$_POST['key']:$_GET['key'];
        $headerInfo = $this->getHeader();//echo json_encode($headerInfo);
        $key = $headerInfo['stoken'];
        //$key = '05c866584ac9512f0a75fff62a77b2b8';
        //echo $key;
        if (empty($key)) {
            output_error('请登录', array('login' => '0'));
        }

        $mb_seller_token_info = $model_mb_seller_token->getSellerTokenInfoByToken($key);
        if (empty($mb_seller_token_info)) {
            output_error('请登录', array('login' => '0'));
        }

        $model_seller = Model('seller');
        //$model_member = Model('member');
        $model_store = Model('store');
        $model_seller_group = Model('seller_group');

        $this->seller_info = $model_seller->getSellerInfo(array('seller_id' => $mb_seller_token_info['seller_id']));
        //$this->member_info = $model_member->getMemberInfoByID($this->seller_info['member_id']);
        $this->store_info = $model_store->getStoreInfoByID($this->seller_info['store_id']);
        $this->seller_group_info = $model_seller_group->getSellerGroupInfo(array('group_id' => $this->seller_info['seller_group_id']));

        // 店铺等级
        /*      if (intval($this->store_info['is_own_shop']) === 1) {
                 $this->store_grade = array(
                     'sg_id' => '0',
                     'sg_name' => '自营店铺专属等级',
                     'sg_goods_limit' => '0',
                     'sg_album_limit' => '0',
                     'sg_space_limit' => '999999999',
                     'sg_template_number' => '6',
                     'sg_price' => '0.00',
                     'sg_description' => '',
                     'sg_function' => 'editor_multimedia',
                     'sg_sort' => '0',
                 );
             } else {
                 $store_grade = rkcache('store_grade', true);
                 $this->store_grade = $store_grade[$this->store_info['grade_id']];
             } */

        /*  if(empty($this->member_info)) {
             output_error('请登录', array('login' => '0'));
         } else {
             $this->seller_info['client_type'] = $mb_seller_token_info['client_type'];
         } */
    }
}

class mobilefenxiaoControl extends mobileControl
{

    protected $member_info = array();

    public function __construct()
    {
        parent::__construct();

        $model_mb_user_token = Model('mb_user_token');
        $key = $_POST['key'];
        if (empty($key)) {
            $key = $_GET['key'];
        }
        $mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);
        if (empty($mb_user_token_info)) {
            output_error('请登录', array('login' => '0'));
        }

        $model_member = Model('member');
        $this->member_info = $model_member->getMemberInfoByID($mb_user_token_info['member_id']);

        if (empty($this->member_info)) {
            output_error('请登录', array('login' => '0'));
        } else {
            if (!in_array($this->member_info['fx_state'], array(2, 4, 5))) {
                output_error('请先认证成为分销员', array('is_fxuser' => '0'));
            }
            $member_gradeinfo = $model_member->getOneMemberGrade(intval($this->member_info['member_exppoints']));
            $this->member_info['level'] = $member_gradeinfo['level'];
            $this->member_info['client_type'] = $mb_user_token_info['client_type'];
            $this->member_info['openid'] = $mb_user_token_info['openid'];
            $this->member_info['token'] = $mb_user_token_info['token'];

            //读取卖家信息
            $seller_info = Model('seller')->getSellerInfo(array('member_id' => $this->member_info['member_id']));
            $this->member_info['store_id'] = $seller_info['store_id'];

            //可提现金额
            $available_trad = $this->member_info['trad_amount'];

            //冻结金额
            $freeze_trad = floatval($this->member_info['freeze_trad']);
            if ($this->member_info['fx_state'] == 2) {
                if ($this->member_info['trad_amount'] >= C('fenxiao_bill_limit')) {
                    $freeze_trad += C('fenxiao_bill_limit');
                    $available_trad -= C('fenxiao_bill_limit');
                } else {
                    $freeze_trad += $this->member_info['trad_amount'];
                    $available_trad = 0;
                }
            }
            $this->member_info['available_fx_trad'] = $available_trad;
            $this->member_info['freeze_fx_trad'] = $freeze_trad;
        }
    }

    public function getOpenId()
    {
        return $this->member_info['openid'];
    }

    public function setOpenId($openId)
    {
        $this->member_info['openid'] = $openId;
        Model('mb_user_token')->updateMemberOpenId($this->member_info['token'], $openId);
    }
}
