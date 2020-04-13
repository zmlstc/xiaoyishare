<?php
/**
 * 文章 
 * @ShopWTb1 (c) 2005-2016 shopwt Inc.
 * @license    http://www.shopwt.com
 * @link       交流群号：216611541
 * @since      ShopWT版权所有，商业用途请到shopwt.com授权
 * 
 **/

defined('ShopWT') or exit('Access Denied By ShopWT');
class article_classControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }
    
    public function indexWt() {
			$article_class_model	= Model('article_class');
			$article_model	= Model('article');
			$condition	= array();
			
			$article_class = $article_class_model->getClassList($condition);
			output_data(array('article_class' => $article_class));		
    }
}
