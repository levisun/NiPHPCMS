<?php
/**
 *
 * 关键词回复 - 微信公众平台 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: KeywordModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class KeywordModel extends Model
{
	protected $tableName = 'reply';
	protected $fields = array(
						'id',
						'keyword',
						'title',
						'content',
						'type',
						'image',
						'url',
						'status',
						'lang'
						);
	protected $pk = 'id';
	protected $updateFields = array(
						'keyword',
						'title',
						'content',
						'type',
						'image',
						'url',
						'status',
						'lang'
						);
	protected $insertFields = array(
						'keyword',
						'title',
						'content',
						'type',
						'image',
						'url',
						'status',
						'lang'
						);

	/**
	 * 获得关键词回复数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('keyword' => array('LIKE', '%' . $key . '%'));
		}

		$map['type'] = $_GET['type'];
		$map['lang'] = LANG_SET;

		$count = $this->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$data['list'] =
		$this->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('id DESC')
		->select();

		return $data;
	}

	/**
	 * 新增关键词回复
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('keyword', 'require', L('error_keyword')),
			array('keyword', '', L('error_keyword_unique'), 0, 'unique', 1),
			array('title', 'require', L('error_title')),
			array('content', 'require', L('error_reply')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			array('url', 'url', L('error_url'), 2),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'keyword' => I('post.keyword'),
			'title' => I('post.title'),
			'content' => I('post.content'),
			'status' => I('post.status', 0, C('PRIMARY_FILTER')),
			'image' => I('post.image'),
			'url' => I('post.url'),
			'type' => 0,
			'lang' => LANG_SET
			);

		$id = $this->data($data)->add();

		action('wechat_keyword_add', $id);
		return true;
	}

	/**
	 * 获得将要修改的数据
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function getDataOne()
	{
		if ($error = illegal()) {
			return $error;
		}

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->where($map)->find();
	}

	/**
	 * 编辑关键词
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('keyword', 'require', L('error_keyword')),
			array('keyword', '', L('error_keyword_unique'), 0, 'unique', 1),
			array('title', 'require', L('error_title')),
			array('content', 'require', L('error_reply')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			array('url', 'url', L('error_url'), 2),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$map = array(
			'id' => array('neq', $id),
			'keyword' => I('post.keyword'),
			);
		$unique = $this->where($map)->find();
		if (!empty($unique)) {
			return L('error_keyword_unique');
		}

		$data = array(
			'keyword' => I('post.keyword'),
			'title' => I('post.title'),
			'content' => I('post.content'),
			'status' => I('post.status', 0, C('PRIMARY_FILTER')),
			'image' => I('post.image'),
			'url' => I('post.url'),
			'type' => 0,
			'lang' => LANG_SET
			);

		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('wechat_keyword_editor', $id);
		return true;
	}

	/**
	 * 删除关键词
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		if ($error = illegal()) {
			return $error;
		}
		$id = I('get.id', '', C('PRIMARY_FILTER'));

		$map = array('id' => $id);
		$this->where($map)->delete();

		action('wechat_keyword_remove', $id);
		return true;
	}

	/**
	 * 获得自动回复数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getAuto()
	{
		$map = array(
			'id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'type' => 1,
			'lang' => LANG_SET
			);
		$data = $this->where($map)->find();
		if (empty($data)) {
			$data = array(
				'id' => 0,
				'title' => '',
				'content' => '',
				'image' => '',
				'url' => '',
				);
			return $data;
		} else {
			return $data;
		}
	}

	/**
	 * 修改自动回复
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateAuto()
	{
		// 没有数据新增
		if (!I('post.id')) {
			$rules = array(
				array('title', 'require', L('error_title')),
				array('content', 'require', L('error_reply')),
				array('status', 'require', L('error_status')),
				array('status', 'number', L('error_status')),
				array('url', 'url', L('error_url'), 2),
				);
			if ($this->validate($rules)->create() === false) {
				return $this->getError();
			}

			$data = array(
				'keyword' => '_AUTO_',
				'title' => I('post.title'),
				'content' => I('post.content'),
				'status' => I('post.status', 0, C('PRIMARY_FILTER')),
				'image' => I('post.image'),
				'url' => I('post.url'),
				'type' => 1,
				'lang' => LANG_SET
			);

			$id = $this->data($data)->add();

			action('wechat_keyword_auto', $id);
			return true;
		}

		// 编辑
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('keyword', 'require', L('error_keyword')),
			array('keyword', '', L('error_keyword_unique'), 0, 'unique', 1),
			array('title', 'require', L('error_title')),
			array('content', 'require', L('error_reply')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			array('url', 'url', L('error_url'), 2),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$data = array(
			'keyword' => '_AUTO_',
			'title' => I('post.title'),
			'content' => I('post.content'),
			'status' => I('post.status', 0, C('PRIMARY_FILTER')),
			'image' => I('post.image'),
			'url' => I('post.url'),
			'type' => 1,
			'lang' => LANG_SET
			);

		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('wechat_keyword_auto', $id);
		return true;
	}

	/**
	 * 获得关注回复数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getAttention()
	{
		$map = array(
			'id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'type' => 2,
			'lang' => LANG_SET
			);
		$data = $this->where($map)->find();
		if (empty($data)) {
			$data = array(
				'id' => 0,
				'title' => '',
				'content' => '',
				'image' => '',
				'url' => '',
				);
			return $data;
		} else {
			return $data;
		}
	}

	/**
	 * 修改关注回复
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateAttention()
	{
		// 没有数据新增
		if (!I('post.id')) {
			$rules = array(
				array('title', 'require', L('error_title')),
				array('content', 'require', L('error_reply')),
				array('status', 'require', L('error_status')),
				array('status', 'number', L('error_status')),
				array('url', 'url', L('error_url'), 2),
				);
			if ($this->validate($rules)->create() === false) {
				return $this->getError();
			}

			$data = array(
				'keyword' => '_ATTENTION_',
				'title' => I('post.title'),
				'content' => I('post.content'),
				'status' => I('post.status', 0, C('PRIMARY_FILTER')),
				'image' => I('post.image'),
				'url' => I('post.url'),
				'type' => 2,
				'lang' => LANG_SET
			);

			$id = $this->data($data)->add();

			action('wechat_keyword_attention', $id);
			return true;
		}

		// 编辑
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('keyword', 'require', L('error_keyword')),
			array('keyword', '', L('error_keyword_unique'), 0, 'unique', 1),
			array('title', 'require', L('error_title')),
			array('content', 'require', L('error_reply')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			array('url', 'url', L('error_url'), 2),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$data = array(
			'keyword' => '_ATTENTION_',
			'title' => I('post.title'),
			'content' => I('post.content'),
			'status' => I('post.status', 0, C('PRIMARY_FILTER')),
			'image' => I('post.image'),
			'url' => I('post.url'),
			'type' => 2,
			'lang' => LANG_SET
			);

		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('wechat_keyword_attention', $id);
		return true;
	}
}