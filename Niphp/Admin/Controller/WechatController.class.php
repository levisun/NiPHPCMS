<?php
/**
 *
 * 微信公众平台 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: WechatController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class WechatController extends CommonController
{

	/**
	 * 关键词回复
	 * @access public
	 * @param
	 * @return void
	 */
	public function keyword()
	{
		$this->assign('submenu', 1);

		$_GET['type'] = 0;
		parent::listing();
		parent::added();
		parent::editor();
		parent::remove();
	}

	/**
	 * 自动回复
	 * @access public
	 * @param
	 * @return void
	 */
	public function auto()
	{
		$this->assign('submenu', 1);
		$_GET['type'] = 1;

		if ($this->_model == 'list') {
			$data = D('keyword')->getData();
			$this->assign('list', $data['list']);
			$this->assign('page', $data['page']);
			$this->display();
		}

		if ($this->_model == 'added') {
			if (IS_POST) {
				$return = D('keyword')->updateAuto();
				if ($return === true) {
					$this->success(L('_success_added'), U(ACTION_NAME));
					exit();
				} else {
					$this->error($return);
				}
			}
			$this->display(ACTION_NAME . '_' . $this->_model);
		}

		if ($this->_model == 'editor') {
			if (IS_POST) {
				$return = D('keyword')->updateAuto();
				if ($return === true) {
					$this->success(L('_success_editor'));
					exit();
				} else {
					$this->error($return);
				}
			}
			$data = D('keyword')->getAuto();
			if (is_string($data)) {
				$this->error($data);
			}
			$this->assign('data', $data);
			$this->display(ACTION_NAME . '_' . $this->_model);
		}

		if ($this->_model == 'remove') {
			$data = D('keyword')->remove();
			if (is_string($data)) {
				$this->error($data);
			} else {
				$this->success(L('_success_delete'), U(ACTION_NAME));
			}
		}
	}

	/**
	 * 关注回复
	 * @access public
	 * @param
	 * @return void
	 */
	public function attention()
	{
		$this->assign('submenu', 1);
		$_GET['type'] = 2;

		if ($this->_model == 'list') {
			$data = D('keyword')->getData();
			$this->assign('list', $data['list']);
			$this->assign('page', $data['page']);
			$this->display('auto');
		}

		if ($this->_model == 'added') {
			if (IS_POST) {
				$return = D('keyword')->updateAttention();
				if ($return === true) {
					$this->success(L('_success_added'), U(ACTION_NAME));
					exit();
				} else {
					$this->error($return);
				}
			}
			$this->display('auto_' . $this->_model);
		}

		if ($this->_model == 'editor') {
			if (IS_POST) {
				$return = D('keyword')->updateAttention();
				if ($return === true) {
					$this->success(L('_success_editor'));
					exit();
				} else {
					$this->error($return);
				}
			}
			$data = D('keyword')->getAttention();
			if (is_string($data)) {
				$this->error($data);
			}
			$this->assign('data', $data);
			$this->display('auto_' . $this->_model);
		}

		if ($this->_model == 'remove') {
			$data = D('keyword')->remove();
			if (is_string($data)) {
				$this->error($data);
			} else {
				$this->success(L('_success_delete'), U(ACTION_NAME));
			}
		}
	}

	/**
	 * 接口配置
	 * @access public
	 * @param
	 * @return void
	 */
	public function config()
	{
		if (IS_POST) {
			$return = D(ACTION_NAME)->updateConfig();
			if ($return === true) {
				$this->success(L('_success_save'));
				exit();
			} else {
				$this->error($return);
			}
		}
		$this->assign('data', D(ACTION_NAME)->getConfig());
		$this->display();
	}

	public function menu()
	{
		# code...
	}
}