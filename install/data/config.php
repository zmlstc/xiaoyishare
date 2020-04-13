<?php
//by shopwt.com ShopWT
$config = array(
		'base_site_url' 	   => '===url===',
		'fenxiao_site_url'     => '===url===/fenxiao',
		'news_site_url'        => '===url===/news',
		'what_site_url'        => '===url===/what',
		'bbs_site_url'         => '===url===/bbs',
		'admin_site_url'       => '===url===/wt',
		'wap_site_url'         => '===url===/mobile',
		'mobile_site_url'      => '===url===/api/mobile',
		'wxmini_site_url'      => '===url===/wxmini',
		'chat_site_url'        => '===url===/api/im',
		'node_site_url' 	   => 'http://127.0.0.1:33', //如果要启用IM，把127.0.0.1修改为：您的服务器IP
		'delivery_site_url'    => '===url===/since',
		'chain_site_url'       => '===url===/store',
		'member_site_url'      => '===url===/home',
		'upload_site_url'      => '===url===/system/upfiles',
		'static_site_url'      => '===url===/system/static',
		'version'              => '20180405001',
		'setup_date'           => '===setup_date===',
		'gip'                  => 0,
		'dbdriver'             => 'mysqli',
		'tablepre'             => '===db_prefix===',
		'db'				   => array(
									'1' => array(
										'dbhost'       => '===db_hosc===',
										'dbport'       => '===db_porc===',
										'dbuser'       => '===db_user===',
										'dbpwd'        => '===db_pwd===',
										'dbname'       => '===db_name===',
										'dbcharset'    => '===db_charsec==='
									),
									'slave'            => ''
									),

		'session_expire'       => 3600,
		'lang_type'            => 'zh_cn',
		'cookie_pre'           => '===cookie_pre===',
		'cache_open'           => false,
		'debug'                => false,
		'url_model'            => false, //如果要启用伪静态，把false修改为true
		'subdomain_suffix'     => '',//如果要启用店铺二级域名，请填写不带www的域名，比如shopwt.com
		'node_chat'            => false,//如果要启用IM，把false修改为true
		'flowstat_tablenum'    => 3,//流量统计表，默认3，不要随意修改，会造成统计数据错误
		'queue'       		   => array(
									'open'      => false,
									'host'      => '127.0.0.1',
									'port'      => 6379
								),
		'https' 			   => false,
		'sys_log'       	   => true
);
return $config;

