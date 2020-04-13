<?php
/**
 * 第三方账号登录
 *
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');


class connectControl extends mobileHomeControl {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 登录开关状态
     */
    public function get_stateWt() {
        $handle_connect_api = Handle('connect_api');
        $state_array = $handle_connect_api->getStateInfo();

        $key = $_GET['t'];
        if (trim($key) != '' && array_key_exists($key, $state_array)) {
            output_data($state_array[$key]);
        } else {
            output_data($state_array);
        }
    }

    /**
     * h5页面微信登录回调
     */
    public function indexWt() {
        $handle_connect_api = Handle('connect_api');
        if (!empty($_POST['code'])) {
            $code = $_POST['code'];
            $client = 'h5';
            $user_info = $handle_connect_api->getWxUserInfo($code, 'h5');
            if (!empty($user_info['unionid'])) {
                $unionid = $user_info['unionid'];
                $model_member = Model('member');
                $member = $model_member->getMemberInfo(array('weixin_unionid' => $unionid));
                $state_data = array();
                $token = 0;
                if (!empty($member)) {//会员信息存在时自动登录
                    $token = $handle_connect_api->getUserToken($member, $client);
                    if ($token) {
                        output_data(array('username' => $member['member_name'], 'nickname' => $member['nickname'], 'userid' => $member['member_id'], 'token' => $token));

                    } else {
                        output_error('会员登录失败');
                    }
                } else {//自动注册会员并登录
                    /* $info_data = $handle_connect_api->wxRegister($user_info, $client);
                    $token = $info_data['token'];
                    $member = $info_data['member'];
                    $state_data['password'] = $member['member_passwd']; */
                    output_data(array('state' => 10, 'SessionKey' => $user_info['session_key'], 'unionid' => $user_info['unionid']));
                }
            } else {
                output_error('登陆失败！', array('state' => 0));
            }


            /*     if(!empty($user_info['unionid'])){
                    $unionid = $user_info['unionid'];
                    $model_member = Model('member');
                    $member = $model_member->getMemberInfo(array('weixin_unionid'=> $unionid));
                    $state_data = array();
                    $token = 0;
                    if(!empty($member)) {//会员信息存在时自动登录
                        $token = $handle_connect_api->getUserToken($member, $client);
                    } else {//自动注册会员并登录
                        $info_data = $handle_connect_api->wxRegister($user_info, $client);
                        $token = $info_data['token'];
                        $member = $info_data['member'];
                        $state_data['password'] = $member['member_passwd'];
                    }
                    if($token) {
                        $state_data['key'] = $token;
                        $state_data['username'] = $member['member_name'];
                        $state_data['userid'] = $member['member_id'];
                        redirect(WAP_SITE_URL.'/html/member/member.html?info=wt&username='.$state_data['username'].'&key='.$state_data['key']);
                    } else {
                        output_error('会员登录失败!');
                    }
                } else {
                    $_url = $handle_connect_api->getWxOAuth2Url();
                    @header("location: ".$_url);
                } */
        } else {
            $_url = $handle_connect_api->getWxOAuth2Url();
            @header("location: " . $_url);
        }
    }

    public function wxlogin_h5Wt() {
        echo time();
        if (!empty($_GET['code'])) {
            @header("location: https://www.fhlego.com/m/#/pages/member/wxlogin?code=" . $_GET['code']);
        }
    }

    /*wx_jsapi支付*/
    public function wxjsapi_codeWt() {
        //https://www.fhlego.com/m/#/pages/store/pay?
        $paysn = trim($_GET['paysn']);
        //$orderinfo = Model('store_order')->getOrderInfo(array('pay_sn'=>$paysn));
        $model_mb_payment = Model('mb_payment');
        $condition = array();
        $condition['payment_code'] = 'wxpay_jsapi';
        $mb_payment_info = $model_mb_payment->getMbPaymentOpenInfo($condition);
        if (!$mb_payment_info) {
            echo '支付方式未开启';
            exit;
        }
        $param = $mb_payment_info['payment_config'];
        $url = sprintf(
            'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=redirected#wechat_redirect',
            $param['appId'],
            urlencode(MOBILE_SITE_URL . '/index.php/connect/pay_wxjsapi/paysn/' . $paysn)
        //urlencode('https://www.fhlego.com/m/#/pages/store/wxjsapi?paysn=' . $paysn)
        );

        header('Location: ' . $url);

    }

    public function pay_wxjsapiWt() {
        echo time();
        if (!empty($_GET['code'])) {
            @header('location: https://www.fhlego.com/m/#/pages/store/wxjsapi?paysn=' . $_GET['paysn'] . '&code=' . $_GET['code']);
        } else {
            echo time();
        }
    }

    /**
     * QQ互联获取应用唯一标识
     */
    public function get_qq_appidWt() {
        output_data(C('app_qq_akey'));
    }

    /**
     * 请求QQ互联授权
     */
    public function get_qq_oauth2Wt() {
        $handle_connect_api = Handle('connect_api');
        $qq_url = $handle_connect_api->getQqOAuth2Url('api');
        @header("location: " . $qq_url);
    }

    /**
     * QQ互联获取回调信息
     */
    public function get_qq_infoWt() {
        $code = $_GET['code'];
        $token = $_GET['token'];
        $client = $_GET['client'];
        $handle_connect_api = Handle('connect_api');
        $user_info = $handle_connect_api->getQqUserInfo($code, $client, $token);
        if (!empty($user_info['openid'])) {
            $qqopenid = $user_info['openid'];
            $model_member = Model('member');
            $member = $model_member->getMemberInfo(array('member_qqopenid' => $qqopenid));
            $state_data = array();
            $token = 0;
            if (!empty($member)) {//会员信息存在时自动登录
                $token = $handle_connect_api->getUserToken($member, $client);
            } else {//自动注册会员并登录
                $info_data = $handle_connect_api->qqRegister($user_info, $client);
                $token = $info_data['token'];
                $member = $info_data['member'];
                $state_data['password'] = $member['member_passwd'];
            }
            if ($token) {
                $state_data['key'] = $token;
                $state_data['username'] = $member['member_name'];
                $state_data['userid'] = $member['member_id'];
                if ($client == 'wap') {
                    redirect(WAP_SITE_URL . '/html/member/member.html?info=wt&username=' . $state_data['username'] . '&key=' . $state_data['key']);
                }
                output_data($state_data);
            } else {
                output_error('会员登录失败');
            }
        } else {
            output_error('QQ互联登录失败');
        }
    }

    /**
     * 新浪微博获取应用唯一标识
     */
    public function get_sina_appidWt() {
        output_data(C('app_sina_akey'));
    }

    /**
     * 请求新浪微博授权
     */
    public function get_sina_oauth2Wt() {
        $handle_connect_api = Handle('connect_api');
        $sina_url = $handle_connect_api->getSinaOAuth2Url('api');
        @header("location: " . $sina_url);
    }

    /**
     * 新浪微博获取回调信息
     */
    public function get_sina_infoWt() {
        $code = $_GET['code'];
        $client = $_GET['client'];
        $sina_token['access_token'] = $_GET['accessToken'];
        $sina_token['uid'] = $_GET['userID'];
        $handle_connect_api = Handle('connect_api');
        $user_info = $handle_connect_api->getSinaUserInfo($code, $client, $sina_token);
        if (!empty($user_info['id'])) {
            $sinaopenid = $user_info['id'];
            $model_member = Model('member');
            $member = $model_member->getMemberInfo(array('member_sinaopenid' => $sinaopenid));
            $state_data = array();
            $token = 0;
            if (!empty($member)) {//会员信息存在时自动登录
                $token = $handle_connect_api->getUserToken($member, $client);
            } else {//自动注册会员并登录
                $info_data = $handle_connect_api->sinaRegister($user_info, $client);
                $token = $info_data['token'];
                $member = $info_data['member'];
                $state_data['password'] = $member['member_passwd'];
            }
            if ($token) {
                $state_data['key'] = $token;
                $state_data['username'] = $member['member_name'];
                $state_data['userid'] = $member['member_id'];
                if ($client == 'wap') {
                    redirect(WAP_SITE_URL . '/html/member/member.html?info=wt&username=' . $state_data['username'] . '&key=' . $state_data['key']);
                }
                output_data($state_data);
            } else {
                output_error('会员登录失败');
            }
        } else {
            output_error('新浪微博登录失败');
        }
    }

    /**
     * 微信获取应用唯一标识
     */
    public function get_wx_appidWt() {
        output_data(C('app_weixin_appid'));
    }

    /**
     * 微信获取回调信息
     */
    public function get_wx_infoWt() {
        $code = $_GET['code'];
        $access_token = $_GET['access_token'];
        $openid = $_GET['openid'];
        $client = $_GET['client'];
        $handle_connect_api = Handle('connect_api');
        if (!empty($code)) {
            $user_info = $handle_connect_api->getWxUserInfo($code, 'api');
        } else {
            $user_info = $handle_connect_api->getWxUserInfoUmeng($access_token, $openid);
        }
        if (!empty($user_info['unionid'])) {
            $unionid = $user_info['unionid'];
            $model_member = Model('member');
            $member = $model_member->getMemberInfo(array('weixin_unionid' => $unionid));
            $state_data = array();
            $token = 0;
            if (!empty($member)) {//会员信息存在时自动登录
                $token = $handle_connect_api->getUserToken($member, $client);
            } else {//自动注册会员并登录
                $info_data = $handle_connect_api->wxRegister($user_info, $client);
                $token = $info_data['token'];
                $member = $info_data['member'];
                $state_data['password'] = $member['member_passwd'];
            }
            if ($token) {
                $state_data['key'] = $token;
                $state_data['username'] = $member['member_name'];
                $state_data['userid'] = $member['member_id'];
                output_data($state_data);
            } else {
                output_error('会员登录失败');
            }
        } else {
            output_error('微信登录失败');
        }
    }

    /**
     * 获取手机短信验证码
     */
    public function get_sms_captchaWt() {
        /* $sec_key = $_POST['sec_key'];
        $sec_val = $_POST['sec_val']; */
        $phone = $_POST['phone'];
        $log_type = $_POST['type'];//短信类型:1为注册,2为登录,3为找回密码
        /*$state_data = array(
            'state' => false,
            'msg' => '验证码或手机号码不正确'
            );*/

        //$result = Model('apivercode')->checkApiVercode($sec_key,$sec_val);
        //if ($result && strlen($phone) == 11){
        if (strlen($phone) == 11) {
            $handle_connect_api = Handle('connect_api');
            $state_data = $handle_connect_api->sendCaptcha($phone, $log_type);
        }
        $this->connect_output_data($state_data);
    }

    /**
     * 获取手机短信验证码
     */
    public function get_sms_sellerWt() {
        /* $sec_key = $_POST['sec_key'];
        $sec_val = $_POST['sec_val']; */
        $phone = $_POST['phone'];
        $log_type = $_POST['type'];//短信类型:1为注册,2为登录,3为找回密码
        /*$state_data = array(
            'state' => false,
            'msg' => '验证码或手机号码不正确'
            );*/

        //$result = Model('apivercode')->checkApiVercode($sec_key,$sec_val);
        //if ($result && strlen($phone) == 11){
        if (strlen($phone) == 11) {
            $handle_connect_api = Handle('connect_api');
            $state_data = $handle_connect_api->sendCaptcha($phone, $log_type);
        }
        $this->connect_output_data($state_data);
    }

    /**
     * 验证手机验证码
     */
    public function check_sms_captchaWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        $this->connect_output_data($state_data, 1);
    }

    /**
     * 手机找回密码
     */
    public function find_passwordWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $client = $_POST['client'];
        if ($password == '') {
            output_error('登陆密码不能为空');;
            return;
        }
        if ($password != $password_confirm) {
            output_error('两次输入的登陆密码不一致。');;
            return;
        }
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->smsPassword($phone, $captcha, $password, $client);
        $this->connect_output_data($state_data);
    }

    /**
     * 商家手机找回密码
     */
    public function find_sellerpwdWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $client = $_POST['client'];
        if ($password == '') {
            output_error('登陆密码不能为空');;
            return;
        }
        if ($password != $password_confirm) {
            output_error('两次输入的登陆密码不一致。');;
            return;
        }
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->smsSellerPwd($phone, $captcha, $password, $client);
        $this->connect_output_data($state_data);
    }

    /**
     * 格式化输出数据
     */
    public function connect_output_data($state_data, $type = 0) {
        if ($state_data['state']) {
            unset($state_data['state']);
            unset($state_data['msg']);
            if ($type == 1) {
                $state_data = 1;
            }
            output_data($state_data);
        } else {
            output_error($state_data['msg']);
        }
    }


    /*****会员注册 开始****/

    /**
     * 手机号检测
     *
     * @param
     * @return
     */
    public function checkmobWt() {
        $model_member = Model('member');
        $check_member_mobile = $model_member->getMemberInfo(array('member_mobile' => $_POST['phone']));
        if (is_array($check_member_mobile) && count($check_member_mobile) > 0) {
            //echo 'false';
            output_error('0');
        } else {
            output_data('1');
        }
    }

    /**
     * 会员名称检测
     *
     * @param
     * @return
     */
    public function check_memberWt() {
        /**
         * 实例化模型
         */
        $model_member = Model('member');

        $check_member_name = $model_member->getMemberInfo(array('member_name' => $_POST['username']));
        if (is_array($check_member_name) && count($check_member_name) > 0) {
            output_error('该用户名已存在,请换其他名字');
        } else {
            output_data('1');
        }
    }

    /**
     * 会员名称检测
     *
     * @param
     * @return
     */
    public function check_nickWt() {
        /**
         * 实例化模型
         */
        $model_member = Model('member');

        $check_member_name = $model_member->getMemberInfo(array('nickname' => $_POST['nickname']));
        if (is_array($check_member_name) && count($check_member_name) > 0) {
            output_error('该昵称已存在,请换其他名字');
        } else {
            output_data('1');
        }
    }

    /**
     * 会员名称检测
     *
     * @param
     * @return
     */
    public function check_emailWt() {
        /**
         * 实例化模型
         */
        $model_member = Model('member');

        $check_member_name = $model_member->getMemberInfo(array('member_email' => $_POST['email']));
        if (is_array($check_member_name) && count($check_member_name) > 0) {
            output_error('该电子邮箱已存在,请换其它');
        } else {
            output_data('1');
        }
    }

    /**
     * 验证手机验证码和邀请码
     */
    public function check_sms_inviteWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];
        $invcode = $_POST['invcode'];
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        //$this->connect_output_data($state_data, 1);
        if ($state_data['state']) {
            //验证邀请码

            $invite_userid = getUserInviteCode(trim($invcode));
            if ($invite_userid == '' || intval($invite_userid) < 1) {
                output_error('邀请码不正确');
            }

            $invite_member_info = Model('member')->getMemberInfo(array('member_id' => $invite_userid));
            if (empty($invite_member_info) || !is_array($invite_member_info) || $invite_member_info['is_realverify'] != 1) {
                output_error('邀请码不存在');
            }
            unset($state_data['state']);
            unset($state_data['msg']);

            output_data('1');
        } else {
            output_error($state_data['msg']);
        }
    }


    /**
     * 验证手机验证码 并绑定微信账号
     */
    public function wxbindMemberWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];
        $unionid = $_POST['unionid'];
        if (empty($unionid) || $unionid == '') {
            output_error('绑定失败！');
        }
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        if ($state_data['state']) {

            $member_info = Model('member')->getMemberInfo(array('member_mobile' => $phone));
            if (empty($member_info)) {
                output_error('绑定账户不存在');
            } else {
                Model('member')->editMember(array('member_mobile' => $phone), array('weixin_unionid' => $unionid));
                $token = $handle_connect_api->getUserToken($member_info, 'wap');
                if ($token) {
                    output_data(array('username' => $member_info['member_name'], 'nickname' => $member_info['nickname'], 'userid' => $member_info['member_id'], 'token' => $token));
                } else {
                    output_error('登录失败');
                }
            }

        } else {
            output_error($state_data['msg']);
        }
    }

    /**
     * 验证手机验证码
     */
    public function check_smsWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        //$this->connect_output_data($state_data, 1);
        if ($state_data['state']) {
            output_data('1');
        } else {
            output_error($state_data['msg']);
        }
    }

    /**
     * 手机验证码登陆
     */
    public function smsloginWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $log_type = $_POST['type'];
        $unionid = $_POST['unionid'];
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->checkSmsCaptcha($phone, $captcha, $log_type);
        //$this->connect_output_data($state_data, 1);
        if ($state_data['state']) {
            $member_info = Model('member')->getMemberInfo(array('member_mobile' => $phone));
            if (empty($member_info)) {
                output_error('登陆失败');
            } else {

                $token = $handle_connect_api->getUserToken($member_info, 'wap');
                if ($token) {
                    //判断uniond是否已经绑定过用户
                    $utype = Model('member')->getMemberInfo(array('weixin_unionid' => $unionid));
                    //如果uniondid为空，且没有绑定其他手机号，则绑定unionid
                    if ($member_info['weixin_unionid'] == "" && !$utype) {
                        Model('member')->editMember(array('member_mobile' => $phone), array('weixin_unionid' => $unionid));
                    }
                    output_data(array('username' => $member_info['member_name'], 'nickname' => $member_info['nickname'], 'userid' => $member_info['member_id'], 'token' => $token));
                } else {
                    output_error('登录失败');
                }
            }
        } else {
            output_error($state_data['msg']);
        }
    }

    /**
     * 手机注册
     */
    public function sms_registerWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $password = $_POST['password'];
//        $client = $_POST['client'];
        $client = 'wap';
        $username = md5($phone);// $_POST['username'];
        $nickname = '_' . $phone;//$_POST['nickname'];
        $password_confirm = $_POST['password_confirm'];
        //$email = $_POST['email'];
        //$puserpwd = $_POST['puserpwd'];
        //$invcode = $_POST['invcode'];
        $invite_userid = getUserInviteCode(trim($_POST['invcode']));
        if ($invite_userid == '' || intval($invite_userid) < 1) {
            output_error('邀请码不正确', array('state' => 0));
        }

        $invite_member_info = Model('member')->getMemberInfo(array('member_id' => $invite_userid));
        if (empty($invite_member_info) || !is_array($invite_member_info)) {
            output_error('邀请码不存在', array('state' => 0));
        }
        //$ppassword_confirm = $_POST['ppassword_confirm'];
        $logic_connect_api = Handle('connect_api');
        $state_data = $logic_connect_api->smsRegister($phone, $captcha, $password, $client, $username, $password_confirm, $invite_member_info['member_id'], $nickname);
        //返回token
        $model_member = Model('member');
        $member = $model_member->getMemberInfo(array('member_mobile' => $phone));
        $handle_connect_api = Handle('connect_api');
        $token = $handle_connect_api->getUserToken($member, $client);
        $state_data['userinfo'] = array('username' => $member['member_name'], 'nickname' => $member['nickname'], 'userid' => $member['member_id'], 'token' => $token);
        $this->connect_output_data($state_data);

        /* $phone = $_POST['phone'];
        $captcha = $_POST['captcha'];
        $password = $_POST['password'];
        $client = $_POST['client'];
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->smsRegister($phone, $captcha, $password, $client);
        $this->connect_output_data($state_data); */
    }

    /**
     * 手机一键注册
     */
    public function quick_registerWt() {
        $phone = $_POST['phone'];
        //$captcha = $_POST['yzcode'];
//        $password = $_POST['password'];
        $client = 'wap';
        $weixin_unionid = $_POST['unionid'];
        $username = md5($phone);// $_POST['username'];
        $nickname = '_' . $phone;//$_POST['nickname'];
//        $password_confirm = $_POST['password_confirm'];
        //$email = $_POST['email'];
        //$puserpwd = $_POST['puserpwd'];
        //$invcode = $_POST['invcode'];
        $invite_userid = getUserInviteCode(trim($_POST['invcode']));
        if ($invite_userid == '' || intval($invite_userid) < 1) {
            output_error('邀请码不正确', array('state' => 0));
        }

        $invite_member_info = Model('member')->getMemberInfo(array('member_id' => $invite_userid));
        if (empty($invite_member_info) || !is_array($invite_member_info)) {
            output_error('邀请码不存在', array('state' => 0));
        }
        //$ppassword_confirm = $_POST['ppassword_confirm'];
        $logic_connect_api = Handle('connect_api');
        $state_data = $logic_connect_api->quickRegister($phone, $weixin_unionid, $username, $invite_member_info['member_id'], $nickname);
        //返回token
        $model_member = Model('member');
        $member = $model_member->getMemberInfo(array('weixin_unionid' => $weixin_unionid));
        $handle_connect_api = Handle('connect_api');
        $token = $handle_connect_api->getUserToken($member, $client);
        $state_data['userinfo'] = array('username' => $member['member_name'], 'nickname' => $member['nickname'], 'userid' => $member['member_id'], 'token' => $token);
        $this->connect_output_data($state_data);

        /* $phone = $_POST['phone'];
        $captcha = $_POST['captcha'];
        $password = $_POST['password'];
        $client = $_POST['client'];
        $handle_connect_api = Handle('connect_api');
        $state_data = $handle_connect_api->smsRegister($phone, $captcha, $password, $client);
        $this->connect_output_data($state_data); */
    }


    /**
     * 微信小程序注册
     */
    public function wxmini_registerWt() {
        $phone = $_POST['phone'];
        $captcha = $_POST['yzcode'];
        $password = $_POST['password'];
        $client = $_POST['client'];
        $username = md5($phone);// $_POST['username'];
        $nickname = $_POST['nickname'];
        $unionid = $_POST['unionid'];
        $password_confirm = $_POST['password_confirm'];
        //$email = $_POST['email'];
        //$puserpwd = $_POST['puserpwd'];
        //$invcode = $_POST['invcode'];
        $invite_userid = getUserInviteCode(trim($_POST['invcode']));
        if ($invite_userid == '' || intval($invite_userid) < 1) {
            output_error('邀请码不正确', array('state' => 0));
        }

        $invite_member_info = Model('member')->getMemberInfo(array('member_id' => $invite_userid));
        if (empty($invite_member_info) || !is_array($invite_member_info)) {
            output_error('邀请码不存在', array('state' => 0));
        }
        //$ppassword_confirm = $_POST['ppassword_confirm'];
        $logic_connect_api = Handle('connectsmslogin_api');
        $state_data = $logic_connect_api->wxMiniRegister($phone, $captcha, $password, $client, $username, $password_confirm, $invite_member_info['member_id'], $nickname, $unionid);

        if ($state_data['state']) {
            $member_info = Model('member')->getMemberInfo(array('member_mobile' => $phone));
            if (empty($member_info)) {
                output_error('登陆失败');
            } else {

                $token = $logic_connect_api->getUserToken($member_info, 'wap');
                if ($token) {
                    output_data(array('username' => $member_info['member_name'], 'nickname' => $member_info['nickname'], 'userid' => $member_info['member_id'], 'token' => $token));
                } else {
                    output_error('登录失败');
                }
            }
        } else {
            output_error($state_data['msg']);
        }


    }

    /**
     * 微信小程序登录
     */
    public function wxloginWt() {
        $handle_connect_api = Handle('connect_api');
        if (!empty($_POST['code'])) {
            $code = $_POST['code'];
            $client = 'wap';
            $user_info = $handle_connect_api->getWxMiniUserInfo($code);
            if (!empty($user_info['unionid'])) {
                $unionid = $user_info['unionid'];
                $model_member = Model('member');
                $member = $model_member->getMemberInfo(array('weixin_unionid' => $unionid));
                $state_data = array();
                $token = 0;
                if (!empty($member)) {//会员信息存在时自动登录
                    $token = $handle_connect_api->getUserToken($member, $client);
                    if ($token) {
                        output_data(array('username' => $member['member_name'], 'nickname' => $member['nickname'], 'userid' => $member['member_id'], 'token' => $token));

                    } else {
                        output_error('会员登录失败');
                    }
                } else {//自动注册会员并登录
                    /* $info_data = $handle_connect_api->wxRegister($user_info, $client);
                    $token = $info_data['token'];
                    $member = $info_data['member'];
                    $state_data['password'] = $member['member_passwd']; */
                    output_data(array('state' => 10, 'SessionKey' => $user_info['session_key'], 'unionid' => $user_info['unionid']));
                }
            } else {
                output_error('登陆失败！', array('state' => 0));
            }
        } else {
            output_error('登陆失败', array('state' => 0));
        }
    }


    /**
     * 微信小程序解密手机号码
     */
    public function wxminphoneWt() {
        include_once "WXBizDataCrypt.php";

        $iv = $_POST['iv'];
        $epdata = $_POST['epdata'];
        $sessionKey = $_POST['skey'];
        $pc = new WXBizDataCrypt($appid, $sessionKey);

        $errCode = $this->MiniDecryptData($epdata, $sessionKey, $iv, $data);

        if ($errCode == 0) {
            output_data(array('info' => $data));
        } else {
            output_error($errCodesmslogin, array('state' => 0));
        }
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function MiniDecryptData($encryptedData, $sessionKey, $iv, &$data) {
        $weixin_appid = C('app_weixin_appid');
        $aesKey = base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return -41003;
        }
        if ($dataObj->watermark->appid != $weixin_appid) {
            return -41003;
        }
        $data = $dataObj;
        return 0;
    }

    /*****会员注册 结束****/


}
