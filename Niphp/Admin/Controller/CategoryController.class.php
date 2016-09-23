<?php
/**
 *
 * 栏目 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: CategoryController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class CategoryController extends CommonController
{

	/**
	 * 网站栏目
	 * @access public
	 * @param
	 * @return void
	 */
	public function category()
	{
		$this->assign('submenu', 1);

		$this->assign('parent', D(ACTION_NAME)->getParent());

		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('type', D(ACTION_NAME)->getType());
			$this->assign('model', D(ACTION_NAME)->getModel());
			$this->assign('level', D(ACTION_NAME)->getLevel());
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 模型
	 * @access public
	 * @param
	 * @return void
	 */
	public function model()
	{
		$this->assign('submenu', 1);

		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('model_list', D(ACTION_NAME)->getModel());
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 字段
	 * @access public
	 * @param
	 * @return void
	 */
	public function fields()
	{
		if (IS_AJAX) {
			// 获得栏目
			$id = I('post.id', '', C('PRIMARY_FILTER'));
			if (empty($id)) {
				exit();
			}
			$data = D(ACTION_NAME)->getCategory($id);
			if (empty($data)) {
				exit();
			}
			$type = I('post.type');
			$type++;
			$option = '<select name="category_id[]" id="category_id_' . $type . '" class="form-control op" data-type="' . $type . '" onchange="fieldsCategory(this)">';
			$option .= '<option value="0">' . L('select_category') . '</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
			}
			$option .= '</select>';
			exit($option);
		}

		$this->assign('submenu', 1);

		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('category_list', D(ACTION_NAME)->getCategory());
			$this->assign('type_list', D(ACTION_NAME)->getType());
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 分类
	 * @access public
	 * @param
	 * @return void
	 */
	public function type()
	{
		$this->assign('submenu', 1);

		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('category_list', D('fields')->getCategory());
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}
}