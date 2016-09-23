<?php
/**
 *
 * 内容 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ContentController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ContentController extends CommonController
{

	/**
	 * 内容管理
	 * @access public
	 * @param
	 * @return void
	 */
	public function content()
	{
		// 栏目列表
		if ($this->_model == 'list') {
			$data = D(ACTION_NAME)->getCategory();
			$this->assign('list', $data['list']);

			$this->display('Content/content/manage');
		}

		// 内容列表
		if ($this->_model == 'manage') {
			$this->assign('submenu', 1);

			C('TOKEN_ON', false);

			// 排序
			if (IS_POST) {
				$return = D(ACTION_NAME)->upSort();
				if ($return === true) {
					$this->success(L('_success_sort'));
					exit();
				} else {
					$this->error($return);
				}
			}

			$data = D(ACTION_NAME)->getData();
			if (is_string($data)) {
				$this->error($data);
			}

			if ($data['tableName'] == 'message' || $data['tableName'] == 'feedback') {
				$this->assign('sort', 0);
			} else {
				$this->assign('sort', 1);
			}

			$this->assign('list', $data['list']);
			$this->assign('page', $data['page']);

			$this->display('Content/content/manage_list');
		}

		// 新增
		if ($this->_model == 'added') {
			if (IS_POST) {
				$return = D(ACTION_NAME)->added();
				if ($return === true) {
					$url = U(ACTION_NAME, array('d' => 'manage', 'cid' => I('get.cid')));
					$this->success(L('_success_added'), $url);
					exit();
				} else {
					$this->error($return);
				}
			}
			$this->assign('fields', D('content')->getFields());
			$this->assign('is_pass', D('content')->getCheck());
			$this->assign('level', D('category')->getLevel());
			$this->assign('type', D('content')->getType());

			$tableName = D('content')->getModelTable();
			if ($tableName == 'feedback' || $tableName == 'message') {
				redirect(U('content/content', array('d' => 'manage', 'cid' => I('get.cid'))));
			}
			$this->assign('model_name', $tableName);
			$this->display('Content/content/model/' . $tableName . '_added');
		}

		// 编辑
		if ($this->_model == 'editor') {
			if (IS_POST) {
				$return = D(ACTION_NAME)->editor();
				if ($return === true) {
					$this->success(L('_success_editor'));
					exit();
				} else {
					$this->error($return);
				}
			}
			$data = D(ACTION_NAME)->getDataOne();
			if (is_string($data)) {
				$this->error($data);
			}
			$this->assign('data', $data);
			$this->assign('fields', D('content')->getFields());
			$this->assign('is_pass', D('content')->getCheck());
			$this->assign('level', D('category')->getLevel());
			$this->assign('type', D('content')->getType());

			$tableName = D('content')->getModelTable();
			$this->assign('model_name', $tableName);
			$this->display('Content/content/model/' . $tableName . '_editor');
		}

		// 单页
		if ($this->_model == 'page') {
			if (IS_POST) {
				$return = D(ACTION_NAME)->pageEditor();
				if ($return === true) {
					$this->success(L('_success_editor'));
					exit();
				} else {
					$this->error($return);
				}
			}
			$data = D(ACTION_NAME)->getPageDataOne();
			if (is_string($data)) {
				$this->error($data);
			}
			$this->assign('data', $data);
			$this->assign('fields', D('content')->getFields());
			$this->assign('is_pass', D('content')->getCheck());
			$this->assign('level', D('category')->getLevel());
			$this->assign('type', D('content')->getType());

			$tableName = D('content')->getModelTable();
			$this->assign('model_name', $tableName);

			if (!empty($data['id'])) {
				$this->display('Content/content/model/' . $tableName . '_editor');
			} else {
				$this->display('Content/content/model/' . $tableName . '_added');
			}
		}

		// 删除到回收站
		if ($this->_model == 'remove') {
			$data = D(ACTION_NAME)->remove();
			if (is_string($data)) {
				$this->error($data);
			} else {
				$url = U(ACTION_NAME, array('d' => 'manage', 'cid' => I('get.cid')));
				$this->success(L('_success_delete'), $url);
			}
		}
	}

	/**
	 * 幻灯片
	 * @access public
	 * @param
	 * @return void
	 */
	public function banner()
	{
		$this->assign('submenu', 1);

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 广告
	 * @access public
	 * @param
	 * @return void
	 */
	public function ads()
	{
		$this->assign('submenu', 1);

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 评论
	 * @access public
	 * @param
	 * @return void
	 */
	public function comment()
	{
		$this->assign('submenu', 1);
		$this->assign('submenu_button_added', 0);

		parent::listing();
		// parent::added();
		if ($this->_model == 'added') {
			redirect(U('content/comment'));
		}
		parent::editor();
		parent::remove();
	}

	/**
	 * 缓存
	 * @access public
	 * @param
	 * @return void
	 */
	public function cache()
	{
		Vendor('File#class', COMMON_PATH . 'Library');
		if ($this->_model == 'remove') {
			if (I('get.type') == 'compile') {
				\File::delete(CACHE_PATH);
				\File::create(CACHE_PATH);
				\File::create(CACHE_PATH . 'index.html', '');
				$this->success(L('_success_compile'), U('content/cache'));
				exit();
			}
			if (I('get.type') == 'cache') {
				\File::delete(TEMP_PATH);
				\File::create(TEMP_PATH);
				\File::create(TEMP_PATH . 'index.html', '');
				// \File::delete(RUNTIME_PATH . 'common~runtime.php');
				$this->success(L('_success_cache'), U('content/cache'));
				exit();
			}
		}


		$data = \File::get(CACHE_PATH);
		foreach ($data as $key => $value) {
			$count = \File::get(CACHE_PATH . $value['name']);
			$data[$key]['count'] = count($count);
		}
		$this->assign('list', $data);
		$this->display();
	}

	/**
	 * 内容回收站
	 * @access public
	 * @param
	 * @return void
	 */
	public function recycle()
	{
		// 栏目列表
		if ($this->_model == 'list') {
			$data = D('content')->getCategory();
			$this->assign('list', $data['list']);

			$this->display('Content/content/manage');
		}

		// 内容列表
		if ($this->_model == 'manage') {
			C('TOKEN_ON', false);

			$data = D(ACTION_NAME)->getData();
			if (is_string($data)) {
				$this->error($data);
			}

			$this->assign('list', $data['list']);
			$this->assign('page', $data['page']);

			$this->display('Content/content/recycle_list');
		}

		// 查看内容
		if ($this->_model == 'view') {
			if (IS_POST) {
				$return = D(ACTION_NAME)->editor();
				if ($return === true) {
					$url = U(ACTION_NAME, array('d' => 'manage', 'cid' => I('get.cid')));
					$this->success(L('_success_editor'), $url);
					exit();
				} else {
					$this->error($return);
				}
			}
			$data = D(ACTION_NAME)->getDataOne();
			if (is_string($data)) {
				$this->error($data);
			}
			$this->assign('data', $data);
			$this->assign('fields', D('content')->getFields());
			$this->assign('is_pass', D('content')->getCheck());
			$this->assign('level', D('category')->getLevel());
			$this->assign('type', D('content')->getType());

			$tableName = D('content')->getModelTable();
			$this->assign('model_name', $tableName);
			$this->display('Content/content/recycle/' . $tableName);
		}

		// 删除到回收站
		if ($this->_model == 'remove') {
			$data = D(ACTION_NAME)->remove();
			if (is_string($data)) {
				$this->error($data);
			} else {
				$url = U(ACTION_NAME, array('d' => 'manage', 'cid' => I('get.cid')));
				$this->success(L('_success_delete'), $url);
			}
		}
	}
}