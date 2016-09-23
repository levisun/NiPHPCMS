<?php
/**
 *
 * 扩展 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ExpandController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ExpandController extends CommonController
{

	/**
	 * 系统日志
	 * @access public
	 * @param
	 * @return void
	 */
	public function log()
	{
		$data = D('log')->getLog();
		$this->assign('list', $data['list']);
		$this->assign('page', $data['page']);
		$this->display();
	}

	/**
	 * 数据备份
	 * @access public
	 * @param
	 * @return void
	 */
	public function databack()
	{
		if ($this->_model == 'back') {
			D('databack')->back();
			$this->success(L('back_success'));
			exit();
		}

		if ($this->_model == 'down') {
			D('databack')->down();
		}

		if ($this->_model == 'remove') {
			D('databack')->remove();
			$this->success(L('_success_delete'));
			exit();
		}

		if ($this->_model == 'reduction') {
			D('databack')->reduction();
			$this->success(L('_success_reduction'));
			exit();
		}
		//
		$data = D('databack')->getDataback();
		$this->assign('list', $data['list']);
		$this->assign('page', $data['page']);
		$this->display();
	}

	/**
	 * 错误日志
	 * @access public
	 * @param
	 * @return void
	 */
	public function elog()
	{
		if (I('get.d') == 'show') {
			$this->assign('data', D('elog')->getShow());
			$this->display('Expand/elog_show');
		} else {
			$this->assign('list', D('elog')->getData());
			$this->display();
		}
	}

	/**
	 * 访问统计
	 * @access public
	 * @param
	 * @return void
	 */
	public function visit()
	{
		$data = D('visit')->getData();
		$this->assign('list', $data['list']);
		$this->assign('page', $data['page']);

		if (I('get.d')) {
			$this->display('Expand/searchengine');
		} else {
			$this->display();
		}

	}
}