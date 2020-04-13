<?php
/**
 * 微信关注自动回复管理
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT提供技术支持
 */
defined('ShopWT') or exit('Access Denied By ShopWT');
class wechat_followControl extends BaseSellerControl {

    /**
     * 构造方法
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 微信接口管理
     *
     */
    public function indexWt() {
        $account_id = intval($_SESSION['member_id']);
        $model_wechat = Model('seller_wechat');
        $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
        if (chksubmit()){
            if(empty($find_data)) {
                if (preg_match("/([\x81-\xfe][\x40-\xfe])/",trim($_POST['token']), $match)) {//存在中文
                    showMessage('TOKEN值不能存在中文或特殊符号！', '', '', 'error');
                    return ;
                }
                if(preg_match("/^\\d+$/",trim($_POST['token'])))//纯数字
                {
                    showMessage('TOKEN值不能使用纯数字！', '', '', 'error');
                    return ;
                }
                if(strlen(trim($_POST['token']))<3||strlen(trim($_POST['token']))>32)
                {
                    showMessage('TOKEN值长度在3-32个字符之间！', '', '', 'error');
                    return ;
                }
                if(!$this->unique($_POST['token'],$account_id))
                {
                    showMessage('TOKEN值已经存在，请换一个！', '', '', 'error');
                    return;
                }
                $data=array('wxname'=>$_POST['wxname'],'weixin'=>$_POST['weixin'],'token'=>$_POST['token'],'user_id'=>$account_id,'wx_type'=>$_POST['wx_type']);
                $model_wechat->insert($data);
                $deal_id = $model_wechat->getLastID();
                if($deal_id > 0) {
                    showMessage("添加成功！");
                    return ;
                } else {
                    showMessage('添加失败！', '', '', 'error');
                    return;
                }
            } else {
                if(!$this->unique($_POST['token'],$account_id)) {
                    showMessage('TOKEN值已经存在，请换一个！', '', '', 'error');
                    return;
                }
                $data=array('wxname'=>$_POST['wxname'],'weixin'=>$_POST['weixin'],'token'=>$_POST['token'],'wx_type'=>$_POST['wx_type']);
                $result = $model_wechat->where(array('user_id'=>$account_id))->update($data);
                if(result) {
                    showMessage('修改成功！');return ;
                } else {
                    showMessage('修改失败！', '', '', 'error');
                    return;
                }
            }
        }

        Tpl::output('account_id',$account_id);
        Tpl::output('wechat', $find_data);
        Tpl::showpage('wechat_index');
    }

     /**
     * 关注自动回复
     *
     */
    public function follow_indexWt()
    {
        $account_id = intval($_SESSION['member_id']);
        $model_wechat = Model('seller_wechat');
        $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
        if(empty($find_data))
        {
            redirect(urlSeller('wechat'));
        }
        Tpl::showpage('wechat_follow_index');
    }

    /*
    * 判断名称是否唯一
    */
    private function unique($token, $user_id = 0)
    {
        $conditions = "token = '" . $token . "'";
        $user_id && $conditions .= " AND user_id <> '" . $user_id . "'";
        $model_wechat = Model('seller_wechat');
        return $model_wechat->where($conditions)->count() == 0;
    }

    /**
     * 显示自动回复
     *
     */
     public function showfollowWt()
     {
        $account_id = intval($_SESSION['member_id']);
        $model_keyword = Model('keyword');
        $keyinfo = $model_keyword->where(array('user_id'=>$account_id, 'isfollow'=>1))->find();
        if(!empty($keyinfo)){
            if($keyinfo['type']==2)
            {
                $keyinfo['titles2']=unserialize($keyinfo['titles']);
                $keyinfo['imageinfo2']=unserialize($keyinfo['imageinfo']);
                $keyinfo['linkinfo2']=unserialize($keyinfo['linkinfo']);
            }
            echo json_encode($keyinfo);
        }else{
            echo json_encode($keyinfo);
        }
     }

     /**
     * *
     * 添加，修改 关注自动回复
     *
     */
    public function addfollowWt()
    {
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $account_id = intval($_SESSION['member_id']);
            $model_wechat = Model('seller_wechat');
            //$find_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."wechat WHERE user_id = ".$account_id);
            $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
            $model_keyword = Model('keyword');
            //$keyinfo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."keyword WHERE user_id = ".$account_id." AND isfollow=1 AND token ='".$find_data['token']."'");
            $keyinfo = $model_keyword->where(array('user_id'=>$account_id, 'isfollow'=>1, 'token'=>$find_data['token']))->select();
            $keyword = array();
            $keyword['type']= intval($_POST['ketype']);
            $linkinfo=explode(",",substr($_POST['linkinfo'], 0, -1));
            $titles=explode(",",substr($_POST['titles'], 0, -1));
            $imageinfo=explode(",",substr($_POST['imageinfo'], 0, -1));
            if(empty($keyinfo)){
                if($keyword['type']==1){
                    $keyword['kecontent'] = trim($_POST['kecontent']);
                }else{
                    $keyword['linkinfo']  = serialize($linkinfo);
                    $keyword['titles']    = serialize($titles);
                    $keyword['imageinfo'] = serialize($imageinfo);
                }
                $keyword['isfollow'] = 1;
                $keyword['user_id'] = $account_id;  
                $keyword['token'] = $find_data['token'];
                //$GLOBALS['db']->autoExecute(DB_PREFIX."keyword",$keyword);
                $keyinfo = $model_keyword->insert($keyword);
                //$insert_id = intval($GLOBALS['db']->insert_id());
                $insert_id = $model_keyword->getLastID();
                //if($model_keyword->add($keyword))
                if($insert_id > 0)
                {
                    echo "1";
                    //exit("1");
                }else 
                {
                    echo "0";
                    //exit("0");
                }
            }else{
                if($keyword['type']==1){
                    $keyword['linkinfo']  = NULL;
                    $keyword['titles']    = NULL;
                    $keyword['imageinfo'] = NULL;
                    $keyword['kecontent'] = trim($_POST['kecontent']);
                }else{
                    $keyword['kecontent'] = NULL;
                    $keyword['linkinfo']  = serialize($linkinfo);
                    $keyword['titles']    = serialize($titles);
                    $keyword['imageinfo'] = serialize($imageinfo);
                }
                $keyword['user_id'] = $account_id;
                $keyword['token'] = $find_data['token'];
                //$where = array('isfollow' => 1,'user_id'=>$userid);
                
                //$GLOBALS['db']->autoExecute(DB_PREFIX."keyword",$keyword,"UPDATE","user_id=".$account_id." AND isfollow = 1");
                $model_keyword->where(array('user_id'=>$account_id, 'isfollow'=>1))->update($keyword);
                $edit_id = $keyinfo[0]['kid'];
                if($edit_id > 0)
                //if($model_keyword->edit("user_id=".$userid." AND isfollow =1",$keyword)!==false)
                {
                    echo "1";
                    //exit("1");
                }else
                {
                    echo "0";
                    //exit("0");
                }   
            }
        }
        //exit("0");
    }
    
    /**
     * 关键词自动回复
     *
    */
    function keyword_indexWt()
    {
        $account_id = intval($_SESSION['member_id']);
        $model_wechat = Model('seller_wechat');
        //$s_account_info = es_session::get("account_info");
        //$account_id = intval($s_account_info['id']);
        $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
        //$find_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."wechat WHERE user_id = ".$account_id);
        if(empty($find_data))
        {
            @header('Location: index.php/seller_wechat/index');
            return;
            //$this->show_warning('请先设置微信接口！','马上去设置','index.php/wechat');return ;
        }
        
        /*$userid=$this->visitor->get('user_id');
        $model_wechat=& m('wechat');
        $find_data = $model_wechat->get("user_id =".$userid);
        $model_keyword=& m('keyword');
        if(empty($find_data))
        {
        $this->show_warning('请先设置微信接口！','马上去设置','index.php/wechat');return ;
        }*/
        
            $model_keyword = Model('keyword');
            $t = $_GET['opt'];
            if(!empty($t)){
                $keyword = array();
                $keyword['kename']    = trim($_POST['kename']);
                $keyword['kyword']    = trim($_POST['keword']);
                $keyword['type']      = intval($_POST['ketype']);
            
                $linkinfo=explode(",",substr($_POST['linkinfo'], 0, -1));
                $titles=explode(",",substr($_POST['titles'], 0, -1));
                $imageinfo=explode(",",substr($_POST['imageinfo'], 0, -1));
                
                if($t == 'add') {
                    if($keyword['type']==1){
                        $keyword['kecontent'] = trim($_POST['kecontent']);
                        $keyword['linkinfo']  = '';
                        $keyword['titles']    = '';
                        $keyword['imageinfo'] = '';
                    }else{
                        $keyword['kecontent'] ='';
                        $keyword['linkinfo']  = serialize($linkinfo);
                        $keyword['titles']    = serialize($titles);
                        $keyword['imageinfo'] = serialize($imageinfo);
                    }
                     $keyword['iskey'] = 1;
                     $keyword['user_id'] = $account_id; 
                     $keyword['token'] = $find_data['token'];
                     $model_keyword->insert($keyword);
                     $insert_id = $model_keyword->getLastID();
                     //$GLOBALS['db']->autoExecute(DB_PREFIX."keyword",$keyword);
                     //$insert_id = intval($GLOBALS['db']->insert_id());
                    if($insert_id > 0)
                    {
                        echo "1";
                    }else 
                    {
                        echo "0";
                    }
                }

                if($t=='update'){
                    $kid = trim($_POST['kid']);
                    if($keyword['type']==1){
                        $keyword['kecontent'] = trim($_POST['kecontent']);
                    }else{
                        $keyword['linkinfo']  = serialize($linkinfo);
                        $keyword['titles']    = serialize($titles);
                        $keyword['imageinfo'] = serialize($imageinfo);
                    }
                    
                    $edit_id = $model_keyword->where(array('user_id'=>$account_id, 'iskey'=>1,'kid'=>$kid))->update($keyword);
                    //$edit_id = $GLOBALS['db']->autoExecute(DB_PREFIX."keyword",$keyword,"UPDATE","kid=".$kid." AND iskey =1 AND user_id=".$account_id);
                    //if($model_keyword->edit("kid=".$kid." AND iskey =1 AND user_id=".$userid,$keyword))
                    if($edit_id > 0)
                    {
                        echo "1";
                    }else 
                    {
                        echo "0";
                    }
                }
                
                if($t=='del'){
                    $kid = trim($_POST['kid']);
                    //if($model_keyword->drop("kid=".$kid." AND user_id=".$userid." AND iskey=1",$kid))
                    if($model_keyword->where(array('kid' =>$kid,'user_id'=>$account_id,'iskey'=>1))->delete())
                    //if($GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."keyword WHERE kid=".$kid." AND user_id=".$account_id." AND iskey=1"))
                    {
                        echo "1";
                    }
                    else 
                    {
                        echo "0";
                    }
                }
            }
            else
            {   
                $model_keyword = Model('keyword');
                $keyinfo = $model_keyword->where(array('user_id'=>$account_id, 'iskey'=>1))->select();
                //$keyinfo = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."keyword WHERE user_id = ".$account_id." AND iskey=1 ORDER BY kid desc");
                
                Tpl::output('keyinfo',$keyinfo);
                Tpl::output('page_title', '关键词自动回复');
                Tpl::showpage('wechat_keyword_index');
            }
    }
    
    /**
     * 消息自动回复
     *
     */
    function message_indexWt()
    {
        $account_id = intval($_SESSION['member_id']);
        $model_wechat = Model('seller_wechat');
        $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
        //$find_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."wechat WHERE user_id = ".$account_id);
        //$userid=$this->visitor->get('user_id');
        //$model_wechat=& m('wechat');
        //$find_data = $model_wechat->get("user_id =".$userid);
        if(empty($find_data))
        {
            @header('Location: index.php/seller_wechat/index');
            return;
            //app_redirect(url("biz","wechat"));return;     
            //$this->show_warning('请先设置微信接口！','马上去设置','index.php/wechat');return ;
        }
        
        Tpl::output('keyinfo',$keyinfo);
        Tpl::output('page_title', '消息自动回复');
        Tpl::showpage('wechat_message_index');
    }
    
      /**
     * *
     * 添加 修改消息自动回复
     * 
     */
    function addmessWt()
    {
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        {
            $account_id = intval($_SESSION['member_id']);
            //$s_account_info = es_session::get("account_info");
            //$account_id = intval($s_account_info['id']);
            $model_wechat = Model('seller_wechat');
            $find_data = $model_wechat->where(array('user_id'=>$account_id))->find();
            //$find_data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."wechat WHERE user_id = ".$account_id);
            
            //$userid=$this->visitor->get('user_id');
            //$model_wechat=& m('wechat');
            //$find_data = $model_wechat->get("user_id =".$userid);
            //$model_keyword=& m('keyword');
            $model_keyword = Model('keyword');
            $keyinfo = $model_keyword->where(array('user_id'=>$account_id, 'ismess'=>1,'token'=>$find_data['token']))->find();
            //$keyinfo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."keyword WHERE user_id =".$account_id." AND ismess=1 AND token ='".$find_data['token']."'");
            //$keyinfo = $model_keyword->get("user_id =".$userid." AND ismess=1 AND token ='".$find_data['token']."'");
            $keyword = array();
            $keyword['type']= intval($_POST['ketype']);
            $linkinfo=explode(",",substr($_POST['linkinfo'], 0, -1));
            $titles=explode(",",substr($_POST['titles'], 0, -1));
            $imageinfo=explode(",",substr($_POST['imageinfo'], 0, -1));
            //var_dump(serialize($linkinfo));
            //exit;
            if(empty($keyinfo)){
                if($keyword['type']==1){
                    $keyword['kecontent'] = trim($_POST['kecontent']);
                }else{
                    $keyword['linkinfo']  = serialize($linkinfo);
                    $keyword['titles']    = serialize($titles);
                    $keyword['imageinfo'] = serialize($imageinfo);
                }
                    $keyword['ismess'] = 1;
                    $keyword['user_id'] = $account_id;  
                    $keyword['token'] = $find_data['token'];
                    $model_keyword->insert($keyword);
                    $insert_id = $model_keyword->getLastID();
                    if($insert_id > 0)
                    {
                        echo "1";
                    }else 
                    {
                        echo "0";
                    }
            }else{
                if($keyword['type']==1){
                    $keyword['linkinfo']  = NULL;
                    $keyword['titles']    = NULL;
                    $keyword['imageinfo'] = NULL;
                    $keyword['kecontent'] = trim($_POST['kecontent']);
                }else{
                    $keyword['kecontent'] = NULL;
                    $keyword['linkinfo']  = serialize($linkinfo);
                    $keyword['titles']    = serialize($titles);
                    $keyword['imageinfo'] = serialize($imageinfo);
                }
                $keyword['user_id'] = $account_id;
                $keyword['token'] = $find_data['token'];
                
                if($model_keyword->where(array('user_id'=>$account_id, 'ismess'=>1))->update($keyword))
                //if($GLOBALS['db']->autoExecute(DB_PREFIX."keyword",$keyword,"UPDATE","user_id=".$account_id." AND ismess =1"))
                //if($model_keyword->edit("user_id=".$userid." AND ismess =1",$keyword)!==false)
                {
                    echo "1";
                }else
                {
                    echo "0";
                }
                
            }
        }
    }
    
    /**
     * 显示自动回复
     *
     */
     function showmessWt()
     {
        $account_id = intval($_SESSION['member_id']);
        $model_keyword = Model('keyword');
        $keyinfo = $model_keyword->where(array('user_id'=>$account_id, 'ismess'=>1))->find();
        //$keyinfo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."keyword WHERE user_id =".$account_id." AND ismess=1");
        if(!empty($keyinfo)){
            if($keyinfo['type']==2)
            {
                $keyinfo['titles2']=unserialize($keyinfo['titles']);
                $keyinfo['imageinfo2']=unserialize($keyinfo['imageinfo']);
                $keyinfo['linkinfo2']=unserialize($keyinfo['linkinfo']);
            } 
            echo json_encode($keyinfo);
        }else{
            echo json_encode($keyinfo);
        }
     }
     
    /**
     * *
     *关键词 AJAX
     */
    function addkeyword_ajaxWt()
    {
        $kid = $_GET['kid'];
        $account_id = intval($_SESSION['member_id']);
        //$s_account_info = es_session::get("account_info");
        //$account_id = intval($s_account_info['id']);
        $model_keyword = Model('keyword');
        $keyinfo = $model_keyword->where(array('iskey'=>1,'kid'=>$kid,'user_id'=>$account_id))->find();
        //$keyinfo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."keyword WHERE iskey=1 AND kid=".$kid." AND user_id=".$account_id);
        //$userid=$this->visitor->get('user_id');
        //$model_keyword=& m('keyword');
        //$keyinfo = $model_keyword->get("iskey=1 AND kid=".$kid." AND user_id=".$userid);
        //$keyinfo = current($keyinfo);
        if(!empty($keyinfo)){
            if($keyinfo['type']==2)
            {
                $keyinfo['titles2']=unserialize($keyinfo['titles']);
                $keyinfo['imageinfo2']=unserialize($keyinfo['imageinfo']);
                $keyinfo['linkinfo2']=unserialize($keyinfo['linkinfo']);
                echo json_encode($keyinfo);
            }else{
                echo json_encode($keyinfo);
            }
        }
    }
    
    //上传微信封面图片 by shisukj
    public function upload_fmWt()
    {
        $account_id = intval($_SESSION['member_id']);
        
        //上传处理
        $upd_id = $id = $account_id;
        if ($_FILES['fm_file']['name'] != ''){
            $upload = new UploadFile();
            $upload->set('default_dir',ATTACH_WEIXIN.'/'.$account_id);
            $upload->set('max_size', C('image_max_filesize'));
            //生成4张缩略图，宽高依次如下
            $thumb_width    = '200,900';
            $thumb_height   = '200,500';
            $upload->set('thumb_width', $thumb_width);
            $upload->set('thumb_height',$thumb_height);
            //4张缩略图名称扩展依次如下
            $upload->set('thumb_ext',   '_small,_mid');
            $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
            //生成新图的扩展名为.jpg
            $upload->set('new_ext','jpg');
            $result = $upload->upfile2('fm_file');
            if ($result){
                $_POST['fm_file'] = $upload->file_name;
                $file_name = substr($upload->file_name,0,-4);
                
            }else {
                showMessage($upload->error);
            }
        }
        
        $fm_file_big = UPLOAD_SITE_URL."/weixin/".$account_id."/".$file_name."_mid.jpg";
        $fm_file_big_url = UPLOAD_SITE_URL."/weixin/".$account_id."/".$file_name."_mid.jpg";
        
        $fm_file_middle = UPLOAD_SITE_URL."/weixin/".$account_id."/".$file_name."_small.jpg";
        $fm_file_middle_url = UPLOAD_SITE_URL."/weixin/".$account_id."/".$file_name."_small.jpg";
        
        $data['status'] = 1;
        
        if($fm_file_big){
            $data['big_url'] = $fm_file_big_url;
        }else{
            $data['big_url'] = UPLOAD_SITE_URL."/weixin/nofm_big.gif";
        }
        
        if($fm_file_middle){
            $data['mid_url'] = $fm_file_middle_url;
        }else{
           $data['mid_url'] = UPLOAD_SITE_URL."/weixin/nofm_big.gif";
        }
        
        $this->ajax_return($data);
    }
    
    /*ajax返回*/
    function ajax_return($data)
    {
        header("Content-Type:text/html; charset=utf-8");
        echo(json_encode($data));
        exit;   
    }

}