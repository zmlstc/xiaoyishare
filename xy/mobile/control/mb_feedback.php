<?php
/**
 * 合作伙伴管理
 *
 *
 *
 *
 * @ShopWT版权所有，商业用途请到shopwt.com授权
 * @license    http://www.shopwt.com
 * @link       交流群号：58219240
 */



defined('ShopWT') or exit('Access Denied By ShopWT');
class mb_feedbackControl extends SystemControl{
    private $links = array(
        array('url'=>'mb_feedback/flist','text'=>'用户反馈'),
        array('url'=>'mb_feedback/fbclass','text'=>'反馈类型')
    );
    public function __construct(){
        parent::__construct();
        Language::read('mobile');
    }

    public function indexWt() {
        $this->flistWt();
    }
    /**
     * 意见反馈
     */
    public function flistWt(){
        $model_mb_feedback = Model('mb_feedback');
        $list = $model_mb_feedback->getMbFeedbackList(array(), 10);

        Tpl::output('list', $list);
        Tpl::output('page', $model_mb_feedback->showpage());
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'flist'));
        Tpl::setDirquna('mobile');
		Tpl::showpage('mb_feedback.index');
    }

    /**
     * 输出XML数据
     */
    public function get_xmlWt() {
        $model_mb_feedback = Model('mb_feedback');
        $condition = array();
        if ($_POST['query'] != '') {
            $condition[$_POST['qtype']] = array('like', '%' . $_POST['query'] . '%');
        }
        $order = '';
        $param = array('id', 'content', 'ftime', 'member_name', 'member_id');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $inform_list = $model_mb_feedback->getMbFeedbackList($condition, $page, $order);

        $data = array();
        $data['now_page'] = $model_mb_feedback->shownowpage();
        $data['total_num'] = $model_mb_feedback->gettotalnum();
        foreach ($inform_list as $value) {
            $param = array();
            $param['operation'] = "<a class='btn red' href=\"javascript:void(0);\" onclick=\"fg_del('".$value['id']."')\"><i class='fa fa-trash-o'></i>删除</a>";
            $param['operation'] .= "<a class='btn blue' href='".urlAdminMobile('mb_feedback','info',array('id'=>$value['id']))."'><i class=\"fa fa-list-alt\"></i>查看</a>";
            $param['id'] = $value['id'];
            $param['content'] = $value['content'];
            $param['ftime'] = date('Y-m-d H:i:s', $value['ftime']);
            $param['member_name'] = $value['member_name'];
            $param['member_id'] = $value['member_id'];
            $param['fbclass'] = $value['fbclass'];
            $param['lxway'] = $value['lxway'];
            $data['list'][$value['id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }
	
    public function infoWt(){
		$id = intval($_GET['id']);
        $model_mb_feedback = Model('mb_feedback');
        $info = $model_mb_feedback->getInfoById($id);
		
		$fb_image_240 = array();
		$fb_image_1024 = array();
		if (!empty($info['fb_image'])) {
			$image_array = explode(',', $info['fb_image']);
			foreach ($image_array as $value) {
				$fb_image_240[] = feedbackThumb($value, 240);
				$fb_image_1024[] = feedbackThumb($value, 1024);
			}
		}
		$info['fb_image_240'] = $fb_image_240;
		$info['fb_image_1024'] = $fb_image_1024;
		
		if(!empty($info)&&$info['is_read']==0){
			$model_mb_feedback->editMbFeedback(array('is_read'=>1), array('id'=>$id));
		}

        Tpl::output('info', $info);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'flist'));
        Tpl::setDirquna('mobile');
		Tpl::showpage('mb_feedback.info');
    }	

    /**
     * 删除
     */
    public function delWt(){
        $ids = explode(',', $_GET['id']);
        if (count($ids) == 0){
            exit(json_encode(array('state'=>false,'msg'=>L('wrong_argument'))));
        }
        $model_mb_feedback = Model('mb_feedback');
        $result = $model_mb_feedback->delMbFeedback($ids);
        if ($result){
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        }else {
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }
    }
	
	/**
     * 类型管理
     */
    public function fbclassWt(){
        $model_class = Model('feedback_class');
        /**
         * 列表
         */
        $class_list = $model_class->getClassList(array());
        

        Tpl::output('class_list',$class_list);
		Tpl::setDirquna('mobile');
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'fbclass'));
        Tpl::showpage('feedback_class.index');
    }

    /**
     * 分类 新增
     */
    public function fbclass_addWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('feedback_class');
        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["f_name"], "require"=>"true", "message"=>'名称不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {

                $insert_array = array();
                $insert_array['f_name'] = trim($_POST['f_name']);
                //$insert_array['f_parent_id'] = intval($_POST['f_parent_id']);

                $result = $model_class->add($insert_array);
                if ($result){
                    $url = array(
                        array(
                            'url'=>urlAdminMobile('mb_feedback','fbclass_add'),
                            'msg'=>'添加反馈分类',
                        ),
                        array(
                            'url'=>urlAdminMobile('mb_feedback','fbclass'),
                            'msg'=>'返回分类列表',
                        )
                    );
                    $this->log('添加反馈分类['.$_POST['f_name'].']',1);
                    showMessage('添加分类成功',$url);
                }else {
                    showMessage('添加分类失败');
                }
            }
        }


		Tpl::setDirquna('mobile');
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'fbclass'));
        Tpl::showpage('feedback_class.add');
    }

    /**
     * 分类编辑
     */
    public function fbclass_editWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('feedback_class');

        if (chksubmit()){
            /**
             * 验证
             */
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["f_name"], "require"=>"true", "message"=>'名称不能为空'),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {

                $update_array = array();
                $update_array['f_id'] = intval($_POST['f_id']);
                $update_array['f_name'] = trim($_POST['f_name']);

                $result = $model_class->updates($update_array);
                if ($result){
                    $url = array(
                        array(
                            'url'=>urlAdminMobile('mb_feedback','fbclass'),
                            'msg'=>'返回分类列表',
                        ),
                        array(
                            'url'=>urlAdminMobile('mb_feedback','fbclass_edit',array('f_id'=>intval($_POST['f_id']))),
                            'msg'=>'返回继续编辑',
                        ),
                    );
                    $this->log('编辑反馈类型['.$_POST['f_name'].']',1);
                    showMessage('编辑成功',urlAdminMobile('mb_feedback','fbclass'));
                }else {
                    showMessage('编辑失败');
                }
            }
        }

        $class_array = $model_class->getOneClass(intval($_GET['f_id']));
        if (empty($class_array)){
            showMessage($lang['param_error']);
        }

        Tpl::output('class_array',$class_array);
        //输出子菜单
        Tpl::output('top_link',$this->sublink($this->links,'fbclass'));
		Tpl::setDirquna('mobile');
        Tpl::showpage('feedback_class.edit');
    }

    /**
     * 删除分类
     */
    public function fbclass_delWt(){
        $lang   = Language::getLangContent();
        $model_class = Model('feedback_class');
        if (intval($_GET['f_id']) > 0){
            $model_class->del(intval($_GET['f_id']));
           
            $this->log('删除反馈类型[ID:'.intval($_GET['f_id']).']',1);
            showMessage('操作成功',urlAdminMobile('mb_feedback','fbclass'));
        }else {
            showMessage('操作失败',urlAdminMobile('mb_feedback','fbclass'));
        }
    }


	
	
}
