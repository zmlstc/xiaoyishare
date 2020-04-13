<?php
//by shopwt.com ShopWT
$config = array(
		'base_site_url' 	   => 'https://www.fhlego.com',
		'seller_site_url' 	   => 'https://www.fhlego.com/seller',
		'fenxiao_site_url'     => 'https://www.fhlego.com/fenxiao',
		'news_site_url'        => 'https://www.fhlego.com/news',
		'what_site_url'        => 'https://www.fhlego.com/what',
		'bbs_site_url'         => 'https://www.fhlego.com/bbs',
		'admin_site_url'       => 'https://www.fhlego.com/xy',
		'agents_site_url'      => 'https://www.fhlego.com/agent',
		'wap_site_url'         => 'https://www.fhlego.com/mobile',
		'mobile_site_url'      => 'https://www.fhlego.com/api/mobile',
		'wxmini_site_url'      => 'https://www.fhlego.com/wxmini',
		'chat_site_url'        => 'https://www.fhlego.com/api/im',
		'node_site_url' 	   => 'http://127.0.0.1:33', //如果要启用IM，把127.0.0.1修改为：您的服务器IP
		'delivery_site_url'    => 'https://www.fhlego.com/since',
		'chain_site_url'       => 'https://www.fhlego.com/store',
		'member_site_url'      => 'https://www.fhlego.com/home',
		'upload_site_url'      => 'https://www.fhlego.com/system/upfiles',
		'static_site_url'      => 'https://www.fhlego.com/system/static',
		'version'              => '20180405001',
		'setup_date'           => '2018-04-05 12:36:24',
		'gip'                  => 0,
		'dbdriver'             => 'mysqli',
		'tablepre'            => 'shopwt_',
		'db'				   => array(
										'1' => array(
											'dbhost'       => 'localhost',
											'dbport'       => '3306',
											'dbuser'       => 'root',
											'dbpwd'        => 'rmdkfguq',
											'dbname'       => 'xiaoyi',
											'dbcharset'    => 'UTF-8'
										),
										'slave'            => ''
									),

		'session_expire'       => 3600,
		'lang_type'            => 'zh_cn',
		'cookie_pre'           => 'B87D_',
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
		'sys_log'       	   => true,
		'oss'				   => array(
									'open'       => false,
									'img_url'    => '',
									'api_url'    => '',
									'bucket'     => '',
									'access_id'  => '',
									'access_key' => ''
									),
);

return $config;