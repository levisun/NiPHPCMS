<?php
/**
 *
 * 微信 - 控制器
 *
 * @category   Wechat\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: IndexController.class.php 2016-06 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 0.1
 */
namespace Wechat\Controller;
use Think\Controller;
class IndexController extends Controller
{

	public function index()
	{
		redirect(U('home/index/index'));
	}
}