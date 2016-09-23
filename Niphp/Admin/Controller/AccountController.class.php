<?php
/**
 *
 * 帐户 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: AccountController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
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
				redirect(U('settings/info'));
			} else {
				$this->error($return);
			}
		}

		$this->display();
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

	/**
	 * 验证码
	 * @access public
	 * @param
	 * @return void
	 */
	public function verify()
	{
		$verify = new \Think\Verify();
		$verify->fontSize = 30;
		$verify->imageW = 255;
		$verify->length = 4;
		$verify->fontttf = '4.ttf';
		$verify->codeSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$verify->entry();
	}
}