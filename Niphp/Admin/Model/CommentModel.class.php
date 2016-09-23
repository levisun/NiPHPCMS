<?php
/**
 *
 * 评论 - 内容 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: CommentModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class CommentModel extends Model
{
	protected $tableName = 'comment';
	protected $fields = array(
						'id',
						'category_id',
						'content_id',
						'user_id',
						'pid',
						'content',
						'is_pass',
						'is_report',
						'support',
						'addtime',
						'lang'
						);
	protected $pk = 'id';
	protected $updateFields = array(
						'is_pass',
						'is_report'
						);

	/**
	 * 获得评论
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('c.keyword' => array('LIKE', '%' . $key . '%'));
		}

		// 未审
		if (!empty($_GET['pass'])) {
			$map['c.is_pass'] = 0;
		}

		// 举报
		if (!empty($_GET['report'])) {
			$map['c.is_report'] = 1;
		}

		$map['c.lang'] = LANG_SET;

		$count = $this->table($this->tablePrefix . $this->tableName . ' AS c')
		->where($map)
		->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field = array(
			'c.id',
			'c.category_id',
			'c.content_id',
			'c.user_id',
			'c.pid',
			'c.content',
			'c.is_pass',
			'c.is_report',
			'c.support',
			'c.addtime',
			'c.lang',
			'm.username'
			);
		$join = array(
			'LEFT JOIN __MEMBER__ AS m ON m.id=c.user_id',
			);

		$data['list'] = $this->table($this->tablePrefix . $this->tableName . ' AS c')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('c.id DESC')
		->select();
		return $data;
	}

	/**
	 * 获得要审核数据
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function getDataOne()
	{
		if ($error = illegal()) {
			return $error;
		}

		$map = array('c.id' => I('get.id', 0, C('PRIMARY_FILTER')));

		$field = array(
			'c.id',
			'c.category_id',
			'c.content_id',
			'c.user_id',
			'c.pid',
			'c.content',
			'c.is_pass',
			'c.is_report',
			'c.support',
			'c.ip',
			'c.ipattr',
			'c.addtime',
			'c.lang',
			'm.username'
			);
		$join = array(
			'LEFT JOIN __MEMBER__ AS m ON m.id=c.user_id',
			);
		$data = $this->table($this->tablePrefix . $this->tableName . ' AS c')
		->field($field)
		->join($join)
		->where($map)
		->find();
		return $data;
	}

	/**
	 * 编辑评论
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('is_report', 'require', L('error_pass_report')),
			array('is_report', 'number', L('error_pass_report')),
			array('is_pass', 'require', L('error_pass_report')),
			array('is_pass', 'number', L('error_pass_report')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));

		$map = array('id' => $id);
		$data = array(
			'is_report' => I('post.is_report', 0, 'intval'),
			'is_pass' => I('post.is_pass', 0, 'intval')
			);
		$this->where($map)->data($data)->save();

		action('comment_editor', $id);
		return true;
	}

	/**
	 * 删除评论
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

		$map = array('pid' => $id);
		$this->where($map)->delete();

		action('comment_remove', $id);
		return true;
	}
}