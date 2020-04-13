<?php
/**
 * 店铺展示图片空间操作
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class photosControl extends BaseSellerControl {
    public function indexWt(){
        $this->cateWt();
        exit;
    }
    public function __construct() {
        parent::__construct();
        Language::read('member_store_album');
    }

    /**
     * 相册分类列表
     *
     */
    public function cateWt(){
        $model_album = Model('photos');



        /**
         * 相册分类
         */
        $param = array();
        $param['photos_aclass.store_id'] = $_SESSION['store_id'];
        $param['order']                 = 'ac_sort desc';
        if($_GET['sort'] != ''){
            switch ($_GET['sort']){
                case '0':
                    $param['order']     = 'add_time desc';
                    break;
                case '1':
                    $param['order']     = 'add_time asc';
                    break;
                case '2':
                    $param['order']     = 'ac_name desc';
                    break;
                case '3':
                    $param['order']     = 'ac_name asc';
                    break;
                case '4':
                    $param['order']     = 'ac_sort desc';
                    break;
                case '5':
                    $param['order']     = 'ac_sort asc';
                    break;
            }
        }
        $aclass_info = $model_album->getClassList($param,$page);
        Tpl::output('aclass_info',$aclass_info);
//      Tpl::output('show_page',$page->show());

        Tpl::output('PHPSESSID',session_id());
        self::profile_menu('photos','photos');
        Tpl::showpage('photos.list');
    }
    /**
     * 相册分类添加
     *
     */
    public function album_addWt(){
        /**
         * 实例化相册模型
         */
        $model_album = Model('photos');
        $class_count = $model_album->countClass($_SESSION['store_id']);
        Tpl::output('class_count',$class_count['count']);
        Tpl::showpage('photos.class_add','null_layout');
    }
    /**
     * 相册保存
     *
     */
    public function album_add_saveWt(){
        if (chksubmit()){
            /**
             * 实例化相册模型
             */
            $model_album = Model('photos');
            $class_count = $model_album->countClass($_SESSION['store_id']);
            if($class_count['count'] >= 40){
                showDialog(Language::get('album_class_save_max_20'),urlSeller('photos'),'error',empty($_GET['inajax'])?'':'CUR_DIALOG.close();');
            }
            /**
             * 实例化相册模型
             */
            $param = array();
            $param['ac_name']   = $_POST['name'];
            $param['store_id']      = $_SESSION['store_id'];
            $param['ac_des']    = $_POST['description'];
            $param['ac_sort']   = intval($_POST['sort']);
            $param['add_time']   = time();

            $return = $model_album->addClass($param);
            if($return){
                showDialog(Language::get('album_class_save_succeed'),urlSeller('photos'),'succ',empty($_GET['inajax'])?'':'CUR_DIALOG.close();');
            }
        }
        showDialog(Language::get('album_class_save_lose'));
    }
    /**
     * 相册分类编辑
     */
    public function album_editWt(){
        if(empty($_GET['id'])){
            echo Language::get('album_parameter_error');exit;
        }
        /**
         * 实例化相册模型
         */
        $model_album = Model('photos');
        $param = array();
        $param['field']     = array('ac_id','store_id');
        $param['value']     = array(intval($_GET['id']),$_SESSION['store_id']);
        $class_info = $model_album->getOneClass($param);
        Tpl::output('class_info',$class_info);

        Tpl::showpage('photos.class_edit','null_layout');
    }
    /**
     * 相册分类编辑保存
     */
    public function album_edit_saveWt(){
        $param = array();
        $param['ac_name']   = $_POST['name'];
        $param['ac_des']    = $_POST['description'];
        $param['ac_sort']   = intval($_POST['sort']);


        /**
         * 实例化相册模型
         */
        $model_album = Model('photos');
        /**
         * 验证
         */
        $return = $model_album->checkAlbum(array('photos_aclass.store_id'=>$_SESSION['store_id'],'photos_aclass.ac_id'=>intval($_POST['id'])));
        if($return){
            /**
             * 更新
             */
            $re = $model_album->updateClass($param,intval($_POST['id']));
            if($re){
                showDialog(Language::get('album_class_edit_succeed'),urlSeller('photos'),'succ',empty($_GET['inajax'])?'':'CUR_DIALOG.close();');
            }
        }else{
            showDialog(Language::get('album_class_edit_lose'));
        }
    }
    /**
     * 相册删除
     */
    public function album_delWt(){
        if(empty($_GET['id'])){
            showMessage(Language::get('album_parameter_error'),'','html','error');
        }
        /**
         * 实例化相册模型
         */
        $model_album = Model('photos');

        /**
         * 验证
         */
        $return = $model_album->checkAlbum(array('photos_aclass.store_id'=>$_SESSION['store_id'],'photos_aclass.ac_id'=>intval($_GET['id']),'is_default'=>'0'));
        if(!$return){
            showDialog(Language::get('album_class_file_del_lose'));
        }
        /**
         * 删除分类
         */
        $return = $model_album->delClass(intval($_GET['id']));
        if(!$return){
            showDialog(Language::get('album_class_file_del_lose'));
        }
        /**
         * 更新图片分类
         */
        $param = array();
        $param['field']     = array('is_default','store_id');
        $param['value']     = array('1',$_SESSION['store_id']);
        $class_info = $model_album->getOneClass($param);
        $param = array();
        $param['ac_id'] = $class_info['ac_id'];
        $return = $model_album->updatePic($param,array('ac_id'=>intval($_GET['id'])));
        if($return){
            showDialog(Language::get('album_class_file_del_succeed'),urlSeller('photos'),'succ');
        }else{
            showDialog(Language::get('album_class_file_del_lose'));
        }
    }
    /**
     * 图片列表
     */
    public function album_pic_listWt(){
        if(empty($_GET['id'])) {
            showMessage(Language::get('album_parameter_error'),'','html','error');
        }

        /**
         * 分页类
         */
        $page   = new Page();
        $page->setEachNum(15);
        $page->setStyle('admin');

        /**
         * 实例化相册类
         */
        $model_album = Model('photos');

        $param = array();
        $param['ac_id'] = intval($_GET['id']);
        $param['photos_pic.store_id']    = $_SESSION['store_id'];
        if($_GET['sort'] != ''){
            switch ($_GET['sort']){
                case '0':
                    $param['order']     = 'add_time desc';
                    break;
                case '1':
                    $param['order']     = 'add_time asc';
                    break;
                case '2':
                    $param['order']     = 'ap_size desc';
                    break;
                case '3':
                    $param['order']     = 'ap_size asc';
                    break;
                case '4':
                    $param['order']     = 'ap_name desc';
                    break;
                case '5':
                    $param['order']     = 'ap_name asc';
                    break;
            }
        }
        $pic_list = $model_album->getPicList($param,$page);
        Tpl::output('pic_list',$pic_list);
        Tpl::output('show_page',$page->show());

        /**
         * 相册列表，移动
         */
        $param = array();
        $param['photos_class.un_ac_id']  = intval($_GET['id']);
        $param['photos_aclass.store_id'] = $_SESSION['store_id'];
        $class_list = $model_album->getClassList($param);
        Tpl::output('class_list',$class_list);
        /**
         * 相册信息
         */
        $param = array();
        $param['field']     = array('ac_id','store_id');
        $param['value']     = array(intval($_GET['id']),$_SESSION['store_id']);
        $class_info         = $model_album->getOneClass($param);
        Tpl::output('class_info',$class_info);

        Tpl::output('PHPSESSID',session_id());
        self::profile_menu('album_pic','pic_list');
        Tpl::showpage('photos.pic_list');
    }

    /**
     * 修改相册封面
     */
    public function change_album_coverWt(){
        if(empty($_GET['id'])) {
            showDialog(Language::get('wt_common_op_fail'));
        }
        /**
         * 实例化相册类
         */
        $model_album = Model('photos');
        /**
         * 图片信息
         */
        $param = array();
        $param['field']     = array('ap_id','store_id');
        $param['value']     = array(intval($_GET['id']),$_SESSION['store_id']);
        $pic_info           = $model_album->getOnePicById($param);
        $return = $model_album->checkAlbum(array('photos_aclass.store_id'=>$_SESSION['store_id'],'photos_aclass.ac_id'=>$pic_info['ac_id']));
        if($return){
            $re = $model_album->updateClass(array('ac_cover'=>$pic_info['ap_cover']),$pic_info['ac_id']);
            if($re){
                showDialog(Language::get('wt_common_op_succ'),'reload','succ');
            }
        }else{
            showDialog(Language::get('wt_common_op_fail'));
        }
    }
    /**
     * ajax修改图名称
     */
    public function change_pic_nameWt(){
        if(empty($_POST['id']) && empty($_POST['name'])){
            echo 'false';
        }
        /**
         * 实例化相册类
         */
        $model_album = Model('photos');

        /**
         * 更新图片名称
         */
        if(strtoupper(CHARSET) == 'GBK'){
            $_POST['name'] = Language::getGBK($_POST['name']);
        }
        $return = $model_album->updatePic(array('ap_name'=>$_POST['name']),array('ap_id'=>intval($_POST['id'])));
        if($return){
            echo 'true';
        }else{
            echo 'false';
        }
    }
    /**
     * 图片详细页
     */
    public function album_pic_infoWt(){
        if(empty($_GET['class_id']) && empty($_GET['id'])){
            showMessage(Language::get('album_parameter_error'),'','html','error');
        }
        /**
         * 实例化相册类
         */
        $model_album = Model('photos');

        /**
         * 验证
         */
        $return = $model_album->checkAlbum(array('photos_pic.store_id'=>$_SESSION['store_id'],'photos_pic.ap_id'=>intval($_GET['id'])));
        if(!$return){
            showMessage(Language::get('album_parameter_error'),'','html','error');
        }

        /**
         * 图片列表
         */
        $param = array();
        $param['ac_id']         = intval($_GET['class_id']);
        $param['store_id']          = $_SESSION['store_id'];
        $page   = new Page();
        $each_num = 9;
        $page->setEachNum($each_num);
        $pic_list                   = $model_album->getPicList($param,$page);
        Tpl::output('pic_list',$pic_list);

        $curpage = intval($_GET['curpage']);
        if (empty($curpage)) $curpage = 1;
        $total_page = (ceil($page->get('total_num')/$each_num));

        Tpl::output('total_page',$total_page);
        Tpl::output('curpage',$curpage);

        $curpage = intval($_GET['curpage']);
        if (empty($curpage)) $curpage = 1;
        $tatal_page = (ceil($page->get('total_num')/$each_num));
        Tpl::output('tatal_page',$tatal_page);
        Tpl::output('curpage',$curpage);


        /**
         * 相册信息
         */
        $param = array();
        $param['field']     = array('ac_id','store_id');
        $param['value']     = array(intval($_GET['class_id']),$_SESSION['store_id']);
        $class_info         = $model_album->getOneClass($param);
        Tpl::output('class_info',$class_info);

        /**
         * 图片信息
         */
        $param = array();
        $param['field']     = array('ap_id','store_id');
        $param['value']     = array(intval($_GET['id']),$_SESSION['store_id']);
        $pic_info           = $model_album->getOnePicById($param);
        $pic_info['ap_size'] = sprintf('%.2f',intval($pic_info['ap_size'])/1024);
        Tpl::output('pic_info',$pic_info);

        self::profile_menu('album_pic_info','pic_info');
        Tpl::showpage('photos.pic_info');
    }

    /**
     * 图片 ajax
     */
    public function album_ad_ajaxWt(){
        if(empty($_GET['class_id']) && empty($_GET['id'])){
            exit();
        }

        $model_album = Model('photos');

        $return = $model_album->checkAlbum(array('photos_pic.store_id'=>$_SESSION['store_id'],'photos_pic.ap_id'=>intval($_GET['id'])));
        if(!$return){
            exit();
        }

        /**
         * 图片列表
         */
        $param = array();
        $param['ac_id']         = intval($_GET['class_id']);
        $param['store_id']          = $_SESSION['store_id'];
        $page   = new Page();
        $each_num = 9;
        $page->setEachNum($each_num);
        $pic_list                   = $model_album->getPicList($param,$page);
        Tpl::output('pic_list',$pic_list);

        Tpl::showpage('photos.pic_scroll_ajax','null_layout');
    }

    /**
     * 图片删除
     */
    public function album_pic_delWt(){
        if (empty($_POST)) $_POST = $_GET;
        if(empty($_POST['id'])) {
            showDialog(Language::get('album_parameter_error'));
        }
        $model_album = Model('photos');
        if(!empty($_POST['id']) && is_array($_POST['id'])){
            $id = "'".implode("','", $_POST['id'])."'";
        }else{
            $id = "'".intval($_POST['id'])."'";
        }

        $return = $model_album->checkAlbum(array('photos_pic.store_id'=>$_SESSION['store_id'],'in_ap_id'=>$id));
        if(!$return){
            showDialog(Language::get('album_class_pic_del_lose'));
        }

        //删除图片
        $return = $model_album->delPic($id, $_SESSION['store_id']);
        if($return){
            showDialog(Language::get('album_class_pic_del_succeed'),'reload','succ');
        }else{
            showDialog(Language::get('album_class_pic_del_lose'));
        }
    }

    /**
     * 移动相册
     */
    public function album_pic_moveWt(){
        /**
         * 实例化相册类
         */
        $model_album = Model('photos');
        if(chksubmit()){
            if(empty($_REQUEST['id'])){
                showDialog(Language::get('album_parameter_error'));
            }
            if(!empty($_REQUEST['id']) && is_array($_REQUEST['id'])){
                $_REQUEST['id'] = trim(implode("','", $_REQUEST['id']),',');
            }

                /**
                 * 验证封面图片
                 */
                $param = array();
                $param['in_ap_id'] = "'".$_REQUEST['id']."'";
                $list_pic = $model_album->getClassList($param);
                $class_cover = $list_pic['0']['ac_cover'];
                $class_id    = $list_pic['0']['ac_id'];
                unset($list_pic);
                if($class_cover != ''){
                    $list_pic = $model_album->getPicList($param);
                    foreach ($list_pic as $val){
                        if(str_ireplace('.', '_small.', $val['ap_cover']) == $class_cover){
                            $model_album->updateClass(array('ac_cover'=>''),$class_id);
                            break;
                        }
                    }
                }

            $param = array();
            $param['ac_id'] = $_REQUEST['cid'];
            $return = $model_album->updatePic($param,array('in_ap_id'=>"'".$_REQUEST['id']."'"));
            if($return){
                showDialog(Language::get('album_class_pic_move_succeed'),'reload','succ');
            }else{
                showDialog(Language::get('album_class_pic_move_lose'));
            }
        }
        $param = array();
        $param['photos_class.un_ac_id']  = $_GET['cid'];
        $param['photos_aclass.store_id'] = $_SESSION['store_id'];
        $class_list = $model_album->getClassList($param);

        if(isset($_GET['id']) && !empty($_GET['id'])){
            Tpl::output('id',$_GET['id']);
        }
        Tpl::output('class_list',$class_list);
        Tpl::showpage('photos.move','null_layout');
    }

    /**
     * 替换图片
     */
    public function replace_image_uploadWt() {
        $file = $_GET['id'];
        $tpl_array = explode('_', $file);
        $id = intval(end($tpl_array));
        $model_album = Model('photos');
        $param = array();
        $param['field'] = array('ap_id', 'store_id');
        $param['value'] = array($id, $_SESSION['store_id']);
        $apic_info = $model_album->getOnePicById($param);
        if (substr(strrchr($apic_info['ap_cover'], "."), 1) != substr(strrchr($_FILES[$file]["name"], "."), 1)) {
            // 后缀名必须相同
            $error = L('album_replace_same_type');
            if (strtoupper(CHARSET) == 'GBK') {
                $error = Language::getUTF8($error);
            }
            echo json_encode( array('state' => 'false', 'message' => $error) );
            exit();
        }
        $pic_cover = implode(DS, explode(DS, $apic_info['ap_cover'], -1)); // 文件路径
        $tmpvar = explode(DS, $apic_info['ap_cover']);
        $pic_name = end($tmpvar); // 文件名称
        /**
         * 上传图片
         */
        $upload = new UploadFile();
        $upload->set('default_dir', ATTACH_GOODS . DS . $_SESSION['store_id'] . DS . $pic_cover);
        $upload->set('max_size', C('image_max_filesize'));
        $upload->set('thumb_width', GOODS_IMAGES_WIDTH);
        $upload->set('thumb_height', GOODS_IMAGES_HEIGHT);
        $upload->set('thumb_ext', GOODS_IMAGES_EXT);
        $upload->set('file_name', $pic_name);
        $return = $upload->upfile($file,true);
        if (!$return) {
            // 后缀名必须相同、
            if (strtoupper(CHARSET) == 'GBK') {
                $error = Language::getUTF8($upload->error);
            }
            echo json_encode( array('state' => 'false', 'message' => $upload->error) );
            exit();
        }
        /**
         * 取得图像大小
         */
        // 取得图像大小
        if (!C('oss.open')) {
            list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH . DS . ATTACH_GOODS . DS . $_SESSION['store_id'] . DS . $apic_info['ap_cover']);
        } else {
            list($width, $height, $type, $attr) = getimagesize(C('oss.img_url') . '/' . ATTACH_GOODS . '/' . $_SESSION['store_id'] . DS . $apic_info['ap_cover']);
        }
        /**
         * 更新图片分类
         */
        $param = array();
        $param['apic_size'] = intval($_FILES[$file]['size']);
        $param['apic_spec'] = $width . 'x' . $height;
        $return = $model_album->updatePic($param, array('apic_id' => $id));

        echo json_encode( array('state' => 'true', 'id' => $id) );
        exit();
    }

	
	
	/**
	 * 添加水印
	 */
	public function album_pic_watermarkWt(){
		if(empty($_POST['id']) && !is_array($_POST['id'])) {
			showDialog(Language::get('album_parameter_error'));
		}

		$id = trim(implode(',', $_POST['id']),',');

		/**
		 * 实例化图片模型
		 */
		$model_album = Model('photos');
		$param['in_ap_id']	= $id;
		$param['store_id']		= $_SESSION['store_id'];
		$wm_list = $model_album->getPicList($param);
		$model_store_wm = Model('store_watermark');
		$store_wm_info = $model_store_wm->getOneStoreWMByStoreId($_SESSION['store_id']);
		if ($store_wm_info['wm_image_name'] == '' && $store_wm_info['wm_text'] == ''){
			showDialog(Language::get('album_class_setting_wm'),urlSeller('photos','store_watermark'),'error','CUR_DIALOG.close();');//"请先设置水印"
		}
		import('bin.gdimage');
		$gd_image = new GdImage();
		$gd_image->setWatermark($store_wm_info);

		foreach ($wm_list as $v) {
			$gd_image->create(BASE_UPLOAD_PATH.DS.ATTACH_GOODS.DS.$_SESSION['store_id'].DS.str_ireplace('.', '_1280.', $v['ap_cover']));//生成有水印的大图
		}
		showDialog(Language::get('album_pic_plus_wm_succeed'),'reload','succ');
	}

    /**
     * 水印管理
     */
    public function store_watermarkWt(){
        /**
         * 读取语言包
         */
        Language::read('member_store_index');
        $model_store_wm = Model('store_watermark');
        /**
         * 获取会员水印设置
         */
        $store_wm_info = $model_store_wm->getOneStoreWMByStoreId($_SESSION['store_id']);
        /**
         * 保存水印配置信息
         */
        if (chksubmit()){
            $param = array();
            $param['wm_image_pos']          = $_POST['image_pos'];
            $param['wm_image_transition']   = $_POST['image_transition'];
            $param['wm_text']               = $_POST['wm_text'];
            $param['wm_text_size']          = $_POST['wm_text_size'];
            $param['wm_text_angle']         = $_POST['wm_text_angle'];
            $param['wm_text_font']          = $_POST['wm_text_font'];
            $param['wm_text_pos']           = $_POST['wm_text_pos'];
            $param['wm_text_color']         = $_POST['wm_text_color'];
            $param['jpeg_quality']          = $_POST['image_quality'];
            if (!empty($_FILES['image']['name'])){
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_WATERMARK);
                $result = $upload->upfile('image');
                if ($result){
                    $param['wm_image_name'] = $upload->file_name;
                    /**
                     * 删除旧水印
                     */
                    if (!empty($store_wm_info['wm_image_name'])){
                        @unlink(BASE_UPLOAD_PATH.DS.ATTACH_WATERMARK.DS.$store_wm_info['wm_image_name']);
                    }
                }else {
                    showDialog($upload->error);
                }
            }elseif ($_POST['is_del_image'] == 'ok'){
                /**
                 * 删除水印
                 */
                if (!empty($store_wm_info['wm_image_name'])){
                    $param['wm_image_name'] = '';
                    @unlink(BASE_UPLOAD_PATH.DS.ATTACH_WATERMARK.DS.$store_wm_info['wm_image_name']);
                }
            }
            $param['wm_id'] = $store_wm_info['wm_id'];
            $result = $model_store_wm->updateStoreWM($param);
            if ($result){
                showDialog(Language::get('store_watermark_congfig_success'),'reload','succ');
            }else {
                showDialog(Language::get('store_watermark_congfig_fail'));
            }
        }
        /**
         * 获取水印字体
         */
        $dir_list = array();
        readFileList(BASE_STATIC_PATH.DS.'font',$dir_list);
        if (!empty($dir_list) && is_array($dir_list)){
            $fontInfo = array();
            include BASE_STATIC_PATH.DS.'font'.DS.'font.info.php';
            foreach ($dir_list as $value){
                $d_array = explode('.',$value);
                if (strtolower(end($d_array)) == 'ttf' && file_exists($value)){
                    $dir_array = explode('/', $value);
                    $value = array_pop($dir_array);
                    $tmp = explode('.',$value);
                    $file_list[$tmp[0]] = $fontInfo[$tmp[0]];
                }
            }
            /**
             * 转码
             */
            if (strtoupper(CHARSET) == 'GBK'){
                $file_list = Language::getGBK($file_list);
            }
            Tpl::output('file_list',$file_list);
        }
        if (empty($store_wm_info)){
            /**
             * 新建店铺水印设置信息
             */
            $model_store_wm->addStoreWM(array(
                'wm_text_font'=>'default',
                'store_id'=>$_SESSION['store_id']
            ));
            $store_wm_info = $model_store_wm->getOneStoreWMByStoreId($_SESSION['store_id']);
        }
        self::profile_menu('photos','watermark');
        Tpl::output('store_wm_info',$store_wm_info);
        Tpl::showpage('watermark.form');
    }    /**
     * 上传图片
     *
     */
    public function image_uploadWt() {
        $store_id = $_SESSION ['store_id'];
        if (! empty ( $_POST ['category_id'] )) {
            $category_id = intval ( $_POST ['category_id'] );
        } else {
            $error = '上传 图片失败';
            if (strtoupper ( CHARSET ) == 'GBK') {
                $error = Language::getUTF8($error);
            }
            $data['state'] = 'false';
            $data['message'] = $error;
            $data['origin_file_name'] = $_FILES["file"]["name"];
            echo json_encode($data);
            exit();
        }
        // 判断图片数量是否超限
        $album_limit = $this->store_grade['sg_album_limit'];
        if ($album_limit > 0) {
            $album_count = Model('photos')->getCount(array('store_id' => $store_id));
            if ($album_count >= $album_limit) {
                // 目前并不出该提示，而是提示上传0张图片
                $error = L('store_goods_album_climit');
                if (strtoupper ( CHARSET ) == 'GBK') {
                    $error = Language::getUTF8($error);
                }
                $data['state'] = 'false';
                $data['message'] = $error;
                $data['origin_file_name'] = $_FILES["file"]["name"];
                $data['state'] = 'true';
                echo json_encode($data);
                exit();
            }
        }

        /**
         * 上传图片
         */
        $upload = new UploadFile();
        $upload->set('default_dir', ATTACH_GOODS . DS . $store_id . DS . $upload->getSysSetPath());
        $upload->set('max_size', C('image_max_filesize'));
        $upload->set('thumb_width', GOODS_IMAGES_WIDTH);
        $upload->set('thumb_height', GOODS_IMAGES_HEIGHT);
        $upload->set('thumb_ext', GOODS_IMAGES_EXT);
        $upload->set('fprefix', $store_id);
        $result = $upload->upfile('file',true);
        if ($result) {
            $pic = $upload->getSysSetPath() . $upload->file_name;
            $pic_thumb = $upload->getSysSetPath() . $upload->thumb_image;
        } else {
            // 目前并不出该提示
            $error = $upload->error;
            if (strtoupper(CHARSET) == 'GBK') {
                $error = Language::getUTF8($error);
            }
            $data['state'] = 'false';
            $data['message'] = $error;
            $data['origin_file_name'] = $_FILES["file"]["name"];
            echo json_encode($data);
            exit();
        }

        // 取得图像大小
        if (!C('oss.open')) {
            list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH . '/' . ATTACH_GOODS . '/' . $store_id . DS . $pic);
        } else {
            list($width, $height, $type, $attr) = getimagesize(C('oss.img_url') . '/' . ATTACH_GOODS . '/' . $store_id . DS . $pic);
        }
        $image = explode('.', $_FILES["file"]["name"]);
        if (strtoupper(CHARSET) == 'GBK') {
            $image['0'] = Language::getGBK($image['0']);
        }
        $insert_array = array();
        $insert_array['ap_name'] = $image['0'];
        $insert_array['ap_tag'] = '';
        $insert_array['ac_id'] = $category_id;
        $insert_array['ap_cover'] = $pic;
        $insert_array['ap_size'] = intval($_FILES['file']['size']);
        $insert_array['ap_spec'] = $width . 'x' . $height;
        $insert_array['add_time'] = time();
        $insert_array['store_id'] = $store_id;
        $result = Model('photos')->addPic($insert_array);

        $data = array ();
        $data['file_id'] = $result;
        $data['file_name'] = $pic;
        $data['origin_file_name'] = $_FILES["file"]["name"];
        $data['file_path'] = $pic;
        $data['instance'] = $_GET['instance'];
        $data['state'] = 'true';
        /**
         * 整理为json格式
         */
        $output = json_encode ( $data );
        echo $output;
    }


    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key=''){
        $menu_array = array();
        switch ($menu_type) {
            case 'photos':
                $menu_array = array(
                1=>array('menu_key'=>'photos','menu_name'=>Language::get('wt_member_path_my_album'),'menu_url'=>urlSeller('photos')),
                2=>array('menu_key'=>'watermark','menu_name'=>Language::get('wt_member_path_watermark'),'menu_url'=>urlSeller('photos','store_watermark'))
                );
                break;
            case 'album_pic':
                $menu_array = array(
                1=>array('menu_key'=>'photos','menu_name'=>Language::get('wt_member_path_my_album'),'menu_url'=>urlSeller('photos')),
                3=>array('menu_key'=>'pic_list','menu_name'=>Language::get('wt_member_path_album_pic_list'),'menu_url'=>urlSeller('photos','album_pic_list',array('id'=>intval($_GET['id'])))),
                2=>array('menu_key'=>'watermark','menu_name'=>Language::get('wt_member_path_watermark'),'menu_url'=>urlSeller('photos','store_watermark'))
                );
                break;
            case 'album_pic_info':
                $menu_array = array(
                1=>array('menu_key'=>'photos','menu_name'=>Language::get('wt_member_path_my_album'),'menu_url'=>urlSeller('photos')),
                3=>array('menu_key'=>'pic_info','menu_name'=>Language::get('wt_member_path_album_pic_info'),'menu_url'=>urlSeller('photos','album_pic_info',array('class_id'=>intval($_GET['class_id']),'id'=>intval($_GET['id'])))),
                2=>array('menu_key'=>'watermark','menu_name'=>Language::get('wt_member_path_watermark'),'menu_url'=>urlSeller('photos','store_watermark'))
                );
                break;
        }
        if (C('oss.open')) {
            unset($menu_array[2]);
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
    /**
     * ajax返回图片信息
     */
    public function ajax_change_imgmessageWt(){
        $str_array = explode('/', $_GET['url']);
        $str = array_pop($str_array);
        $str = explode('.', $str);
        /**
         * 实例化图片模型
         */
        $model_album = Model('photos');
        $param = array();
        $search = explode(',', GOODS_IMAGES_EXT);
        $param['like_cover']    = str_ireplace($search, '', $str['0']);
        $pic_info = $model_album->getPicList($param);

        /**
         * 小图尺寸
         */
        list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH.DS.ATTACH_GOODS.DS.$_SESSION['store_id'].DS.$pic_info['0']['ap_cover']);
        if(strtoupper(CHARSET) == 'GBK'){
            $pic_info['0']['ap_name'] = Language::getUTF8($pic_info['0']['ap_name']);
        }
        echo json_encode(array(
                'img_name'=>$pic_info['0']['ap_name'],
                'default_size'=>sprintf('%.2f',intval($pic_info['0']['ap_size'])/1024),
                'default_spec'=>$pic_info['0']['ap_spec'],
                'upload_time'=>date('Y-m-d',$pic_info['0']['add_time']),
                'small_spec'=>$width.'x'.$height
            ));
    }
    /**
     * ajax验证名称时候重复
     */
    public function ajax_check_class_nameWt(){
        $ac_name    = trim($_GET['ac_name']);
        if($ac_name == ''){
            echo 'true';die;
        }
        $model_album    = Model('photos');
        $param = array();
        $param['field']     = array('ac_name','store_id');
        $param['value']     = array($ac_name,$_SESSION['store_id']);
        $class_info = $model_album->getOneClass($param);
        if(!empty($class_info)){
            echo 'false';die;
        }else{
            echo 'true';die;
        }
    }
}
