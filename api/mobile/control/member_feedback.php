<?php
/**
 * 我的反馈
 *
 * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */



defined('ShopWT') or exit('Access Denied By ShopWT');

class member_feedbackControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }
	public function indexWt() {
		 $this->classWt();
	}
	public function classWt() {
		$model_class = Model('feedback_class');
        $class_list = $model_class->getClassList(array()); 
		/* $list = array();
		$clist = array();
		if(!empty($class_list)&&is_array($class_list)){
			foreach($class_list as $v){
				$list[] = $v;
				$clist[] = $v['f_name'];
			}
		} */
		output_data(array('list'=>$class_list));
	}

    /**
     * 添加反馈
     */
    public function feedback_addWt() {
        $model_mb_feedback = Model('mb_feedback');

        $param = array();
        $param['content'] = $_POST['feedback'];
        $param['fb_image'] = $_POST['fb_image'];
        $param['lxway'] = $_POST['lxway'];
        $param['fbclass'] = $_POST['fbclass'];
        $param['fbclass_id'] = $_POST['fbclass_id'];
        $param['type'] = $this->member_info['client_type'];
        $param['ftime'] = TIMESTAMP;
        $param['member_id'] = $this->member_info['member_id'];
        $param['member_name'] = $this->member_info['member_name'];

        $result = $model_mb_feedback->addMbFeedback($param);

        if($result) {
            output_data('1');
        } else {
            output_error('保存失败');
        }
    }
	
	/**
     * 上传图片
     *
     * @param
     * @return
     */
    public function file_uploadWt() {
        /**
         * 读取语言包
         */
        Language::read('sns_home');
        $lang   = Language::getLangContent();
        $member_id  = $this->member_info['member_id'];
        
        $model = Model();
    
        /**
         * 上传图片
         */
        $upload = new UploadFile();
        $upload_dir = ATTACH_MALBUM.DS.'feedback'.DS;
    
        $upload->set('default_dir',$upload_dir.$upload->getSysSetPath());
        $thumb_width    = '240,1024';
        $thumb_height   = '2048,1024';
    
        $upload->set('max_size',C('image_max_filesize'));
        $upload->set('thumb_width', $thumb_width);
        $upload->set('thumb_height',$thumb_height);
        $upload->set('fprefix',$member_id);
        $upload->set('thumb_ext', '_240,_1024');
        $result = $upload->upfile('pic');
        if (!$result){
            output_error($upload->error);
        }
    
        $img_path = $upload->getSysSetPath().$upload->file_name;
        list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH.DS.ATTACH_MALBUM.DS.'feedback'.DS.$img_path);
    
        $image = explode('.', $_FILES["pic"]["name"]);

    
        $data = array();
        //$data['file_id'] = $result;
        $data['file_name'] = $img_path;
        $data['origin_file_name'] = $_FILES["file"]["name"];
        $data['file_url'] = feedbackThumb($img_path, 240);
        output_data($data);
    }
	
	
}
