<?php
/**
 *
 * 系统设置 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: UserController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class UserController extends CommonController
{

	/**
	 * 会员
	 * @access public
	 * @param
	 * @return void
	 */
	public function member()
	{
		if (IS_AJAX) {
			// 获得地区
			$data = D(ACTION_NAME)->region(I('post.id', '', C('PRIMARY_FILTER')));
			$option = '';
			foreach ($data as $key => $value) {
				$option .= '<option class="op" value="' . $value['id'] . '">' . $value['name'] . '</option>';
			}
			exit($option);
		}

		$this->assign('submenu', 1);
		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('level', D(ACTION_NAME)->getLevel());
			$this->assign('province', D(ACTION_NAME)->region(1));
		}

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
			$this->assign('city', D(ACTION_NAME)->region($data['province']));
			$this->assign('area', D(ACTION_NAME)->region($data['city']));
			$this->display(ACTION_NAME . '_' . $this->_model);
		}

		parent::listing();
		parent::added();
		parent::remove();
	}

	/**
	 * 会员等级（组）
	 * @access public
	 * @param
	 * @return void
	 */
	public function level()
	{
		$this->assign('submenu', 1);

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 管理员
	 * @access public
	 * @param
	 * @return void
	 */
	public function user()
	{
		$this->assign('submenu', 1);
		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('role', D(ACTION_NAME)->getRole());
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 管理员组
	 * @access public
	 * @param
	 * @return void
	 */
	public function role()
	{
		$this->assign('submenu', 1);
		if ($this->_model == 'added' || $this->_model == 'editor') {
			$this->assign('node', D(ACTION_NAME)->getNode());
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 系统节点管理
	 * @access public
	 * @param
	 * @return void
	 */
	public function node()
	{
		$this->assign('submenu', 1);

		if ($this->_model == 'added' || $this->_model == 'editor') {
			$data = D(ACTION_NAME)->getData();
			$this->assign('node', $data['list']);
		}

		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}
}