<?php
/**
 * 店铺展示相册管理
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT提供技术支持 授权请购买ShopWT授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class photosModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 计算数量
     *
     * @param array $condition 条件
     * $param string $table 表名
     * @return int
     */
    public function getPhotosPicCount($condition)
    {
        $result = $this->table('photos_pic')->where($condition)->count();
        return $result;
    }

    /**
     * 计算数量
     *
     * @param array $condition 条件
     * $param string $table 表名
     * @return int
     */
    public function getCount($condition, $table = 'photos_pic')
    {
        $result = $this->table($table)->where($condition)->count();
        return $result;
    }

    /**
     * 获取单条数据
     *
     * @param array $condition 条件
     * @param string $table 表名
     * @return array 一维数组
     */
    public function getOne($condition, $table = 'photos_pic')
    {
        $resule = $this->table($table)->where($condition)->find();
        return $resule;
    }

    /**
     * 分类列表
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getClassList($condition, $page = '')
    {
        $param = array();
        $param['table'] = 'photos_class,photos_pic';
        $param['field'] = 'photos_class.ac_id,min(photos_class.ac_name) as ac_name,min(photos_class.store_id) as store_id,min(photos_class.ac_des) as ac_des,min(photos_class.ac_sort) as ac_sort,min(photos_class.ac_cover) as ac_cover,min(photos_class.add_time) as add_time,min(photos_class.is_default) as is_default,count(photos_pic.ac_id) as count';
        $param['join_type'] = 'left join';
        $param['join_on'] = array('photos_class.ac_id = photos_pic.ac_id');
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'] ? $condition['order'] : 'ac_sort desc';
        $param['group'] = 'photos_class.ac_id';
        return Db::select($param, $page);
    }

    public function getPhotosList($condition, $page = '', $limit = null)
    {
        return $this->table('photos_class')->where($condition)->page($page)->limit($limit)->select();
    }

    /**
     * 计算分类数量
     *
     * @param int id
     * @return array 一维数组
     */
    public function countClass($id)
    {
        $param = array();
        $param['table'] = 'photos_class';
        $param['field'] = 'count(*) as count';
        $param['where'] = " and store_id = '$id'";
        $return = Db::select($param);
        return $return['0'];
    }

    /**
     * 验证相册
     *
     * @param array $param 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function checkAlbum($condition)
    {
        /**
         * 验证是否为当前合作伙伴
         */
        $check_array = self::getClassList($condition, '');
        if (!empty($check_array)) {
            unset($check_array);
            return true;
        }
        unset($check_array);
        return false;
    }

    /**
     * 图片列表
     *
     * @param array $condition 查询条件
     * @param obj $page 分页对象
     * @return array 二维数组
     */
    public function getPicList($condition, $page = '', $field = '*')
    {
        $param = array();
        $param['table'] = 'photos_pic';
        $param['where'] = $this->getCondition($condition);
        $param['order'] = $condition['order'] ? $condition['order'] : 'ap_id desc';
        $param['field'] = $field;
        return Db::select($param, $page);
    }

    /**
     * 添加相册分类
     *
     * @param array $input
     * @return bool
     */
    public function addClass($input)
    {
        if (is_array($input) && !empty($input)) {
            return Db::insert('photos_class', $input);
        } else {
            return false;
        }
    }

    /**
     * 添加相册图片
     *
     * @param array $input
     * @return bool
     */
    public function addPic($input)
    {
        $result = $this->table('photos_pic')->insert($input);
        return $result;
    }

    /**
     * 更新相册分类
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updateClass($input, $id)
    {
        if (is_array($input) && !empty($input)) {
            return Db::update('photos_class', $input, " ac_id='$id' ");
        } else {
            return false;
        }
    }

    /**
     * 更新相册图片
     *
     * @param array $input
     * @param int $id
     * @return bool
     */
    public function updatePic($input, $condition)
    {
        if (is_array($input) && !empty($input)) {
            return Db::update('photos_pic', $input, $this->getCondition($condition));
        } else {
            return false;
        }
    }

    /**
     * 删除分类
     *
     * @param string $id
     * @return bool
     */
    public function delClass($id)
    {
        if (!empty($id)) {
            return Db::delete('photos_class', " ac_id ='" . $id . "' ");
        } else {
            return false;
        }
    }

    /**
     * 根据店铺id删除图片空间相关信息
     *
     * @param int $id
     * @return bool
     */
    public function delAlbum($id)
    {
        $id = intval($id);
        Db::delete('photos_class', " store_id= " . $id);
        $pic_list = $this->getPicList(array(" store_id= " . $id), '', 'ap_cover');
        if (!empty($pic_list) && is_array($pic_list)) {
            $image_ext = explode(',', GOODS_IMAGES_EXT);
            foreach ($pic_list as $v) {
                foreach ($image_ext as $ext) {
                    $file = str_ireplace('.', $ext . '.', $v['ap_cover']);
                    @unlink(BASE_UPLOAD_PATH . DS . ATTACH_GOODS . DS . $id . DS . $file);
                }
            }
        }
        Db::delete('photos_pic', " store_id= " . $id);
    }

    /**
     * 删除图片
     *
     * @param string $id
     * @param int $store_id
     * @return bool
     */
    public function delPic($id, $store_id)
    {
        $pic_list = $this->getPicList(array('in_ap_id' => $id), '', 'ap_cover');

        /**
         * 删除图片
         */
        if (!empty($pic_list) && is_array($pic_list)) {
            $image_ext = explode(',', GOODS_IMAGES_EXT);
            foreach ($pic_list as $v) {
                if (C('oss.open')) {
                    if ($v['apic_cover'] != '') {
                        oss::del(array(ATTACH_GOODS . DS . $store_id . DS . $v['ap_cover']));
                    }
                } else {
                    @unlink(BASE_UPLOAD_PATH . DS . ATTACH_GOODS . DS . $store_id . DS . $v['ap_cover']);
                    foreach ($image_ext as $ext) {
                        $file = str_ireplace('.', $ext . '.', $v['ap_cover']);
                        @unlink(BASE_UPLOAD_PATH . DS . ATTACH_GOODS . DS . $store_id . DS . $file);
                    }
                }
            }
        }
        if (!empty($id)) {
            return Db::delete('photos_pic', 'ap_id in(' . $id . ')');
        } else {
            return false;
        }
    }

    /**
     * 查询单条分类信息
     *
     * @param int $id 活动id
     * @return array 一维数组
     */
    public function getOneClass($param)
    {
        if (is_array($param) && !empty($param)) {
            return Db::getRow(array_merge(array('table' => 'photos_class'), $param));
        } else {
            return false;
        }
    }

    /**
     * 根据id查询一张图片
     *
     * @param int $id 活动id
     * @return array 一维数组
     */
    public function getOnePicById($param)
    {
        if (is_array($param) && !empty($param)) {
            return Db::getRow(array_merge(array('table' => 'photos_pic'), $param));
        } else {
            return false;
        }
    }

    /**
     * 构造查询条件
     *
     * @param array $condition 条件数组
     * @return $condition_sql
     */
    private function getCondition($condition)
    {
        $condition_sql = '';
        if ($condition['ap_id'] != '') {
            $condition_sql .= " and ap_id= '{$condition['ap_id']}'";
        }
        if ($condition['ap_name'] != '') {
            $condition_sql .= " and ap_name='" . $condition['ap_name'] . "'";
        }
        if ($condition['ap_tag'] != '') {
            $condition_sql .= " and ap_tag like '%" . $condition['ap_tag'] . "%'";
        }
        if ($condition['ac_id'] != '') {
            $condition_sql .= " and ac_id= '{$condition['ac_id']}'";
        }
        if ($condition['photos_aclass.store_id'] != '') {
            $condition_sql .= " and photos_class.store_id = '{$condition['photos_aclass.store_id']}'";
        }
        if ($condition['photos_aclass.ac_id'] != '') {
            $condition_sql .= " and photos_class.ac_id= '{$condition['photos_aclass.ac_id']}'";
        }
        if ($condition['photos_pic.store_id'] != '') {
            $condition_sql .= " and photos_pic.store_id= '{$condition['photos_pic.store_id']}'";
        }
        if ($condition['photos_pic.ap_id'] != '') {
            $condition_sql .= " and photos_pic.ap_id= '{$condition['photos_pic.ap_id']}'";
        }
        if ($condition['store_id'] != '') {
            $condition_sql .= " and store_id= '{$condition['store_id']}'";
        }
        if ($condition['ac_name'] != '') {
            $condition_sql .= " and ac_name='" . $condition['ac_name'] . "'";
        }
        if ($condition['in_ap_id'] != '') {
            $condition_sql .= " and ap_id in (" . $condition['in_ap_id'] . ")";
        }
        if ($condition['gt_ap_id'] != '') {
            $condition_sql .= " and ap_id > '{$condition['gt_ap_id']}'";
        }
        if ($condition['like_cover'] != '') {
            $condition_sql .= " and ap_cover like '%" . $condition['like_cover'] . "%'";
        }
        if ($condition['is_default'] != '') {
            $condition_sql .= " and is_default= '{$condition['is_default']}'";
        }
        if ($condition['photos_class.un_ac_id'] != '') {
            $condition_sql .= " and photos_class.ac_id <> '{$condition['photos_class.un_ac_id']}'";
        }
        return $condition_sql;
    }
}
