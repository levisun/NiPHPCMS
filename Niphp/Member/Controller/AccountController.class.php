<?php
/**
 *
 * 会员帐户 - 控制器
 *
 * @category   Member\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: AccountController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Member\Controller;
use Home\Controller\CommonController;
class AccountController extends CommonController
{
	/**
	 * 登录
	 * @access public
	 * @param
	 * @return void
	 */
	public function login()
	{
		if (IS_POST) {
			$return = D('account')->checkLogin();
			if ($return === true) {
				redirect(U('index/my'));
			} else {
				$this->error($return);
			}
		}

		$this->display('account_login');
	}

	/**
	 * 注册
	 * @access public
	 * @param
	 * @return void
	 */
	public function reg()
	{
		if (IS_POST) {
			$return = D('account')->reg();
			if ($return === true) {
				redirect(U('index/my'));
			} else {
				$this->error($return);
			}
		}

		$this->display('account_reg');
	}

	/**
	 * 注销
	 * @access public
	 * @param
	 * @return void
	 */
	public function logout()
	{
		D('account')->logout();
	}
}