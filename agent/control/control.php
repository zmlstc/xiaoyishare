<?php
/**
 * 系统后台公共方法
 *
 * 包括系统后台父类
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class SystemControl{

    /**
     * 管理员资料 name id group
     */
    protected $admin_info;


    /**
     * 菜单
     */
    protected $menu;

    /**
     * 常用菜单
     */
    protected $quick_link;
    protected function __construct() {
        Language::read('common,layout');
        /**
         * 验证用户是否登录
         * $admin_info 管理员资料 name id
         */
        $this->admin_info = $this->systemLogin();

        //转码  防止GBK下用ajax调用时传汉字数据出现乱码
        if (($_GET['branch']!='' || $_GET['t']=='ajax') && strtoupper(CHARSET) == 'GBK'){
            $_GET = Language::getGBK($_GET);
        }
    }

    /**
     * 取得当前管理员信息
     *
     * @param
     * @return 数组类型的返回结果
     */
    protected final function getAdminInfo() {
        return $this->admin_info;
    }

    /**
     * 系统后台登录验证
     *
     * @param
     * @return array 数组类型的返回结果
     */
    protected final function systemLogin() {
        //取得cookie内容，解密，和系统匹配
        $user = unserialize(decrypt(cookie('sys_agent_key'),MD5_KEY));
        if ((empty($user['name']) || empty($user['id']))){ 
            @header('Location: '. urlAgent('login','login'));exit;
        }else {
            $this->systemSetKey($user);
        }
        return $user;
    }

    /**
     * 系统后台 会员登录后 将会员验证内容写入对应cookie中
     *
     * @param string $name 用户名
     * @param int $id 用户ID
     * @return bool 布尔类型的返回结果
     */
    protected final function systemSetKey($user) {
        setWtCookie('sys_agent_key',encrypt(serialize($user),MD5_KEY),3600,'',null);
        
    }


    /**
     * 取得后台菜单的Html形式
     *
     * @param string $permission
     * @return
     */
    protected final function getNav() {
        $_menu = $this->getMenu();
        
        $top_nav = '';
        $left_nav = '';
        $map_nav = '';
        $map_top = '';
        $quick_array = array();
        foreach ($_menu as $key => $value) {
            $top_nav .= '<li data-param="' . $key . '"><a href="javascript:void(0);">' . $value['name'] . '</a></li>';
            $left_nav .= '<div id="admincpNavTabs_'. $key .'" class="nav-tabs">';
            $map_top .= '<li><a href="javascript:void(0);" data-param="map-' . $key . '">' . $value['name'] . '</a></li>';
            $map_nav .= '<div class="admiwtp-map-div" data-param="map-' . $key . '">';
            foreach ($value['child'] as $ke => $val) {
                if (!empty($val['child'])) {
                    $left_nav .= '<dl><dt><a href="javascript:void(0);"><span class="ico-' . $key . '-' . $ke . '"></span><h3>' . $val['name'] . '</h3></a></dt>';
                    $left_nav .= '<dd class="sub-menu"><ul>';
                    $map_nav .= '<dl><dt>' . $val['name'] . '</dt>';
                    foreach ($val['child'] as $k => $v) {
                        $left_nav .= '<li><a href="javascript:void(0);" data-param="' . $key . '|' . $k . '">' . $v . '</a></li>';
                        $selected = '';
                       
                        $map_nav .= '<dd class="' . $selected . '"><a href="javascript:void(0);" data-param="' . $key . '|' . $k . '">' . $v . '</a><i class="fa fa-check-square-o"></i></dd>';
                    }
                    $left_nav .= '</ul></dd></dl>';
                    $map_nav .= '</dl>';
                }
            }
            $left_nav .= '</div>';
            $map_nav .= '</dl></div>';
        }
        $map_nav = '<ul class="admiwtp-map-nav">'.$map_top.'</ul>'.$map_nav;
        return array($top_nav, $left_nav, $map_nav, $quick_array);
    }


    /**
     * 获取菜单
     */
    protected final function getMenu() {
        if (empty($this->menu)) {
			if (file_exists(BASE_PATH.DS.'agents/include/menu.php')) {
				require(BASE_PATH.DS.'agents/include/menu.php');
			}
            $this->menu  = $_menu;
        }
        return $this->menu;
    }


    /**
     * 取得顶部小导航
     *
     * @param array $links
     * @param 当前页 $actived
     */
    protected final function sublink($links = array(), $actived = '', $file='index.php') {
		$linkstr = '';
		if(!empty($links)&&is_array($links)){
			foreach ($links as $k=>$v) {
				//parse_str($v['url'],$array);
				$array = explode('/',$v['url']);
				if(count($array)<1)continue;
				if (empty($array[1])) $array[1] = 'index';
				//if (!$this->checkPermission($array)) continue;
				$href = ($array[1] == $actived ? null : 'href="'.APP_SITE_URL.'/'.$file.'/'.$v['url'].'"');
				$class = ($array[1] == $actived ? "class=\"current\"" : null);
				$lang = $v['text'] ? $v['text'] : L($v['lang']);
				$linkstr .= sprintf('<li><a %s %s><span>%s</span></a></li>',$href,$class,$lang);
			}
			return "<ul class=\"tab-base wt-row\">{$linkstr}</ul>";
		}else{
			return '';
		}
		

    }

    /**
     * 记录系统日志
     *
     * @param $lang 日志语言包
     * @param $state 1成功0失败null不出现成功失败提示
     * @param $admin_name
     * @param $admin_id
     */
    protected final function log($lang = '', $state = 1, $admin_name = '', $admin_id = 0) {
		return;
        if (!C('sys_log') || !is_string($lang)) return;
        if ($admin_name == ''){
            $admin = unserialize(decrypt(cookie('sys_agent_key'),MD5_KEY));
            $admin_name = $admin['name'];
            $admin_id = $admin['id'];
        }
        $data = array();
        if (is_null($state)){
            $state = null;
        }else{
            $state = $state ? '' : L('wt_fail');
        }
        $data['content']    = $lang.$state;
        $data['admin_name'] = $admin_name;
        $data['createtime'] = TIMESTAMP;
        $data['admin_id']   = $admin_id;
        $data['ip']         = getIp();
        $data['url']        = $_REQUEST['w'].'&'.$_REQUEST['t'];
        return Model('admin_log')->insert($data);
    }

    /**
     * 输出JSON
     *
     * @param string $errorMessage 错误信息 为空则表示成功
     */
    protected function jsonOutput($errorMessage = false)
    {
        $data = array();

        if ($errorMessage === false) {
            $data['result'] = true;
        } else {
            $data['result'] = false;
            $data['message'] = $errorMessage;
        }

        $jsonFlag = C('debug') && version_compare(PHP_VERSION, '5.4.0') >= 0
            ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            : 0;

        echo json_encode($data, $jsonFlag);
        exit;
    }

}

class CommControl{
	
    protected function __construct() {
      
    }
}
