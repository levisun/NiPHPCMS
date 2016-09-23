<?php
/**
 *
 * 评论 - 控制器
 *
 * @category   Comment\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: IndexController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Comment\Controller;
use Home\Controller\CommonController;
class IndexController extends CommonController
{

	/**
	 * 列表页
	 * @access public
	 * @param
	 * @return void
	 */
	public function entry()
	{
		$this->illegal(1);

		C('TMPL_PARSE_STRING.__COMMENT_ADDURL__',
			U('comment/index/added', array('cid' => I('get.cid'), 'id' => I('get.id')))
		);

		$data = D('comment')->getList($this->category['model_name']);
		if (is_string($data)) {
			$this->error($data, U('home/index/index'));
		}
		$this->assign('article', $data['article']);
		$this->assign('list', $data['list']);
		$this->assign('page', $data['page']);

		$this->display('comment_list');
	}

	/**
	 * 支持
	 * @access public
	 * @param
	 * @return void
	 */
	public function support()
	{
		$this->illegal(3);
		D('comment')->support();
	}

	/**
	 * 举报
	 * @access public
	 * @param
	 * @return void
	 */
	public function report()
	{
		$this->illegal(3);
		D('comment')->report();
	}

	/**
	 * 回复
	 * @access public
	 * @param
	 * @return void
	 */
	public function reply()
	{
		$this->illegal(3);
		D('comment')->reply();
	}

	/**
	 * 添加评论
	 * @access public
	 * @param
	 * @return void
	 */
	public function added()
	{
		$this->illegal(1);

		$return = D('comment')->added($this->category['model_name']);
		if ($return === true) {
			$this->success(L('success_save'));
			exit();
		} else {
			$this->error($return);
		}
	}
}