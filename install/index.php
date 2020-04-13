<?php
/**
 *
 * ShopWT V6.5.0  instller
 *
 *
 */
 error_reporting (0);
define('ShopWT',true);

// 设置最大执行时间
set_time_limit(0);

error_reporting(E_ALL & ~E_NOTICE);
define('ROOT_PATH', dirname(dirname(__FILE__)));

define('DBCHARSET','UTF8');//编码 分为 UTF8 GBK
input($_GET);input($_POST);
if(function_exists('date_default_timezone_set')){
    date_default_timezone_set('Asia/Shanghai');
}
function input(&$data){
    foreach ((array)$data as $key => $value) {
        if (is_string($value)) {
            if (!get_magic_quotes_gpc()) {
                $value = htmlentities($value, ENT_NOQUOTES);
                $value = addslashes(trim($value));
            }
        }else{
            $data[$key] = input($value);
        }
    }
}

if (file_exists('lock') && $_GET['step'] != 5){
    @header("Content-type: text/html; charset=UTF-8");
    echo "系统已经安装过，如果重新安装，请查看安装文档。";
    exit;
}

//define variable

$html_title = 'ShopWT V6.5.0 B2B2C多用户电商系统 安装程序';

$html_header = <<<EOF
<div class="header">
  <div class="layout">
    <div class="title">
      <h2>SHOPWT B2B2C</h2>
    </div>
    <div class="version">ShopWT B2B2C多用户电商系统V6.5.0 安装向导</div>
  </div>
</div>

EOF;

$html_footer = <<<EOF
<div class="footer">
  <h6>Powered by <b><a href="http://www.shopwt.com" target="_blank">ShopWT V6.5.0</a></b> &copy;2008-2018 &nbsp;<a href="http://www.shopwt.com" target="_blank">ShopWT Inc.</a></h6>
</div>
EOF;


require('./include/function.php');
if (!in_array($_GET['step'],array(2,3,4,5))) $_GET['step'] = 1;

switch ($_GET['step']) {
    case 2:
        require('./include/var.php');
        env_check($env_items);
        dirfile_check($dirfile_items);
        function_check($func_items);
    break;
    case 3:
        $install_error = '';
        $install_recover = '';
        $demo_data =  file_exists('./data/utf8_add.sql') ? true : false;
        step3($install_error,$install_recover);
        break;
    case 4:
        step4();
        break;
    case 5:
        $sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
        $sitepath = str_replace('/install',"",$sitepath);
        $auto_site_url = strtolower('http://'.$_SERVER['HTTP_HOST'].$sitepath);
        break;
    default:
    break;
}

function step3(&$install_error,&$install_recover){
    global $html_title,$html_header,$html_footer;
    if ($_POST['submitform'] != 'submit') return;
    $db_host = $_POST['db_host'];
    $db_port = $_POST['db_port'];
    $db_user = $_POST['db_user'];
    $db_pwd = $_POST['db_pwd'];
    $db_name = $_POST['db_name'];
    $db_prefix = $_POST['db_prefix'];
    $admin = $_POST['admin'];
    $password = $_POST['password'];
    if (!$db_host || !$db_port || !$db_user || !$db_pwd || !$db_name || !$db_prefix || !$admin || !$password){
        $install_error = '输入不完整，请检查';
    }
    if(strpos($db_prefix, '.') !== false) {
        $install_error .= '数据表前缀为空，或者格式错误，请检查';
    }

    if(strlen($admin) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^游客|^Guest/is", $admin)) {
        $install_error .= '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字';
    }
    if ($install_error != '') reutrn;
        $mysqli = @ new mysqli($db_host, $db_user, $db_pwd, '', $db_port);
        if($mysqli->connect_error) {
            $install_error = '数据库连接失败';return;
        }

    if($mysqli->get_server_info()> '5.0') {
        $mysqli->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET ".DBCHARSET);
    } else {
        $install_error = '数据库必须为MySQL5.0版本以上';return;
    }
    if($mysqli->error) {
        $install_error = $mysqli->error;return ;
    }
    if($_POST['install_recover'] != 'yes' && ($query = $mysqli->query("SHOW TABLES FROM $db_name"))) {
        while($row = mysqli_fetch_array($query)) {
            if(preg_match("/^$db_prefix/", $row[0])) {
                $install_error = '数据表已存在，继续安装将会覆盖已有数据';
                $install_recover = 'yes';
                return;
            }
        }
    }

    require ('step_4.php');
    $sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
    $sitepath = str_replace('/install',"",$sitepath);
    $auto_site_url = strtolower('http://'.$_SERVER['HTTP_HOST'].$sitepath);

    write_config($auto_site_url);

    $_charset = strtolower(DBCHARSET);
    $mysqli->select_db($db_name);
    $mysqli->set_charset($_charset);

    $sql = file_get_contents("data/{$_charset}.sql");
    if (file_exists('data/'.$_charset.'_news.sql')) {
        $sql .= "\r\n".file_get_contents("data/{$_charset}_news.sql");
    }
    if (file_exists('data/'.$_charset.'_bbs.sql')) {
        $sql .= "\r\n".file_get_contents("data/{$_charset}_bbs.sql");
    }
    if (file_exists('data/'.$_charset.'_what.sql')) {
        $sql .= "\r\n".file_get_contents("data/{$_charset}_what.sql");
    }

    if ($_POST['demo_data'] == '1'){
        $sql .= file_get_contents("data/{$_charset}_add.sql");
    }
    $sql = str_replace("\r\n", "\n", $sql);
    runquery($sql,$db_prefix,$mysqli);
    showjsmessage('初始化数据 ... 成功 ');

    /**
     * 转码
     */
    $sitename = $_POST['site_name'];
    $username = $_POST['admin'];
    $password = $_POST['password'];
    $store_name = $_POST['store_name'];
    $member_name = $_POST['member_name'];
    $seller_name = $_POST['seller_name'];
    $member_password = $_POST['member_password'];
    /**
     * 产生随机的md5_key，来替换系统默认的md5_key值
     */
    $md5_key = md5(random(4).substr(md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT'].$db_host.$db_user.$db_pwd.$db_name.substr(time(), 0, 6)), 8, 6).random(10));
    $mysqli->query("UPDATE {$db_prefix}setting SET value='".$sitename."' WHERE name='site_name'");
    $mysqli->query("UPDATE {$db_prefix}setting SET value='".$md5_key."' WHERE name='md5_key'");
    //管理员账号密码
    $mysqli->query("INSERT INTO {$db_prefix}admin (`admin_id`,`admin_name`,`admin_password`,`admin_login_time`,`admin_login_num`,`admin_is_super`) VALUES ('1','$username','". md5($password) ."', '".time()."' ,'0',1);");

    // 创建店铺
    $mysqli->query("INSERT INTO {$db_prefix}member (`member_id`,`member_name`,`member_passwd`,`member_email`,`member_time`,`member_login_time`,`member_old_login_time`) VALUES ('1', '{$member_name}','". md5($member_password) ."', '', '". time() ."', '". time() ."', '". time() ."')");
    $mysqli->query("INSERT INTO {$db_prefix}member_common (`member_id`) VALUES ('1')");
    $mysqli->query("INSERT INTO {$db_prefix}store (`store_id`,`store_name`,`grade_id`,`member_id`,`member_name`,`seller_name`,`store_state`,`store_time`,`is_own_shop`,`bind_all_gc`) VALUES ('1','{$store_name}','0','1','{$member_name}','{$seller_name}','1', '". time() ."', '1', '1')");
    $mysqli->query("INSERT INTO {$db_prefix}store_extend (`store_id`, `express`, `pricerange`, `orderpricerange`, `bill_cycle`) VALUES ('1', NULL, NULL, NULL, 0)");
    $mysqli->query("INSERT INTO {$db_prefix}seller (`seller_id`,`seller_name`,`member_id`,`seller_group_id`,`store_id`,`is_admin`) VALUES ('1', '{$seller_name}', '1', '0', '1', '1')");
    $mysqli->query("INSERT INTO {$db_prefix}album_class (`aclass_name`, `store_id`, `aclass_des`, `aclass_sort`, `aclass_cover`, `upload_time`, `is_default`) VALUES('默认相册', 1, '', 255, '', 1387942806, 1)");
    $mysqli->query("INSERT INTO {$db_prefix}video_album_class (`video_class_name`, `store_id`, `video_class_des`, `video_class_sort`, `upload_time`, `is_default`) VALUES('默认媒体库', 1, '', 255, 1387942806, 1)");

    $mysqli->query("UPDATE `{$db_prefix}navigation` SET nav_url=CONCAT('{$auto_site_url}/',nav_url)");
    $mysqli->query("UPDATE `{$db_prefix}news_navigation` SET navigation_link=CONCAT('{$auto_site_url}/',navigation_link)");
    if ($_POST['demo_data'] == '1'){
        $mysqli->query("UPDATE `{$db_prefix}goods` SET `store_name` = '{$store_name}'");
        $mysqli->query("UPDATE `{$db_prefix}goods_common` SET `store_name` = '{$store_name}'");
        $mysqli->query("UPDATE `{$db_prefix}robbuy` SET `store_name` = '{$store_name}'");
        $mysqli->query("UPDATE `{$db_prefix}robbuy_quota` SET `member_name` = '{$member_name}' ,`store_name` = '{$store_name}';");
        $mysqli->query("UPDATE `{$db_prefix}p_xianshi` SET `member_name` = '{$member_name}' ,`store_name` = '{$store_name}';");
        $mysqli->query("UPDATE `{$db_prefix}p_xianshi_quota` SET `member_name` = '{$member_name}' ,`store_name` = '{$store_name}';");
    }
    //新增一个标识文件，用来屏蔽重新安装
    $fp = @fopen('lock','wb+');
    @fclose($fp);
    exit("<script type=\"text/javascript\">document.getElementById('install_process').innerHTML = '安装完成，下一步...';document.getElementById('install_process').href='index.php?step=5&sitename={$sitename}&username={$username}&password={$password}';</script>");
    exit();
}
include ("step_{$_GET['step']}.php");

//execute sql
function runquery($sql, $db_prefix, $mysqli) {
//  global $lang, $tablepre, $db;

    if(!isset($sql) || empty($sql)) return;

    $sql = str_replace("\r", "\n", str_replace('#__', $db_prefix, $sql));
    $ret = array();
    $num = 0;
    foreach(explode(";\n", trim($sql)) as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        foreach($queries as $query) {
            $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
        }
        $num++;
    }
    unset($sql);
    foreach($ret as $query) {
        $query = trim($query);
        if($query) {
            if(substr($query, 0, 12) == 'CREATE TABLE') {
                $line = explode('`',$query);
                $data_name = $line[1];
                showjsmessage('数据表  '.$data_name.' ... 创建成功');
                $mysqli->query(droptable($data_name));
                $mysqli->query($query);
                unset($line,$data_name);
            } else {
                $mysqli->query($query);
            }
        }
    }
}
//抛出JS信息
function showjsmessage($message) {
    echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
    flush();
    ob_flush();
}



//写入config文件
function write_config($url) {
    extract($GLOBALS, EXTR_SKIP);
    $config = 'data/config.php';

    $configfile = @file_get_contents($config);
    $configfile = trim($configfile);
    $configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;

    $charset = 'UTF-8';

    $db_host = $_POST['db_host'];
    $db_port = $_POST['db_port'];
    $db_user = $_POST['db_user'];
    $db_pwd = $_POST['db_pwd'];
    $db_name = $_POST['db_name'];
    $db_prefix = $_POST['db_prefix'];
    $admin = $_POST['admin'];
    $password = $_POST['password'];
    $db_type = 'mysql';

    $cookie_pre = strtoupper(substr(md5(random(6).substr($_SERVER['HTTP_USER_AGENT'].md5($_SERVER['SERVER_ADDR'].$db_host.$db_user.$db_pwd.$db_name.substr(time(), 0, 6)), 8, 6).random(5)),0,4)).'_';
    $configfile = str_replace("===url===",          $url, $configfile);
    $configfile = str_replace("===node_ip===",      $_SERVER["SERVER_ADDR"], $configfile);
    $configfile = str_replace("===db_prefix===",    $db_prefix, $configfile);
    $configfile = str_replace("===db_driver===",    $db_type, $configfile);
    $configfile = str_replace("===db_charsec===",   $charset, $configfile);
    $configfile = str_replace("===db_hosc===",      $db_host, $configfile);
    $configfile = str_replace("===db_user===",      $db_user, $configfile);
    $configfile = str_replace("===db_pwd===",       $db_pwd, $configfile);
    $configfile = str_replace("===db_name===",      $db_name, $configfile);
    $configfile = str_replace("===db_porc===",      $db_port, $configfile);
    $configfile = str_replace("===setup_date===",   date("Y-m-d H:i:s",time()), $configfile);
    $configfile = str_replace("===cookie_pre===",   $cookie_pre, $configfile);
    @file_put_contents('../system/config/config.php', $configfile);
}
