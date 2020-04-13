<?php
/**
 * 微信管理
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT提供技术支持
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

define('MAX_LAYER', 2);

class wechat_menuControl extends BaseSellerControl
{

    /**
     * 构造方法
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 微信接口管理
     *
     */
    public function indexWt()
    {
        $account_id = intval($_SESSION['member_id']);
        $model_wechat = Model('seller_wechat');
        $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
        $model_wxconfig = Model('wechat_config');
        $data_wxconfig = $model_wxconfig->where(array('user_id'=>$account_id))->find();
        if (empty($find_data)) {
            redirect(urlSeller('wechat', 'index'));
        }
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            if (empty($data_wxconfig)) {
                $data=array('appid'=>$_POST['appid'],'appsecret'=>$_POST['appsecret'],'user_id'=>$account_id);
                $model_wxconfig->insert($data);
                $insert_id = $model_wxconfig->getLastID();
                if ($insert_id > 0) {
                    showMessage("添加成功！");
                    return;
                } else {
                    showMessage('添加失败！');
                    return;
                }
            } else {
                $data=array('appid'=>$_POST['appid'],'appsecret'=>$_POST['appsecret']);
                $edit_id = $model_wxconfig->where(array('user_id'=>$account_id))->update($data);
                if ($edit_id) {
                    showMessage("修改成功！");
                    return;
                } else {
                    showMessage('修改失败！');
                    return;
                }
            }
        }
        
        $model_menu = Model('wechat_menu');
        $gcategories_list = array();
        $gcategories_list = $model_menu->where(array('store_id'=>$account_id))->select();
        
        if ($gcategories_list) {
            foreach ($gcategories_list as $gacte) {
                $gcategories[$gacte['cate_id']] = $gacte;
            }
            $tree = & $this->_tree($gcategories);
            
            //先根排序
            $sorted_gcategories = array();
            $cate_ids = $tree->getChilds();
            foreach ($cate_ids as $id) {
                $sorted_gcategories[] = array_merge($gcategories[$id], array('layer' => $tree->getLayer($id)));
            }
        }
        
        Tpl::output('gcategories', $sorted_gcategories);
        
        //构造映射表（每个结点的父结点对应的行，从1开始）
        $row = array(0 => 0); // cate_id对应的row
        $map = array(); // parent_id对应的row
        if ($sorted_gcategories) {
            foreach ($sorted_gcategories as $key => $gcategory) {
                $row[$gcategory['cate_id']] = $key + 1;
                $map[] = $row[$gcategory['parent_id']];
            }
        }
        Tpl::output('map', json_encode($map));
        Tpl::output('info', $data_wxconfig);
        Tpl::output('account_id', $account_id);
        Tpl::output('page_title', $page_title);
        Tpl::showpage('wechat_menu_index');
    }
    
    function addWt()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
            
            $pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
            $gcategory = array('parent_id' => $pid, 'if_show' => 1);//'sort_order' => 255,

            Tpl::setLayout('null_layout');
            Tpl::output('t', 'add');
            Tpl::output('gcategory', $gcategory);
            Tpl::output('parents', $this->_get_options());
            Tpl::showpage('wechat_menu_form');

        } else {

            $account_id = intval($_SESSION['member_id']);
            $model_wechat = Model('seller_wechat');
            $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();

            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
                'if_show'    => $_POST['if_show'],
                'type'    => $_POST['type'],
                'keyvalue'    => $_POST['keyvalue'],
                'keyword'    => $_POST['keyword'],
                'token'=>$find_data['token'],
                'store_id' => $account_id,
            );

            /* 保存 */
            $model_menu = Model('wechat_menu');
            $cate_id = $model_menu->insert($data);
            
            if (!$cate_id) {
                showMessage('新增失败！');
                return;
            }
            $this->pop_warning('ok', 'my_category_add');
        }
    }
    
    function editWt()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
            Tpl::setLayout('null_layout');
            
            /* 是否存在 */
            $model_menu = Model('wechat_menu');
            $gcategory = $model_menu->where(array('cate_id'=>$id))->find();
            if (!$gcategory) {
                echo "分类不存在！";
                return;
            }
            
            Tpl::output('t', 'edit');
            Tpl::output('id', $id);
            Tpl::output('gcategory', $gcategory);
            Tpl::output('parents', $this->_get_options($id));
            
            header("Content-Type:text/html;charset=utf-8");
            Tpl::showpage('wechat_menu_form');
        } else {
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
                'if_show' => $_POST['if_show'],
                'type' => $_POST['type'],
                'keyvalue' => $_POST['keyvalue'],
               'keyword'=> $_POST['keyword'],
            );

            /* 保存 */
            $model_menu = Model('wechat_menu');
            $edit_id = $model_menu->where(array('cate_id'=>$id))->update($data);
            if (!$edit_id) {
                $this->pop_warning("修改失败！");
                return;
            }
          
            $this->pop_warning('ok', 'my_category_edit');
        }
    }
    
    function dropWt()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id) {
            showMessage('分类不存在！');
            return;
        }

        $ids = explode(',', $id);
        $model_menu = Model('wechat_menu');
        $model_menu->where(array('cate_id' => array('in', $ids)))->delete();

        showMessage("删除成功！");
    }
    
    function creat_menuWt()
    {
        $account_id = intval($_SESSION['member_id']);
        $model_wxconfig = Model('wechat_config');
        $wechat = $model_wxconfig->where(array('user_id'=>$account_id))->find();

        if (!is_array($wechat)) {
            showMessage('请先添加微信APPID和微信AppSecret!');
            return;
        } else {
            if ($wechat['appid']==''||$wechat['appsecret']=='') {
                showMessage('微信APPID或微信AppSecret不能为空!');
                return;
            }
        }
        
        $ACCESS_LIST = $this->curl($wechat['appid'], $wechat['appsecret']);
        if ($ACCESS_LIST['access_token']!='') {
            $access_token=$ACCESS_LIST['access_token'];//获取到ACCESS_TOKEN
            $data=$this->getmenu();
            $msg=$this->curl_menu($access_token, preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data));
            if ($msg['errmsg']=='ok') {
                showMessage('创建自定义菜单成功');
                return ;
            } else {
                $wechat_error= $this->wechat_error($msg['errcode']);
                showMessage('创建自定义菜单失败!'.$wechat_error);
            }
        } else {
            showMessage('创建失败,微信AppId或微信AppSecret填写错误!');
        }
    }
    
    public function delete_menuWt()
    {
        $account_id = intval($_SESSION['member_id']);
        $model_wxconfig = Model('wechat_config');
        $wechat = $model_wxconfig->where(array('user_id'=>$account_id))->find();

        if (!is_array($wechat)) {
            showMessage('请先添加微信APPID和微信AppSecret!');
            return;
        } else {
            if ($wechat['appid']==''||$wechat['appsecret']=='') {
                showMessage('微信APPID或微信AppSecret不能为空!');
                return;
            }
        }
        
        $ACCESS_LIST=$this->curl($wechat['appid'], $wechat['appsecret']);
        if ($ACCESS_LIST['access_token']!='') {
            $access_token=$ACCESS_LIST['access_token'];//获取到ACCESS_TOKEN
            $msg=$this->curl_delete($access_token);
            if ($msg['errmsg']=='ok') {
                showMessage('删除自定义菜单成功!');
                return;
            } else {
                showMessage('删除自定义菜单失败!');
                return;
            }
        } else {
            showMessage('微信AppId或微信AppSecret填写错误!');
            return;
        }
    }
    
    public function curl_delete($ACCESS_TOKEN)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$ACCESS_TOKEN);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
        }
        curl_close($ch);
        $arr = json_decode($tmpInfo, true);
        return $arr;
    }
    
    public function curl($appid, $secret)
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
        $arr = json_decode($tmpInfo, true);
        return $arr;
    }
    
    public function getmenu()
    {
        $account_id = intval($_SESSION['member_id']);
        $keyword = array();
        $model_menu = Model('wechat_menu');
        $topmemu = $model_menu->where(array('store_id'=>$account_id,'if_show'=>1,'parent_id'=>0))->select();

        foreach ($topmemu as $key) {
            $nextmenu = $model_menu->where(array('store_id'=>$account_id,'if_show'=>1,'parent_id'=>$key['cate_id']))->select();
            if (count($nextmenu)!=0) {//没有下级栏目
                foreach ($nextmenu as $key2) {
                    if ($key2['type']==1) {
                        $kk[]=array('type'=>'view','name'=>$key2['cate_name'],'url'=>$key2['keyvalue']);
                    } else {
                           $kk[]=array('type'=>'click','name'=>$key2['cate_name'],'key'=>$key2['keyvalue']);
                    }
                }
                 $keyword['button'][]=array('name'=>$key['cate_name'],'sub_button'=>$kk);
                 $kk='';
            } else {
                if ($key['type']==1) {
                    $keyword['button'][]=array('type'=>'view','name'=>$key['cate_name'],'url'=>$key['keyvalue']);
                } else {
                    $keyword['button'][]=array('type'=>'click','name'=>$key['cate_name'],'key'=>$key['keyvalue']);
                }
            }
        }
        return  json_encode($keyword);
    }
    
    public function curl_menu($ACCESS_TOKEN, $data)
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
    
    public function wechat_error($error)
    {
        $wechat_error= array('-1'=>'系统繁忙',
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
          '47001'=>'解析JSON/XML内容错误',

        );
        return $wechat_error[$error];
    }
    
    function pop_warning($msg, $dialog_id = '', $url = '')
    {
        if ($msg == 'ok') {
            if (!empty($url)) {
                echo "<script type='text/javascript'>window.parent.location.href='".$url."';</script>";
            }
            echo "<script type='text/javascript'>window.parent.js_success('" . $dialog_id ."');</script>";
        } else {
            header("Content-Type:text/html;charset=utf-8");
            $msg = is_array($msg) ? $msg : array(array('msg' => $msg));
            $errors = '';
            foreach ($msg as $k => $v) {
                //$error = $v[obj] ? Lang::get($v[msg]) . " [" . Lang::get($v[obj]) . "]" : Lang::get($v[msg]);
                //$error = $v[obj] ? $GLOBALS['lang'][$v[msg]] .  " [" . $GLOBALS['lang'][$v[obj]] . "]" : $GLOBALS['lang'][$v[msg]];
                //$errors .= $errors ? "<br />" . $error : $error;
            }
            echo "<script type='text/javascript'>window.parent.js_fail('" . $errors . "');</script>";
        }
    }
    
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = null)
    {
        $account_id = intval($_SESSION['member_id']);
        $model_menu = Model('wechat_menu');
        $gcategories = $model_menu->where(array('store_id'=>$account_id))->select();
        $tree = & $this->_tree($gcategories);
        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        $trees = new Tree();
        $trees->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $trees;
    }
}

class Tree //extends Object
{
    var $data   = array();
    var $child  = array(-1 => array());
    var $layer  = array(0 => 0);
    var $parent = array();
    var $value_field = '';
    /**
     * 构造函数
     *
     * @param mix $value
     */
    function construct($value = 'root')
    {
        $this->Trees($value);
    }

    function Trees($value = 'root')
    {
        $this->setNode(0, -1, $value);
    }

    /**
     * 构造树
     *
     * @param array $nodes 结点数组
     * @param string $id_field
     * @param string $parent_field
     * @param string $value_field
     */
    function setTree($nodes, $id_field, $parent_field, $value_field)
    {
        $this->value_field = $value_field;
        foreach ($nodes as $node) {
            $this->setNode($node[$id_field], $node[$parent_field], $node);
        }
        $this->setLayer();
    }

    /**
     * 取得options
     *
     * @param int $layer
     * @param int $root
     * @param string $space
     * @return array (id=>value)
     */
    function getOptions($layer = 0, $root = 0, $except = null, $space = '&nbsp;&nbsp;')
    {
        $options = array();
        $childs = $this->getChilds($root, $except);
        foreach ($childs as $id) {
            if ($id > 0 && ($layer <= 0 || $this->getLayer($id) <= $layer)) {
                $options[$id] = $this->getLayer($id, $space) . htmlspecialchars($this->getValue($id));
            }
        }
        return $options;
    }

    /**
     * 设置结点
     *
     * @param mix $id
     * @param mix $parent
     * @param mix $value
     */
    function setNode($id, $parent, $value)
    {
        $parent = $parent ? $parent : 0;

        $this->data[$id] = $value;
        if (!isset($this->child[$id])) {
            $this->child[$id] = array();
        }

        if (isset($this->child[$parent])) {
            $this->child[$parent][] = $id;
        } else {
            $this->child[$parent] = array($id);
        }

        $this->parent[$id] = $parent;
    }

    /**
     * 计算layer
     */
    function setLayer($root = 0)
    {
        foreach ($this->child[$root] as $id) {
            $this->layer[$id] = $this->layer[$this->parent[$id]] + 1;
            if ($this->child[$id]) {
                $this->setLayer($id);
            }
        }
    }

    /**
     * 先根遍历，不包括root
     *
     * @param array $tree
     * @param mix $root
     * @param mix $except 除外的结点，用于编辑结点时，上级不能选择自身及子结点
     */
    function getList(&$tree, $root = 0, $except = null)
    {
        foreach ($this->child[$root] as $id) {
            if ($id == $except) {
                continue;
            }

            $tree[] = $id;

            if ($this->child[$id]) {
                $this->getList($tree, $id, $except);
            }
        }
    }

    function getValue($id)
    {
        return $this->data[$id][$this->value_field];
    }

    function getLayer($id, $space = false)
    {
        return $space ? str_repeat($space, $this->layer[$id]) : $this->layer[$id];
    }

    function getParent($id)
    {
        return $this->parent[$id];
    }

    /**
     * 取得祖先，不包括自身
     *
     * @param mix $id
     * @return array
     */
    function getParents($id)
    {
        while ($this->parent[$id] != -1) {
            $id = $parent[$this->layer[$id]] = $this->parent[$id];
        }

        ksort($parent);
        reset($parent);

        return $parent;
    }

    function getChild($id)
    {
        return $this->child[$id];
    }

    /**
     * 取得子孙，包括自身，先根遍历
     *
     * @param int $id
     * @return array
     */
    function getChilds($id = 0, $except = null)
    {
        $child = array($id);
        $this->getList($child, $id, $except);
        unset($child[0]);

        return $child;
    }

    /**
     * 先根遍历，数组格式
     * array(
     *     array('id' => '', 'value' => '', children => array(
     *         array('id' => '', 'value' => '', children => array()),
     *     ))
     * )
     */
    function getArrayList($root = 0, $layer = null)
    {
        $data = array();
        foreach ($this->child[$root] as $id) {
            if ($layer && $this->layer[$this->parent[$id]] > $layer-1) {
                continue;
            }
            $data[] = array('id' => $id, 'value' => $this->getValue($id), 'children' => $this->child[$id] ? $this->getArrayList($id, $layer) : array());
        }
        return $data;
    }

    /**
     * 取得csv格式数据
     *
     * @param int $root
     * @param mix $ext_field 辅助字段
     * @return array(
     *      array('辅助字段名','主字段名'), //如无辅助字段则无此元素
     *      array('辅助字段值','一级分类'), //如无辅助字段则无辅助字段值
     *      array('辅助字段值','一级分类'),
     *      array('辅助字段值','', '二级分类'),
     *      array('辅助字段值','', '', '三级分类'),
     * )
     */
    function getCSVData($root = 0, $ext_field = array())
    {
        $data = array();
        $main = $this->value_field; //用于显示树分级结果的字段
        $extra =array(); //辅助的字段
        if (!empty($ext_field)) {
            if (is_array($ext_field)) {
                $extra = $ext_field;
            } elseif (is_string($ext_field)) {
                $extra = array($ext_field);
            }
        }
        $childs = $this->getChilds($root);
        array_values($extra) && $data[0] = array_values($extra);
        $main && $data[0] && array_push($data[0], $main);
        foreach ($childs as $id) {
            $row = array();
            $value = $this->data[$id];
            foreach ($extra as $field) {
                $row[] = $value[$field];
            }
            for ($i = 1; $i < $this->getLayer($id); $i++) {
                $row[] = '';
            }
            if ($main) {
                $row[] = $value[$main];
            } else {
                $row[] = $value;
            }
            $data[] = $row;
        }
        return $data;

    }
}