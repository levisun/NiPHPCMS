<?php
/**
 *
 * 会员组 - 用户管理 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: LevelModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class LevelModel extends Model
{
	protected $tableName = 'level';
	protected $fields = array('id', 'name', 'integral', 'status', 'remark');
	protected $pk = 'id';
	protected $insertFields = array('name', 'integral', 'status', 'remark');
	protected $updateFields = array('name', 'integral', 'status', 'remark');

	/**
	 * 获得会员等级（组）
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('name' => array('LIKE', '%' . $key . '%'));
		}

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
	 * 新增会员等级（组）
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_levelname')),
			array('name', '', L('error_levelname_unique'), 0, 'unique', 1),
			array('integral', 'require', L('error_levelintegral')),
			array('integral', 'number', L('error_levelintegral')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'name' => I('post.name'),
			'integral' => I('post.integral', 0, C('PRIMARY_FILTER')),
			'status' => I('post.status', 0, 'intval'),
			'remark' => I('post.remark'),
			);

		$id = $this->data($data)->add();

		action('level_add', $id);
		return true;
	}

	/**
	 * 获得要编辑的数据
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
	 * 编辑会员等级（组）
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('name', 'require', L('error_levelname')),
			array('name', '', L('error_levelname_unique'), 0, 'unique', 1),
			array('integral', 'require', L('error_levelintegral')),
			array('integral', 'number', L('error_levelintegral')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$map = array(
			'id' => array('neq', $id),
			'name' => I('post.name'),
			);
		$unique = $this->where($map)->find();
		if (!empty($unique)) {
			return L('error_levelname_unique');
		}

		$data = array(
			'name' => I('post.name'),
			'integral' => I('post.integral', 0, C('PRIMARY_FILTER')),
			'status' => I('post.status', 0, 'intval'),
			'remark' => I('post.remark'),
			);

		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('level_editor', $id);
		return true;
	}

	/**
	 * 删除会员等级（组）
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		if ($error = illegal()) {
			return $error;
		}

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->where($map)->delete();
	}
}