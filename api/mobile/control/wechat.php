<?php
/**
 * 微信接口管理
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
ini_set('always_populate_raw_post_data', -1);

defined('ShopWT') or exit('Access Denied By ShopWT');

include('wechat/wechat.lib.php');

class wechatControl extends mobileHomeControl
{
    private $token;
    private $data = array();
    private $msg;
    private $wx_store_id;
    private $timestamp = TIMESTAMP;
    private $wechat_data = array();
    private $fromUsername = '';
    private $toUsername = '';
    private $msgType = '';
    private $event = '';
    private $eventKey = '';
    private $ticket = '';
    private $keyword = '';
    private $model_member;
    private $model_wechat_user;
    private $model_wechat_setting;
    private $model_wechat_autoreg;
    private $model_wechat_message;
    private $model_wechat_qrcode;
    private $model_wechat_config;
    private $model_wechat_menu;
    private $model_wechat_keyword;
    private $wechat_user_uid;
    private $member_member_id;
    private $notice_info;
    private $wechat_setting;
    private $member_id;

    public function __construct()
    {
        parent::__construct();
        $this->model_member             = Model('member');
        $this->model_wechat_user        = Model('wechat_user');
        $this->model_wechat_setting     = Model('wechat_setting');
        $this->model_wechat_autoreg     = Model('wechat_autoreg');
        $this->model_wechat_message     = Model('wechat_message');
        $this->model_wechat_qrcode      = Model('wechat_qrcode');
        $this->model_wechat_config      = Model('wechat_config');
        $this->model_wechat_menu = Model('wechat_menu');
        $this->model_wechat_keyword     = Model('wechat_keyword');
        $this->notice_info              = '';
        $this->wechat_setting           = array();
    }

    public function indexWt()
    {
        if ($_GET['amp;t']) {
            $wechat_t = $_GET['amp;t'];
        }
        if ($_GET['amp;wx_store_id']) {
            $wechat_wx_store_id = intval($_GET['amp;wx_store_id']);
        }
        if ($wechat_t == 'oauth' || trim($_GET['t'] == 'oauth')) {
            $this->oauthWt();
        }
        $wx_store_id = intval($_GET['wx_store_id']);
        $wx_token = trim($_GET['token']);
        $this->wx_store_id = $wx_store_id;
        $this->token = $wx_token;
        $weixin = new Wechat($this->token);
        $this->data = $weixin->request();
        list($content, $type) = $this->reply($this->data);
        $weixin->response($content, $type);
    }

    //用户信息
    public function user_infoWt()
    {
        $wechat_unionid = !empty($_GET['wechat_unionid']) ? $_GET['wechat_unionid'] : '';
        $store_id = !empty($_GET['store_id']) ? intval($_GET['store_id']) : 0;

        if (!empty($wechat_unionid) && !empty($store_id)) {
            $this->access_token($store_id);
            $wechat_config = $this->model_wechat_config->where(array('user_id' => $store_id))->find();
            $access_token = $wechat_config['access_token'];
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$wechat_unionid&lang=zh_CN";
            $res_json = file_get_contents($url);
            $w_user = json_decode($res_json, true);

            //二次获取
            if ($w_user['errcode'] == '40001') {
                $access_token = $this->new_access_token($store_id);
                $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$wechat_unionid";
                $res_json = file_get_contents($url);
                $w_user = json_decode($res_json, true);
            }

            //去掉微信昵称中的特殊字符和空格
            //$w_user['nickname'] = preg_replace('~\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]~', '', $w_user['nickname']);
            //$w_user['nickname'] = trim($w_user['nickname']);
            Wechat::log('w_user === ' . serialize($w_user));

            /*
            if (empty($w_user['nickname'])) {
                if ($wechat_unionid == 'oo1v-tir7oHXTL42WpwAlNsLTZlc') {
                    $this->model_wechat_user->where(array('wechat_unionid' => $wechat_unionid))->update(array('setp' => 3, 'nickname' => 'empty'));
                }
                exit('nickname is empty');
            }
            */

            $wechat_user_data = array(
                'nickname'       => $w_user['nickname'],
                'sex'            => $w_user['sex'],
                'city'           => $w_user['city'],
                'country'        => $w_user['country'],
                'province'       => $w_user['province'],
                'language'       => $w_user['language'],
                'headimgurl'     => $w_user['headimgurl'],
                'localimgurl'    => $w_user['localimgurl'],
                'subscribe_time' => $w_user['subscribe_time']
            );
            $this->model_wechat_user->where(array('wechat_unionid' => $wechat_unionid))->update($wechat_user_data);

            /**
             * 更新平台账号信息
             */

            /*暂时不考虑去重
            //昵称重复
            $users_q_info = $this->model_member->where(array('member_name' => $w_user['nickname']))->find();
            if ($users_q_info) {
                $w_user_tmp = $w_user['nickname'] . random(6);

                //二次随机，再重复就太有缘分了
                $users_q_info = $this->model_member->where(array('member_name' => $w_user_tmp))->find();
                if ($users_q_info) {
                    $w_user_tmp = $w_user_tmp . random(6);
                }

                $w_user['nickname'] = $w_user_tmp;
            }
            */

            $this->model_member->where(array('wechat_unionid' =>$wechat_unionid))->update(array(
                'member_name' => $w_user['nickname'],
                'member_avatar' => $w_user['headimgurl'],
                'member_sex' => $w_user['sex']
            ));
        }
    }

    //生成菜单
    public function creat_menuWt()
    {
        $this->wx_store_id = 1;
        $wechat = $this->model_wechat_config->where(array('user_id' => $this->wx_store_id))->find();
        $result = $this->curl($wechat['appid'], $wechat['appsecret']);

        if ($result['access_token'] != '') {
            //获取到ACCESS_TOKEN
            $access_token = $result['access_token'];
            $data = $this->getmenu();
            //var_dump(@preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $data));die;
            $msg = $this->curl_menu($access_token, @preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data));
            //var_dump($msg);die;
            if ($msg['errmsg'] == 'ok') {
                exit('创建自定义菜单成功');
            } else {
                $wechat_error= $this->wechat_error($msg['errcode']);
                exit('创建自定义菜单失败!' . $wechat_error);
            }
        } else {
            exit('创建失败,微信AppId或微信AppSecret填写错误!');
        }
    }

    //微信授权
    public function oauthWt()
    {
        $base_site_url = str_replace('/mobile', '', MOBILE_SITE_URL);
        $oauth_id = intval($_REQUEST['oauth_id']);
        if (!$oauth_id) {
            $oauth_id = intval($_REQUEST['amp;oauth_id']);
        }
        Wechat::log('oid === ' . $oauth_id);
        $wechat_oauth = Model('wechat_oauth')->where(array('oid' => $oauth_id))->find();
        Wechat::log('wechat_oauth === ' . serialize($wechat_oauth));
        if (!$wechat_oauth) {
            $error = '非法登陆！';
        } else {
            $wx_store_id = $wechat_oauth['store_id'];
        }
        $m_config = $this->model_wechat_config->where(array('user_id' => $wx_store_id))->find();

        if (Wechat::isWeixin()) {
            $url = $base_site_url . Wechat::get_current_url();
            if ($m_config['appid'] && $m_config['appsecret']) {
                $wx_code = trim($_REQUEST['code']);
                $wx_status = intval($_REQUEST['state']);
                if ($wx_code && $wx_status) {
                    //微信端回跳回wap
                    $weixin = new wechatOauth($m_config['appid'], $m_config['appsecret'], $url);
                    $wx_info = $weixin->scope_get_userinfo($wx_code);
                }

                if ($wx_info && $wx_info['openid']) {
                    $wechat_unionid = $wx_info['openid'];

                    //查询账号信息
                    $member_info = $this->model_member->getMemberInfo(array('wechat_unionid' => $wechat_unionid));
                    if (empty($member_info)) {
                        $error = '登陆失败';
                    } else {
                        if ($token = $this->checkMemberInfo($member_info)) {
                            if (strpos($wechat_oauth['contents'], '?') === false) {
                                $target_url = $wechat_oauth['contents'] . "?key=" . $token;
                            } else {
                                $target_url = $wechat_oauth['contents'] . "&key=" . $token;
                            }
                            redirect($target_url);
                        } else {
                            $error = '获取token失败！';
                        }
                    }
                    //header("Content-type: text/html; charset=utf-8;");
                    exit('<html><script>alert("' . $error . '")</script></html>');
                } else {
                    //跳转至微信的授权页
                    $url = $base_site_url . Wechat::get_current_url();
                    $weixin = new wechatOauth($m_config['appid'], $m_config['appsecret'], $url);
                    $wx_url = $weixin->scope_get_code();
                    redirect($wx_url);
                }
            } else {
                exit('<html><script>alert("商家还未开通微信功能"")</script></html>');
            }
        } else {
            exit('<html><script>alert("暂不支持微信功能"")</script></html>');
        }
    }

    //微信被动回复
    private function reply($data)
    {
        $this->wechat_data  = $data;
        $this->fromUsername = $this->wechat_data['FromUserName'];
        $this->toUsername   = $this->wechat_data['ToUserName'];
        $this->msgType      = $this->wechat_data['MsgType'];
        $this->event        = $this->wechat_data['Event'];
        $this->eventKey     = $this->wechat_data['EventKey'];
        $this->keyword      = trim($this->wechat_data['Content']);
        $this->ticket       = $this->wechat_data['Ticket'];

        //关键词
        if (empty($this->keyword )) {
            $this->keyword = trim($_GET['keyword']);
        }

        //微信设置
        $this->wechat_setting = $this->model_wechat_setting->select();
        $this->wechat_setting = array_under_reset($this->wechat_setting, 'name');

        //搜索推荐
        $wechat_setting_plustj =$this->wechat_setting['plustj']['value'];

        //PC网址
        $wechat_setting_baseurl = $this->wechat_setting['baseurl']['value'];

        //WAP网址
        $wechat_setting_murl = $this->wechat_setting['murl']['value'];

        //图片路径
        $wechat_setting_imgpath = $this->wechat_setting['imgpath']['value'];
        $base_img_path = $wechat_setting_baseurl;
        if ($wechat_setting_imgpath == 'local') {
            $img_url = $wechat_setting_murl;
            if (empty($img_url)) {
                $tmp_img_array = explode('.', $wechat_setting_baseurl);
                $tmp_array = array('http://m', 'http://mobile');
                if (in_array($tmp_img_array[0], $tmp_array)) {
                    $base_img_path = 'http://www.' . $tmp_img_array[1] . '.' . $tmp_img_array[2];
                }
            }
        }

        //微信Oauth
        $oauth_location = MOBILE_SITE_URL . '/index.php?act=wechat.php?op=oauth&uri=';

        if ('unsubscribe' !== $data['Event']) {
            $this->wechat_user_uid = $this->checkWechatUserExistis();
            $this->member_member_id = $this->checkMemeberExistis();
        }

        //事件消息
        if ('subscribe' == $data['Event']) { //关注事件
            //未关注
            if (strlen($data['EventKey']) == 0) {
                $data['EventKey'] = 'follow';
            } else {
                //事件KEY值，qrscene_为前缀，后面为二维码的参数值
                $qrscene = $data['EventKey'];
                $scene_id_arr = explode("qrscene_", $qrscene);
                $scene_id = $scene_id_arr[1];

                //关注次数自增
                $this->model_wechat_qrcode->where(array('scene_id' => $scene_id))->setInc('subscribe', 1);

                $wechat_qrcode = $this->model_wechat_qrcode->where(array('scene_id' => $scene_id))->find();

                $member_info = $this->model_member->where(array('member_id' => $scene_id))->find();

                if (!empty($member_info)) {
                    $data['EventKey'] = 'affiliate_推荐成功_' . $scene_id;
                } else {
                    $data['EventKey'] = $wechat_qrcode['function'];
                }
            }
        } elseif ('unsubscribe' == $data['Event']) { //取消关注
            $subscribe = 0;
        } elseif ('SCAN' == $data['Event']) { //扫码

            $member_info = $this->model_member->getMemberInfo(array('wechat_unionid' => $this->fromUsername));
            if (!empty($member_info) && !empty($member_info['inviter_id']) && $member_info['inviter_id'] != $member_info['member_id']) {
                $msgType = "text";
                $parent_info = $this->model_member->getMemberInfo(array('member_id' => $member_info['inviter_id']));
                $contentStr .= "您好，您已经有了上级！您的上级是：" . $parent_info['member_name'];

                //写入消息表
                $insert_wechat_message = array(
                    'wechat_unionid' => $this->fromUsername,
                    'w_message' => $contentStr,
                    'belong' => $this->wechat_user_uid,
                    'dateline' => $this->timestamp
                );
                $this->model_wechat_message->insert($insert_wechat_message);

                return array($contentStr, $msgType);
            }

            $qrscene = $data['EventKey'];
            $inviter_id = $scene_id = $qrscene;

            //扫描次数+1
            $this->model_wechat_qrcode->where(array('scene_id' => $scene_id))->setInc('scan', 1);

            $wechat_qrcode = $this->model_wechat_qrcode->where(array('scene_id' => $scene_id))->find();
            Wechat::log('wechat_qrcode === ' . serialize($wechat_qrcode));
            if ($wechat_qrcode['affiliate'] >= 1) {
                $this->keyword = $data['EventKey'] = 'affiliate_推荐成功_' . $wechat_qrcode['affiliate'];
            } else {
                $this->keyword = $data['EventKey'] = 'affiliate_推荐成功_' . $wechat_qrcode['function'];
            }
        }

        //消息类型为事件时关键词为事件的值
        if ($data['MsgType'] == 'event') {
            $this->keyword = $data['EventKey'];
        }

        //页面建设中
        if ($this->keyword == 'blank') {
            return array('页面建设中', 'text');
        }

        //用户信息
        if ($this->keyword == 'follow' || $this->keyword == 'getuser') {
            $msgType = "text";
            $contentStr = '';
            if (!empty($notice_info)) {
                $contentStr .= $notice_info;
            } else {
                $member_info = $this->model_member->where(array('wechat_unionid' => $this->fromUsername))->find();
                $contentStr .= "您好，您已经注册过！账号是：" . $member_info['member_name'] . "，默认密码是：" . $member_info['wechat_password'];
            }

            //写入消息表
            $insert_wechat_message = array(
                'wechat_unionid' => $this->fromUsername,
                'w_message' => $contentStr,
                'belong' => $this->wechat_user_uid,
                'dateline' => $this->timestamp
            );
            $this->model_wechat_message->insert($insert_wechat_message);

            //完善信息
            $result = $this->updateMemberInfo($this->fromUsername, $wechat_setting_baseurl, $this->wx_store_id);
            if ($result) {
                return array($contentStr, $msgType);
            } else {
                return array('网络繁忙，请稍后再试！', 'text');
            }
        }
        Wechat::log('wechat_data === ' . serialize($data));
        //点击自定义菜单事件
        if ('CLICK' == $data['Event']) {
            //我的二维码
            if ($data['EventKey'] == 'qrcode') {
                $ArticleCount = 1;
                $scene_id = $this->_member_id;
                $affiliate = $this->_member_id;
                $type = 'invite';
                $wechat_qrcode = $this->model_wechat_qrcode->where(array('scene_id' => $scene_id))->find();
                $qr_path = $wechat_qrcode['qr_path'];

                $member_info = $this->model_member->where(array('member_id' => $scene_id))->find();
                $scene = $user_name = $member_info['member_name'];

                //存在二维码的情况下直接返回
                $qrcode = $this->getQrcodeByMemberId($member_info['member_id']);
                Wechat::log('qrcode === ' . serialize($qrcode));
                if (!empty($qr_path) && file_exists($qrcode['local']) && $token = $this->checkMemberInfo($member_info)) {
                    $surl = $qr_path;
                    $real_surl = $qr_path;

                    $link_url = WAP_SITE_URL . '/tmpl/member/distribution_qrcode.html?key=' . $token;

                    $newsArray[] = array (
                        'Title' => '您已获得二维码',
                        'Description' => '扫描二维码获得推荐关系',
                        'PicUrl' => $qrcode['url'],
                        'Url' => $link_url
                    );

                    return array($newsArray, 'news');

                } else {
                    $action_name = "QR_LIMIT_SCENE";
                    //永久二维码
                    $expire_time = 0;
                    $json_arr = array('action_name'=>$action_name,'action_info'=>array('scene'=>array('scene_id'=>$scene_id)));
                    $data = json_encode($json_arr);
                    $this->access_token($this->wx_store_id);

                    $ret = $this->model_wechat_config->where(array('user_id' => $this->wx_store_id))->find();
                    $access_token = $ret['access_token'];
                    if (strlen($access_token) >= 64) {
                        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
                        $res_json = $this->JsonPost($url, $data);
                        Wechat::log('res_json === ' . $res_json);
                        $json = json_decode($res_json);
                    }

                    $ticket = $json->ticket;

                    if ($ticket) {
                        $ticket_url = urlencode($ticket);
                        $ticket_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket_url;
                        $imageinfo = $this->downloadImageFromWechat($ticket_url);
                        $dir_path = BASE_UPLOAD_PATH . DS . ATTACH_DISTRIBUTION . DS .'qrcode' . DS . $this->_member_id . DS;
                        if (!is_dir($dir_path)) {
                            mk_dir($dir_path, 0755);
                        }
                        $path = $dir_path . $this->timestamp . '.jpg';
                        $surl = UPLOAD_SITE_URL . DS . ATTACH_DISTRIBUTION . DS .'qrcode' . DS . $this->_member_id . DS . $this->timestamp . '.jpg';

                        if ($this->_member_id) {
                            $local_file = fopen($path, 'a');
                            if (false !== $local_file) {
                                if (false !== fwrite($local_file, $imageinfo)) {
                                    $qrcode_complete = fclose($local_file);

                                    //将生成的二维码图片的地址放到数据库中
                                    if ($qrcode_complete) {
                                        $wechat_qrcode_data = array(
                                            'type' => $type,
                                            'action_name' => $action_name,
                                            'ticket' => $ticket,
                                            'scene_id' => $scene_id,
                                            'scene' => $scene,
                                            'qr_path' => $surl,
                                            'function' => $type,
                                            'affiliate' => $affiliate,
                                            'endtime' => $expire_time,
                                            'dateline' => $this->timestamp
                                        );
                                        $this->model_wechat_qrcode->insert($wechat_qrcode_data);
                                    }
                                }
                            }
                        }
                    }
                }

                $from_user = $scene;
                $rand_file = $scene_id . '.png';
                $att_target_file = 'qr-image-' . $rand_file;
                $att_head_cache_file = 'head-image-' . $rand_file;

                /**
                 * 纯二维码图片
                 * @var $qrcode_img_path 物理路径
                 * @var $qrcode_img_url  网络地址
                 */
                $qrcode_img_path = $path;
                $qrcode_img_url = $surl;

                /**
                 * 合成后的二维码图片
                 * @var $qrcode_img_last_path 物理路径
                 * @var $qrcode_img_last_url  网络地址
                 */
                $qrcode_img_last_path = BASE_UPLOAD_PATH . DS . ATTACH_DISTRIBUTION . DS . 'qrcode' . DS . $this->_member_id . DS . 'share.png';
                $qrcode_img_last_url = UPLOAD_SITE_URL . DS . ATTACH_DISTRIBUTION .DS . 'qrcode' .DS . $this->_member_id . DS . 'share.png';

                //会员头像图片
                $head_cache_file = BASE_UPLOAD_PATH . DS . ATTACH_DISTRIBUTION . DS . 'qrcode' . DS . $this->_member_id . DS . $att_head_cache_file;

                //事先上传的背景图片
                $bg_file = BASE_UPLOAD_PATH . DS . ATTACH_DISTRIBUTION . DS . "bg.jpg";

                $ch['qrleft'] = 159;
                $ch['qrtop'] = 403;
                $ch['qrwidth'] = 248;
                $ch['qrheight'] = 248;
                $ch['avatarenable'] = 1;
                $ch['nameenable'] = 1;
                $ch['namesize'] = 18;
                $ch['nameleft'] = 175;
                $ch['nametop'] = 48;
                $ch['avatarleft'] = 62;
                $ch['avatartop'] = 32;
                $ch['avatarwidth'] = 74;
                $ch['avatarheight'] = 74;

                $enableHead = $ch['avatarenable'];
                $enableName = $ch['nameenable'];

                //生成带有二维码的图片
                if ($this->_member_id && $qrcode_complete) {
                    $this->mergeImage($bg_file, $qrcode_img_path, $qrcode_img_last_path, array('left' => $ch['qrleft'], 'top' => $ch['qrtop'], 'width' => $ch['qrwidth'], 'height' => $ch['qrheight']));
                }

                $myheadimg = $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername))->find();

                if (!empty($myheadimg)) {
                    //允许姓名显示
                    if ($enableName) {
                        if (strlen($myheadimg['nickname']) > 0) {
                            //在指定的位置写入姓名昵称
                            if ($this->_member_id) {
                                $this->writeText($qrcode_img_last_path, $qrcode_img_last_path, '我是' . $myheadimg['nickname'], array('size' => $ch['namesize'], 'left' => $ch['nameleft'], 'top' => $ch['nametop']));
                            }
                        }
                    }

                    //允许头像显示
                    if ($enableHead) {
                        if (!empty($myheadimg['headimgurl'])) {
                            //将头像加入到二维码图片指定位置中
                            if ($this->_member_id) {
                                $this->mergeImage($qrcode_img_last_path, $bild, $qrcode_img_last_path, array('left' => $ch['avatarleft'], 'top' => $ch['avatartop'], 'width' => $ch['avatarwidth'], 'height' => $ch['avatarheight']));
                            }
                        }
                    }
                }

                $filedata = array("media" => "@" . $qrcode_img_last_path);
                $this->access_token($this->wx_store_id);
                $wechat_config = $this->model_wechat_config->where(array('user_id' => $this->wx_store_id))->find();
                $access_token = $wechat_config['access_token'];

                if (strlen($access_token) >= 64) {
                    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=image';
                    $res_json = $this->https_request($url, $filedata);
                    $json = json_decode($res_json);
                }

                $msgType = "image";
                $contentStr = '二维码操作 ';
                if ($json->errcode) {
                    $contentStr = $json->errcode . $json->errmsg;
                } else {
                    $contentStr = $json->media_id;
                }

                //写入消息表
                $insert_wechat_message = array(
                    'wechat_unionid' => $this->fromUsername,
                    'w_message' => $contentStr,
                    'belong' => $wechat_user_uid,
                    'dateline' => $this->timestamp
                );
                $this->model_wechat_message->insert($insert_wechat_message);

                //完善信息
                $result = $this->updateMemberInfo($this->fromUsername, $wechat_setting_baseurl, $this->wx_store_id);
                if ($result) {
                    return array($contentStr, $msgType);
                } else {
                    return array('网络繁忙，请稍后再试！', 'text');
                }

            } else {
                $custom_menu = $this->model_wechat_menu->where(array('store_id' => $this->wx_store_id, 'token' => $this->token, 'keyvalue' => $data['EventKey']))->find();

                if (is_array($custom_menu)) {
                    if ($custom_menu['type'] == 0) {
                        $data_keyword = $this->model_wechat_keyword->where(array('user_id' => $this->wx_store_id, 'iskey' => 1, 'token' => $this->token, 'keyword' => array('like', '%' . $custom_menu['keyword'] . '%')))->find();
                        if (is_array($data_keyword)) {
                            if ($data_keyword['type'] == 2) {
                                $titles = unserialize($data_keyword['titles']);//标题
                                $imageinfo = unserialize($data_keyword['imageinfo']);//图片
                                $linkinfo = unserialize($data_keyword['linkinfo']);//链接
                                for ($i = 0; $i < count($titles); $i++) {
                                    $return[] = array($titles[$i], $titles[$i], $imageinfo[$i], $this->wechat_links($linkinfo[$i]));
                                }
                                return array($return, 'news');
                            } else {
                                return array($this->wechat_content($data_keyword['keycontent']), 'text');
                            }
                        }
                    }
                } else {
                    return array('未查到相关信息', 'text');
                }
            }
        }

        Wechat::log('data === ' . serialize($data));

        /**
         * 扫码推荐，建立推荐关系
         */
        if ($wechat_qrcode['function'] == 'invite') {
            $aff_arr = explode('_', $this->keyword);
            Wechat::log('aff_arr === ' . serialize($aff_arr));
            $inviter_id = $aff_arr[2];
            if ($aff_arr[0] == 'affiliate') {
                if (!empty($inviter_id)) {
                    $aff_db = $this->model_member->where(array('member_id' => $inviter_id))->find();

                    //检查关系合法性
                    $checkRelationship = true;
                    if ($aff_db['wechat_unionid'] == $this->fromUsername) {
                        $checkRelationship = false;
                    }
                    Wechat::log('checkRelationship === ' . $checkRelationship);

                    /**
                     * 1、上下级关系绑定不能改变
                     * 2、验证上下级关系，找出自己所有的下级
                     * 3、自己有下级后不能成为别人的下级
                     */
                    $member_info = $this->model_member->where(array('wechat_unionid' => $this->fromUsername))->find();
                    $wechat_user = $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername))->find();
                    $parent_id = $member_info['inviter_id'];
                    $child_list = $this->model_member->where(array('inviter_id' => $member_info['member_id']))->select();

                    if (empty($parent_id)) {
                        if (!empty($member_info) && $checkRelationship) {
                            //绑定推荐人
                            $this->model_member->where(array('wechat_unionid'=> $this->fromUsername))->update(array('inviter_id' => $inviter_id));
                            $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername))->update(array('affiliate' => $inviter_id));

                            //查询上级昵称
                            $parent_member = $this->model_member->where(array('member_id' => $inviter_id))->find();
                            $parent_wechat_unionid = $parent_member['wechat_unionid'];
                            $parent_wechat_user = $this->model_wechat_user->where(array('wechat_unionid' => $parent_wechat_unionid))->find();
                            $parent_wechat_nickname = $parent_wechat_user['nickname'];

                            //查询自己的昵称
                            $nick_name = $wechat_user['nickname'];

                            //查询网站有多少会员
                            $num_user = $this->model_member->getMemberCount();

                            //查询店铺名字
                            $shop_name = C('site_name');

                            //新增扫描关注
                            $up_uid = $inviter_id;
                            $msgType = "text";
                            $contentStr = $nick_name . "您好！恭喜您由" . $parent_wechat_nickname . "推荐成为" . $shop_name . "的会员！";

                            //写入消息表
                            $insert_wechat_message = array(
                                'wechat_unionid' => $this->fromUsername,
                                'w_message' => $contentStr,
                                'belong' => $wechat_user_uid,
                                'dateline' => $this->timestamp
                            );
                            $this->model_wechat_message->insert($insert_wechat_message);

                            //完善信息
                            //$this->updateMemberInfo($this->fromUsername, $wechat_setting_baseurl, $this->wx_store_id);

                            return array($contentStr, $msgType);

                        } else {
                            $msgType = "text";
                            $contentStr = "错误提示：推荐关系不合法，自己不能成为自己的下级。";

                            //写入消息表
                            $insert_wechat_message = array(
                                'wechat_unionid' => $this->fromUsername,
                                'w_message' => $contentStr,
                                'belong' => $wechat_user['uid'],
                                'dateline' => $this->timestamp
                            );
                            $this->model_wechat_message->insert($insert_wechat_message);

                            //完善信息
                            $result = $this->updateMemberInfo($this->fromUsername, $wechat_setting_baseurl, $this->wx_store_id);
                            if ($result) {
                                return array($contentStr, $msgType);
                            } else {
                                return array('网络繁忙，请稍后再试！', 'text');
                            }
                        }
                    } else {
                        //查询上级昵称
                        $parent_member = $this->model_member->where(array('member_id' => $parent_id))->find();
                        $parent_wechat_unionid = $parent_member['wechat_unionid'];
                        $parent_wechat_user = $this->model_wechat_user->where(array('wechat_unionid' => $parent_wechat_unionid))->find();
                        $parent_wechat_nickname = $parent_wechat_user['nickname'];

                        $msgType = "text";
                        $contentStr = "您已经有上级了哦，您的上级是" . $parent_wechat_nickname;

                        //写入消息表
                        $insert_wechat_message = array(
                            'wechat_unionid' => $this->fromUsername,
                            'w_message' => $contentStr,
                            'belong' => $wechat_user['uid'],
                            'dateline' => $this->timestamp
                        );
                        $this->model_wechat_message->insert($insert_wechat_message);

                        //完善信息
                        //$this->updateMemberInfo($this->fromUsername, $wechat_setting_baseurl, $this->wx_store_id);

                        return array($contentStr, $msgType);
                    }
                }
            } else {
                //关键字自动回复以及消息自动回复
                $data_keyword = $this->model_wechat_keyword->where(array('token' => $this->token, 'iskey' => 1, 'user_id' => $this->wx_store_id, 'keyword' => array('like', '%' . $this->keyword . '%')))->find();
                //找到关键词
                if (is_array($data_keyword) && !empty($data_keyword)) {
                    if ($data_keyword['type'] == 2) {
                        $titles = unserialize($data_keyword['titles']);//标题
                        $imageinfo = unserialize($data_keyword['imageinfo']);//图片
                        $linkinfo = unserialize($data_keyword['linkinfo']);//链接
                        for ($i = 0; $i < count($titles); $i++) {
                            $return[] = array($titles[$i], $titles[$i],$imageinfo[$i], $this->wechat_links($linkinfo[$i]));
                        }
                        return array($return, 'news');
                    } else {
                        return array($this->wechat_content($data_keyword['kecontent']), 'text');
                    }
                } else {
                    return array('系统升级中', 'text');
                }
            }
        }
    }

    //获取菜单
    private function getmenu()
    {
        $account_id = $this->wx_store_id;
        $keyword = array();

        $topmemu = $this->model_wechat_menu->where(array('store_id' => $account_id, 'if_show'=>1, 'parent_id'=>0))->select();

        foreach ($topmemu as $key) {
            $nextmenu = $this->model_wechat_menu->where(array('store_id'=> $account_id, 'if_show'=>1, 'parent_id'=>$key['cate_id']))->select();
            //没有下级栏目
            if (count($nextmenu) != 0) {
                foreach ($nextmenu as $key2) {
                    if ($key2['type'] == 1) {
                        $kk[] = array('type'=>'view','name'=>$key2['cate_name'],'url'=>$key2['keyvalue']);
                    } else {
                        $kk[] = array('type'=>'click','name'=>$key2['cate_name'],'key'=>$key2['keyvalue']);
                    }
                }

                $keyword['button'][] = array('name'=>$key['cate_name'],'sub_button'=>$kk);
                $kk='';
            } else {
                if ($key['type'] == 1) {
                    $keyword['button'][] = array('type'=>'view','name'=>$key['cate_name'],'url'=>$key['keyvalue']);
                } else {
                    $keyword['button'][] = array('type'=>'click','name'=>$key['cate_name'],'key'=>$key['keyvalue']);
                }
            }
        }
        return json_encode($keyword);
    }

    //获取菜单
    private function curl_menu($ACCESS_TOKEN, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACCESS_TOKEN);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
        }
        curl_close($ch);
        $arr = json_decode($tmpInfo, true);
        return $arr;
    }

    //微信错误代码
    private function wechat_error($error)
    {
        $wechat_error = array(
            '-1'=>'系统繁忙',
            '0'=>'请求成功',
            '40001'=>'验证失败',
            '40002'=>'不合法的凭证类型',
            '40003'=>'不合法的OpenID',
            '40013'=>'不合法的APPID',
            '40014'=>'不合法的access_token',
            '40015'=>'不合法的菜单类型',
            '40016'=>'不合法的按钮个数',
            '40017'=>'不合法的按钮个数',
            '40018'=>'不合法的按钮名字长度',
            '40019'=>'不合法的按钮KEY长度',
            '40020'=>'不合法的按钮URL长度',
            '40021'=>'不合法的菜单版本号',
            '40022'=>'不合法的子菜单级数',
            '40023'=>'不合法的子菜单按钮个数',
            '40024'=>'不合法的子菜单按钮类型',
            '40025'=>'不合法的子菜单按钮名字长度',
            '40026'=>'不合法的子菜单按钮KEY长度',
            '40027'=>'不合法的子菜单按钮URL长度',
            '40028'=>'不合法的自定义菜单使用用户',
            '41001'=>'缺少access_token参数',
            '41002'=>'缺少appid参数',
            '41003'=>'缺少refresh_token参数',
            '41004'=>'缺少secret参数',
            '41005'=>'缺少多媒体文件数据',
            '41006'=>'缺少media_id参数',
            '41007'=>'缺少子菜单数据',
            '42001'=>'access_token超时',
            '43001'=>'需要GET请求',
            '43002'=>'需要POST请求',
            '43003'=>'需要HTTPS请求',
            '45010'=>'创建菜单个数超过限制',
            '46002'=>'不存在的菜单版本',
            '46003'=>'不存在的菜单数据',
            '47001'=>'解析JSON/XML内容错误'
        );
        return $wechat_error[$error];
    }

    //文本回复得到wecha_id
    private function wechat_content($contentStr)
    {
        $oauth_location = WAP_SITE_URL . "/oauth.html?wecha_id=" . $this->fromUsername . "&token=" . $this->token . "&uri=";
        $str = $contentStr;
        $reg = '/\shref=[\'\"]([^\'"]*)[\'"]/i';
        //正则：得到href的地址
        preg_match_all($reg, $str, $out_array);
        $src_array = $out_array[1];
        //存在
        if (!empty($src_array)) {
            $comment=$src_array[0];
            if (stristr($comment, $_SERVER['SERVER_NAME'])) {
                if (stristr($comment, "?")) {
                    $links = $oauth_location . $comment;
                    $contentStr = str_replace($comment, $links, $str);
                } else {
                    $links = $oauth_location . $comment;
                    $contentStr = str_replace($comment, $links, $str);
                }
            }
        }

        return $contentStr;
    }

    //链接处理
    private function wechat_links($linkinfo)
    {
        $oauth_location = WAP_SITE_URL . "/oauth.html?wecha_id=" . $this->fromUsername . "&token=" . $this->token . "&uri=";
        Wechat::log('oauth_location === ' . $oauth_location);
        if (stristr($linkinfo, $_SERVER['SERVER_NAME'])) {
            if (stristr($linkinfo, "?")) {
                $links = $oauth_location . $linkinfo;
            } else {
                $links = $oauth_location . $linkinfo;
            }
        } else {
            $links = $linkinfo;
        }

        return $links;
    }

    /**
     * 注册会员
     * @param  string  $wechat_password     明文密码
     * @param  string  $wechat_password_md5 加密密码
     * @param  boolean $bind                是否绑定
     */
    private function registerMember($wechat_password, $wechat_password_md5, $bind = false)
    {
        $member_data = array(
            'member_name' => $this->fromUsername,
            'wechat_unionid' => $this->fromUsername,
            'wechat_password' => $wechat_password,
            'member_passwd' => $wechat_password_md5,
            'wechat_bind' => 0,
            'member_time' => $this->timestamp
        );

        //注册会员
        $member_id = $this->model_member->insert($member_data);

        //绑定会员
        if ($bind) {
            $this->model_member->where(array('wechat_unionid' => $this->fromUsername))->update(array('wechat_bind' => 1));
        }

        return $member_id;
    }

    private function updateMemberInfo($wechat_unionid, $wechat_setting_baseurl, $store_id)
    {
        $url_arr = explode('/', $wechat_setting_baseurl);
        if (count($url_arr) == 5) {
            $target_url = $url_arr[2];
            $append = '/' . $url_arr[3];
            return $this->updateInfoUrl($target_url, $wechat_unionid, $append, $store_id);
        } else {
            $target_url = $url_arr[2];
            return $this->updateInfo($target_url, $wechat_unionid, $store_id);
        }
    }

    private function updateInfo($host, $wechat_unionid, $store_id)
    {
        if (function_exists(fsockopen)) {
            $fp = fsockopen("$host", 80, $errno, $errstr, 10);
        } else {
            $fp = pfsockopen("$host", 80, $errno, $errstr, 10);
        }
        $url = "/mobile/index.php?act=wechat&op=user_info&wechat_unionid=$wechat_unionid&store_id=" . $store_id;
        if (!$fp) {
            //echo "$errstr $errno <br />\n";
            return false;
        } else {
            $out = "GET $url HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            $inheader = 1;
            $result = '';
            while (!feof($fp)) {
                $line = fgets($fp, 1024);
                if ($inheader && ($line == "\n" || $line == "\r\n")) {
                    $inheader = 0;
                }
                if ($inheader == 0) {
                    $result .= trim($line);
                }
            }
            fclose($fp);

            return true;
        }
    }

    private function updateInfoUrl($host, $wechat_unionid, $append, $store_id)
    {
        if (function_exists(fsockopen)) {
            $fp = fsockopen("$host", 80, $errno, $errstr, 10);
        } else {
            $fp = pfsockopen("$host", 80, $errno, $errstr, 10);
        }
        $url = $append . "/mobile/index.php?act=wechat&op=user_info&wechat_unionid=$wechat_unionid&store_id=" . $store_id;
        if (!$fp) {
            //echo "$errstr $errno <br />\n";
            return false;
        } else {
            $out = "GET $url HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            $inheader = 1;
            $result = '';
            while (!feof($fp)) {
                $line = fgets($fp, 1024);
                if ($inheader && ($line == "\n" || $line == "\r\n")) {
                    $inheader = 0;
                }
                if ($inheader == 0) {
                    $result .= trim($line);
                }
            }
            fclose($fp);

            return true;
        }
    }

    private function send($ToUserName, $FromUserName, $content)
    {
        $content = $this->wechat_content($content);
        $str = "
            <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>
        ";
        return $resultStr = sprintf($str, $FromUserName, $ToUserName, $this->timestamp, $content);
    }

    private function curl($appid, $secret)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
        }
        curl_close($ch);
        $arr= json_decode($tmpInfo, true);
        return $arr;
    }

    private function user_info($access_token, $openid)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
        }
        curl_close($ch);
        $arr= json_decode($tmpInfo, true);
        return $arr;
    }

    private function access_token($store_id)
    {
        $ret = $this->model_wechat_config->where(array('user_id' => $store_id))->find();
        $appid = $ret['appid'];
        $appsecret = $ret['appsecret'];
        $dateline = $ret['dateline'];
        if (($this->timestamp - $dateline) > 600) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $ret_json = $this ->curl_get_contents($url);
            $ret = json_decode($ret_json);
            if ($ret->access_token) {
                $data = array();
                $data['access_token'] = $ret->access_token;
                $data['dateline'] = $this->timestamp;
                $this->model_wechat_config->where(array('user_id' => $store_id))->update($data);
            }
        }
    }

    private function new_access_token($store_id)
    {
        $ret = $this->model_wechat_config->where(array('user_id' => $store_id))->find();
        $appid = $ret['appid'];
        $appsecret = $ret['appsecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $ret_json = $this->curl_get_contents($url);
        $ret = json_decode($ret_json);
        if ($ret->access_token) {
            $data = array();
            $data['access_token'] = $ret->access_token;
            $data['dateline'] = $this->timestamp;
            $this->model_wechat_config->where(array('user_id' => $store_id))->update($data);
        }
        return $ret->access_token;
    }

    private function curl_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    //curl发送json请求
    private function JsonPost($url, $jsonData) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            Wechat::log('curl falied. Error Info === ' . curl_error($curl));
        }
        curl_close($curl);
        return $result;
    }

    private function downloadImageFromWechat($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $return_content;
    }

    private function mergeImage($bg, $qr, $out, $param)
    {
        list($bgWidth, $bgHeight) = getimagesize($bg);
        list($qrWidth, $qrHeight) = getimagesize($qr);
        extract($param);
        $bgImg = $this->imagecreate2($bg);
        $qrImg = $this->imagecreate2($qr);
        imagecopyresized($bgImg, $qrImg, $left, $top, 0, 0, $width, $height, $qrWidth, $qrHeight);
        ob_start();
        imagejpeg($bgImg, null, 100);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($bgImg);
        imagedestroy($qrImg);
        $fh = fopen($out, 'w+');
        fwrite($fh, $contents);
        fclose($fh);
    }

    private function writeText($bg, $out, $text, $param = array())
    {
        list($bgWidth, $bgHeight) = getimagesize($bg);
        extract($param);
        $im = imagecreatefromjpeg($bg);
        $black = imagecolorallocate($im, 0, 0, 0);
        $font = BASE_UPLOAD_PATH . DS . ATTACH_DISTRIBUTION . DS . 'font' . DS . 'msyhbd.ttf';
        $white = imagecolorallocate($im, 255, 255, 255);
        imagettftext($im, $size, 0, $left, $top + $size / 2, $white, $font, $text);
        ob_start();
        imagejpeg($im, null, 100);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($im);
        $fh = fopen($out, 'w+');
        fwrite($fh, $contents);
        fclose($fh);
    }

    private function curl_file_get_contents($durl)
    {
        $r = null;
        if (function_exists('curl_init') && function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $durl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $r = curl_exec($ch);
            curl_close($ch);
        }
        return $r;
    }

    private function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    private function imagecreate2($bg)
    {
        $bgImg = @imagecreatefromjpeg($bg);
        if (false == $bgImg) {
            $bgImg = @imagecreatefrompng($bg);
        }
        if (false == $bgImg) {
            $bgImg = @imagecreatefromgif($bg);
        }
        return $bgImg;
    }

    private function addCookie($name, $value, $expire = '3600', $path = '', $domain = '', $secure = false)
    {
        if (empty($path)) {
            $path = '/';
        }
        if (empty($domain)) {
            $domain = SUBDOMAIN_SUFFIX ? SUBDOMAIN_SUFFIX : '';
        }
        $expire = intval($expire) ? intval($expire) : (intval(SESSION_EXPIRE) ? intval(SESSION_EXPIRE) : 3600);
        $result = setcookie($name, $value, time() + $expire, $path, $domain, $secure);
        $_COOKIE[$name] = $value;
    }

    /**
     * 登录生成token
     */
    private function _get_token($member_id, $member_name, $client)
    {
        $model_mb_user_token = Model('mb_user_token');

        //生成新的token
        $mb_user_token_info = array();
        $token = md5($member_name . strval(TIMESTAMP) . strval(rand(0, 999999)));
        $mb_user_token_info['member_id'] = $member_id;
        $mb_user_token_info['member_name'] = $member_name;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = $client;

        $result = $model_mb_user_token->addMbUserToken($mb_user_token_info);

        if ($result) {
            return $token;
        } else {
            return null;
        }

    }

    //获取二维码的本地和网络地址
    private function getQrcodeByMemberId($member_id)
    {
        $info = array(
            'local' => BASE_UPLOAD_PATH . DS . ATTACH_DISTRIBUTION . DS . 'qrcode' . DS . $member_id . DS . 'share.png',
            'url' => UPLOAD_SITE_URL . DS . ATTACH_DISTRIBUTION . DS . 'qrcode' . DS . $member_id .DS . 'share.png'
        );

        return $info;
    }

    private function checkMemberInfo($member_info)
    {
        if (!$member_info['member_state']) {
            //$error = '账号被停用';
            return false;
        }
        //添加会员积分
        $this->model_member->addPoint($member_info);
        //添加会员经验值
        $this->model_member->addExppoint($member_info);
        $update_info = array(
            'member_login_num'=> ($member_info['member_login_num']+1),
            'member_login_time'=> TIMESTAMP,
            'member_old_login_time'=> $member_info['member_login_time'],
            'member_login_ip'=> getIp(),
            'member_old_login_ip'=> $member_info['member_login_ip']
        );
        $this->model_member->editMember(array('member_id'=>$member_info['member_id']), $update_info);
        //生成新的token
        $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], 'wechat');
        if ($token) {
            //卖家信息
            $seller_info = Model('seller')->getSellerInfo(array('member_id' => $member_info['member_id']));
            $member_info['store_id'] = $seller_info['store_id'];
            $store_id = $seller_info['store_id'];
            //店铺信息
            $store_info = Model('store')->getStoreInfo(array('member_id' => $member_info['member_id']));
            $this->addCookie('username', $member_info['member_name']);
            $this->addCookie('userid', $member_info['member_id']);
            $this->addCookie('key', $token);
            return $token;
        } else {
            return false;

        }
    }

    /**
     * 检查微信用户是否存在，不存在直接创建
     * @return [type] [description]
     */
    private function checkWechatUserExistis()
    {
        $wechat_user = $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername))->find();
        if (!$wechat_user) {
            if ($this->fromUsername) {
                $wechat_user = array(
                    'step' => 1,
                    'subscribe' => 1,
                    'wechat_unionid' => $this->fromUsername,
                    'dateline' => $this->timestamp
                );
                $wechat_user_uid = $this->model_wechat_user->insert($wechat_user);
            }
        } else {
            $wechat_user_data = array(
                'dateline' => $this->timestamp
            );
            $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername))->update($wechat_user_data);
            $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername))->setInc('subscribe', 1);
            $wechat_member_name = $wechat_user['member_name'];
            $wechat_user_uid = $wechat_user['uid'];

            //附属信息
            /*
            $affiliate_id = $wechat_user['affiliate'];
            if ($affiliate_id >= 1) {
                $affiliate = '&u=' . $affiliate_id;
            } else {
                $affiliate_id = 0;
            }
            */
        }

        return $wechat_user_uid;
    }

    private function checkMemeberExistis()
    {
        //自动注册设置
        $wechat_autoreg = $this->model_wechat_autoreg->where(array('autoreg_id' => $this->wx_store_id))->find();

        //用户名初始前缀
        $q_name = $wechat_autoreg['autoreg_name'];
        if (empty($q_name)) {
            $q_name = $wechat_setting['q_name']['value'] ? $this->wechat_setting['q_name']['value'] : 'wechat';
        }

        //密码随机长度
        $autoreg_rand = $wechat_autoreg['autoreg_rand'] ? $wechat_autoreg['autoreg_rand'] : 6;

        //初始密码
        $wechat_setting_userpwd = $this->wechat_setting['userpwd']['value'];

        //是否开启会员绑定
        $wechat_setting_bd = $this->wechat_setting['bd']['value'];

        //默认密码
        $wechat_password_tmp = random($autoreg_rand);
        $wechat_password = $wechat_setting_userpwd . $wechat_password_tmp;
        $wechat_password_md5 = md5($wechat_password);

        //PC地址
        $wechat_setting_baseurl = $this->wechat_setting['baseurl']['value'];

        //会员信息
        $member_info = $this->model_member->where(array('wechat_unionid' => $this->fromUsername))->find();
        if ($member_info) {
            $this->_member_id = $member_info['member_id'];

            /**
             * 查询是否绑定
             * 若有已绑定的网站会员则更新该微信会员信息，未绑定的直接绑定
             */
            $this->model_member->where(array('wechat_unionid' => $this->fromUsername, 'wechat_bind' => 0))->update(array('wechat_bind' => 1));
            $this->model_wechat_user->where(array('wechat_unionid' => $this->fromUsername, 'wechat_bind' => 1, 'step' => array('neq', 10)))->update(array('step' => 10));

        } else {

            if ($wechat_autoreg['state']) {
                //注册并绑定
                $member_id = $this->registerMember($wechat_password, $wechat_password_md5, true);

                //更新会员名
                $this->model_member->where(array('member_id' => $member_id))->update(array('member_name' => $q_name . $member_id));

            } else {
                $this->notice_info = '自动注册功能未开启，请手动注册';
            }
        }

        //完善会员信息
        $result = $this->updateMemberInfo($this->fromUsername, $wechat_setting_baseurl, $this->wx_store_id);
        if ($result) {
            $member_info = $this->model_member->getMemberInfoByID($member_id);
            $this->notice_info = "您的账号是：" . $member_info['member_name'] . "密码是：" . $wechat_password;
        } else {
            $this->notice_info = "网络繁忙，请稍后再试！";
        }
    }
}
