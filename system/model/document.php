<?php
/**
 * 系统文章
 *
 *
 *
 * * @ShopWT (c) 2015-2018 ShopWT Inc. (http://www.shopwt.com)
 * @license    http://www.sh opwt.c om
 * @link       交流群号：58219240
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 */
defined('ShopWT') or exit('Access Denied By ShopWT');

class documentModel extends Model
{
    /**
     * 查询所有系统文章
     */
    public function getList()
    {
        $param = array(
            'table' => 'document'
        );
        return Db::select($param);
    }

    /**
     * 根据编号查询一条
     *
     * @param unknown_type $id
     */
    public function getOneById($id)
    {
        $param = array(
            'table' => 'document',
            'field' => 'doc_id',
            'value' => $id
        );
        return Db::getRow($param);
    }

    /**
     * 根据标识码查询一条
     *
     * @param unknown_type $id
     */
    public function getOneByCode($code)
    {
        $param = array(
            'table' => 'document',
            'field' => 'doc_code',
            'value' => $code
        );
        return Db::getRow($param);
    }

    /**
     * 更新
     *
     * @param unknown_type $param
     */
    public function updates($param)
    {
        return Db::update('document', $param, "doc_id='{$param['doc_id']}'");
    }
}
