<?php
/**
 *
 * 网站 - 控制器
 *
 * @category   Home\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: IndexController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController
{

	/**
	 * 首页
	 * @access public
	 * @param
	 * @return void
	 */
	public function index()
	{
		$this->display('index_index');
	}

	/**
	 * 列表页
	 * @access public
	 * @param
	 * @return void
	 */
	public function entry()
	{
		$this->illegal(1);

		$this->ToTKD($this->category);
		switch ($this->category['model_name']) {
			// 外部模型
			case 'external':
				redirect($this->category['url']);
				break;

			// 留言模型
			case 'message':
				$url = U('entry', array(
					'd' => 'list',
					'cid' => I('get.cid', 0, C('PRIMARY_FILTER'))
					));
				$this->assign('list_url', $url);

				$url = U('entry', array('cid' => I('get.cid', 0, C('PRIMARY_FILTER'))));
				$this->assign('mess_url', $url);

				if (I('get.d')) {
					$data = D($this->category['model_name'])->getList();
					$this->assign('list', $data['list']);
					$this->assign('page', $data['page']);
					$this->display('list_' . $this->category['model_name'] . '_list');
					exit();
				}

			// 反馈模型
			case 'feedback':
				if (IS_POST) {
					$return = D($this->category['model_name'])->added();
					if ($return === true) {
						$url = U('entry', array('cid' => I('get.cid', 0, C('PRIMARY_FILTER'))));
						$this->success(L('_success_added'), $url);
						exit();
					} else {
						$this->error($return);
					}
				}
				$this->assign('fields', D($this->category['model_name'])->getFields());
				$this->assign('type', D($this->category['model_name'])->getType());
				break;

			// 友链模型
			case 'link':
				$data = D('link')->getLink();
				$this->assign('list', $data);
				break;

			// 单页模型
			case 'page':
				if (IS_AJAX) {
					D('page')->hits();
					exit();
				}
				$data = D('page')->getPage();
				$this->ToTKD($this->category, $data);
				$this->assign('data', $data);
				break;

			// 文章、图片、产品模型
			default:
				$data = D('list')->getList($this->category['model_name']);
				$this->assign('list', $data['list']);
				$this->assign('page', $data['page']);
				break;
		}

		$this->display('list_' . $this->category['model_name']);
	}

	/**
	 * 内容页
	 * @access public
	 * @param
	 * @return void
	 */
	public function article()
	{
		$this->illegal(2);

		if (IS_AJAX) {
			D('article')->hits($this->category['model_name']);
			exit();
		}

		C('TMPL_PARSE_STRING.__COMMENT_ADDURL__',
			U('comment/index/added', array('cid' => I('get.cid'), 'id' => I('get.id')))
		);

		$data = D('article')->getArticle($this->category['model_name']);
		$this->ToTKD($this->category, $data);
		$this->assign('data', $data);
		$this->display('content_' . $this->category['model_name']);
	}

	/**
	 * 搜索页
	 * @access public
	 * @param
	 * @return void
	 */
	public function search()
	{
		$data = D('search')->getList();
		$this->assign('list', $data['list']);
		$this->assign('page', $data['page']);
		$this->display('list_tags');
	}

	/**
	 * 标签
	 * @access public
	 * @param
	 * @return void
	 */
	public function tags()
	{
		$this->illegal(3);
		$data = D('tags')->getList();
		$this->assign('list', $data['list']);
		$this->assign('page', $data['page']);
		$this->display('list_tags');
	}

	/**
	 * 广告
	 * @access public
	 * @param
	 * @return void
	 */
	public function ads()
	{
		$this->illegal(3);
		D('ads')->toRedirect();
	}

	/**
	 * 幻灯片
	 * @access public
	 * @param
	 * @return void
	 */
	public function banner()
	{
		$this->illegal(3);
		D('banner')->toRedirect();
	}

	/**
	 * 友情链接
	 * @access public
	 * @param
	 * @return void
	 */
	public function link()
	{
		$this->illegal(3);
		D('link')->toRedirect();
	}
}