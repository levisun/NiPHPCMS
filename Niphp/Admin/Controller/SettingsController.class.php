<?php
/**
 *
 * 系统设置 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: SettingsController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class SettingsController extends CommonController
{

	/**
	 * 系统信息
	 * @access public
	 * @param
	 * @return void
	 */
	public function info()
	{
		$this->assign('sys_info', D('info')->getSysInfo());
		$this->display();
	}

	/**
	 * 基本设置
	 * @access public
	 * @param
	 * @return void
	 */
	public function basic()
	{
		if (IS_POST) {
			$return = D('basic')->updateConfig();
			if ($return === true) {
				$this->success(L('_success_save'));
				exit();
			} else {
				$this->error($return);
			}
		}
		$this->assign('data', D('basic')->getConfig());
		$this->display();
	}

	/**
	 * 语言设置
	 * @access public
	 * @param
	 * @return void
	 */
	public function lang()
	{
		if (IS_POST) {
			$return = D('lang')->updateLang();
			if ($return === true) {
				$this->success(L('_success_save'));
				exit();
			} else {
				$this->error($return);
			}
		}
		$data = D('lang')->getLang();
		$this->assign('lang_list', $data['LANG_LIST']);
		$this->assign('sys_default_lang', $data['SYS_DEFAULT_LANG']);
		$this->assign('web_default_lang', $data['WEB_DEFAULT_LANG']);
		$this->assign('lang_auto_detect', $data['LANG_AUTO_DETECT']);
		$this->display();
	}

	/**
	 * 图片设置
	 * @access public
	 * @param
	 * @return void
	 */
	public function image()
	{
		if (IS_POST) {
			$return = D('image')->updateImage();
			if ($return === true) {
				$this->success(L('_success_save'));
				exit();
			} else {
				$this->error($return);
			}
		}
		$this->assign('data', D('image')->getImage());
		$this->display();
	}

	/**
	 * 安全与效率
	 * @access public
	 * @param
	 * @return void
	 */
	public function safe()
	{
		if (IS_POST) {
			$return = D('safe')->updateSafe();
			if ($return === true) {
				$this->success(L('_success_save'));
				exit();
			} else {
				$this->error($return);
			}
		}
		$this->assign('data', D('safe')->getSafe());
		$this->display();
	}

	/**
	 * 邮箱设置
	 * @access public
	 * @param
	 * @return void
	 */
	public function email()
	{
		if (IS_POST) {
			$return = D('email')->updateEmail();
			if ($return === true) {
				$this->success(L('_success_save'));
				exit();
			} else {
				$this->error($return);
			}
		}

		$this->assign('data', D('email')->getEmail());
		$this->display();
	}
}