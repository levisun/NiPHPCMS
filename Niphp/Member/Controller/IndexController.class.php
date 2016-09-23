<?php
/**
 *
 * 会员 - 控制器
 *
 * @category   Member\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: IndexController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Member\Controller;
use Home\Controller\CommonController;
class IndexController extends CommonController
{

	public function my()
	{
		$this->display('index_my');
	}
}