<?php
/**
 *
 * 系统设置 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ThemeController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ThemeController extends CommonController
{

	/**
	 * 网站模板
	 * @access public
	 * @param
	 * @return void
	 */
	public function template()
	{
		if ($this->_model == 'update') {
			$return = D('template')->editor('Home');
			if ($return === true) {
				$this->success(L('_success_editor'));
				exit();
			} else {
				$this->error($return);
			}
		}

		$data = D('template')->getTheme('Home');
		$this->assign('type', 'Home');
		$this->assign('config', $data['config']);
		$this->assign('list', $data['list']);
		$this->display('template');
	}

	/**
	 * 会员模板
	 * @access public
	 * @param
	 * @return void
	 */
	public function member()
	{
		if ($this->_model == 'update') {
			$return = D('template')->editor('Member');
			if ($return === true) {
				$this->success(L('_success_editor'));
				exit();
			} else {
				$this->error($return);
			}
		}

		$data = D('template')->getTheme('Member');
		$this->assign('type', 'Member');
		$this->assign('config', $data['config']);
		$this->assign('list', $data['list']);
		$this->display('template');
	}

	/**
	 * 会员模板
	 * @access public
	 * @param
	 * @return void
	 */
	public function shop()
	{
		if ($this->_model == 'update') {
			$return = D('template')->editor('Shop');
			if ($return === true) {
				$this->success(L('_success_editor'));
				exit();
			} else {
				$this->error($return);
			}
		}

		$data = D('template')->getTheme('Shop');
		$this->assign('type', 'Shop');
		$this->assign('config', $data['config']);
		$this->assign('list', $data['list']);
		$this->display('template');
	}
}