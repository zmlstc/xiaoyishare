<?php defined('ShopWT') or exit('Access Denied By ShopWT'); return array (
  'system' => 
  array (
    'name' => '系统',
    'child' => 
    array (
      0 => 
      array (
        'name' => '设置',
        'child' => 
        array (
          'setting' => '平台设置',
          'upload' => '上传设置',
          'show' => '广告管理',
          'db' => '数据库管理',
          'area' => '地区管理',
          'task' => '计划任务',
          'cache' => '更新缓存',
        ),
      ),
      1 => 
      array (
        'name' => '权限',
        'child' => 
        array (
          'admin' => '权限设置',
          'admin_log' => '操作日志',
        ),
      ),
      2 => 
      array (
        'name' => '接口',
        'child' => 
        array (
          'message' => '邮件设置',
          'sms' => '短信设置',
          'account' => '第三方登录',
        ),
      ),
      3 => 
      array (
        'name' => '文章',
        'child' => 
        array (
          'article' => '文章管理',
          'article_class' => '文章分类',
          'document' => '商城协议',
        ),
      ),
    ),
  ),
  'shop' => 
  array (
    'name' => '商城',
    'child' => 
    array (
      0 => 
      array (
        'name' => '设置',
        'child' => 
        array (
          'upload' => '上传设置',
          'payment' => '支付方式',
          'message' => '消息模板',
          'inviteset' => '推广佣金设置',
        ),
      ),
      1 => 
      array (
        'name' => '运营',
        'child' => 
        array (
          'rechargecard' => '充值卡管理',
          'mall_consult' => '客服咨询管理',
        ),
      ),
      2 => 
      array (
        'name' => '店铺',
        'child' => 
        array (
          'store' => '店铺列表',
          'store_class' => '店铺分类',
          'facility' => '设施管理',
          'help_store' => '店铺帮助',
          'store_predeposit' => '提现管理',
        ),
      ),
      3 => 
      array (
        'name' => '交易',
        'child' => 
        array (
          'order' => '商品订单管理',
          'evaluate' => '商品评价管理',
        ),
      ),
      4 => 
      array (
        'name' => '统计',
        'child' => 
        array (
          'stat_general' => '概述统计',
          'stat_trade' => '销量统计',
          'stat_store' => '店铺统计',
          'stat_industry' => '行业分析',
          'stat_member' => '会员统计',
        ),
      ),
      5 => 
      array (
        'name' => '促销',
        'child' => 
        array (
          'operation' => '促销设置',
          'activity' => '平台活动',
          'coupon' => '平台优惠券',
          'voucher' => '店铺代金券',
          'pointprod' => '积分兑换',
        ),
      ),
      6 => 
      array (
        'name' => '活动',
        'child' => 
        array (
          'plat_act' => '平台活动',
          'plat_actclass' => '活动分类',
        ),
      ),
      7 => 
      array (
        'name' => '会员',
        'child' => 
        array (
          'member' => '会员管理',
          'sns_malbum' => '相册管理',
          'points' => '积分管理',
          'chat_log' => 'IM消息记录',
          'predeposit' => '预存款管理',
        ),
      ),
      8 => 
      array (
        'name' => '代理',
        'child' => 
        array (
          'agencies' => '代理管理',
        ),
      ),
    ),
  ),
  'news' => 
  array (
    'name' => '资讯',
    'child' => 
    array (
      0 => 
      array (
        'name' => '设置',
        'child' => 
        array (
          'news_manage' => '资讯设置',
          'news_index' => '首页管理',
          'news_navigation' => '导航管理',
          'news_tag' => '标签管理',
          'news_comment' => '评论管理',
        ),
      ),
      1 => 
      array (
        'name' => '专题',
        'child' => 
        array (
          'news_special' => '专题管理',
        ),
      ),
      2 => 
      array (
        'name' => '文章',
        'child' => 
        array (
          'news_article_class' => '文章分类',
          'news_article' => '文章管理',
        ),
      ),
      3 => 
      array (
        'name' => '图刊',
        'child' => 
        array (
          'news_picture_class' => '图刊分类',
          'news_picture' => '图刊管理',
        ),
      ),
    ),
  ),
  'mobile' => 
  array (
    'name' => '手机端',
    'child' => 
    array (
      0 => 
      array (
        'name' => '设置',
        'child' => 
        array (
          'mb_setting' => '手机端设置',
          'mb_feedback' => '用户反馈',
          'mb_payment' => '手机支付',
          'mb_app' => 'APP二维码',
          'mb_wx' => '微信二维码',
          'mb_connect' => '第三方登录',
        ),
      ),
    ),
  ),
  'wechat' => 
  array (
    'name' => '微信',
    'child' => 
    array (
      0 => 
      array (
        'name' => '设置',
        'child' => 
        array (
          'setting' => '基本设置',
          'api' => '接口配置',
          'wechat_msg' => '微信通知',
          'material' => '素材管理',
          'subcribe' => '首次关注设置',
          'menu' => '自定义菜单',
          'keyword' => '关键词管理',
          'url' => 'URL管理',
        ),
      ),
    ),
  ),
);