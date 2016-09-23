<?php
/**
 *
 * 管理组 - 用户管理 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: RoleModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model
{
	protected $tableName = 'role';
	protected $fields = array('id', 'name', 'pid', 'status', 'remark');
	protected $pk = 'id';
	protected $insertFields = array('name', 'pid', 'status', 'remark');
	protected $updateFields = array('name', 'pid', 'status', 'remark');

	/**
	 * 获得管理员数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array('id' => array('neq', 1));
		if ($key = I('get.key')) {
			$map['name'] = array('LIKE', '%' . $key . '%');
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
	 * 新增管理组
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_rolename')),
			array('name', '', L('error_rolename_unique'), 0, 'unique', 1),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}
		$data = array(
			'name' => I('post.name'),
			'status' => I('post.status', 0, 'intval'),
			'remark' => I('post.remark'),
			);
		$id = $this->data($data)->add();

		$map = array('role_id' => $id);
		$this->table($this->tablePrefix . 'access')->where($map)->delete();

		$field = array('role_id', 'node_id', 'status', 'level', 'module');
		foreach ($_POST['node'] as $key => $value) {
			foreach ($value as $k => $val) {
				$k = explode('_', $k);
				$k = !empty($k[1]) ? $k[1] : $k[0];
				$data = array(
					'role_id' => $id,
					'node_id' => $val,
					'status' => 1,
					'level' => $key,
					'module' => $k,
					);
				$this->table($this->tablePrefix . 'access')
				->field($field)
				->data($data)
				->add();
			}
		}

		action('role_add', $id);
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
		$data = $this->where($map)->find();

		$map = array('role_id' => I('get.id', 0, C('PRIMARY_FILTER')));
		$node = $this->table($this->tablePrefix . 'access')->where($map)->select();
		foreach ($node as $key => $value) {
			$data['node'][] = $value['node_id'];
		}
		return $data;
	}

	/**
	 * 编辑管理组
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('name', 'require', L('error_rolename')),
			array('name', '', L('error_rolename_unique'), 0, 'unique', 1),
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
			return L('error_rolename_unique');
		}

		$data = array(
			'name' => I('post.name'),
			'status' => I('post.status', 0, 'intval'),
			'remark' => I('post.remark'),
			);
		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		$map = array('role_id' => $id);
		$this->table($this->tablePrefix . 'access')->where($map)->delete();

		$field = array('role_id', 'node_id', 'status', 'level', 'module');
		foreach ($_POST['node'] as $key => $value) {
			foreach ($value as $k => $val) {
				$k = explode('_', $k);
				$k = !empty($k[1]) ? $k[1] : $k[0];
				$data = array(
					'role_id' => $id,
					'node_id' => $val,
					'status' => 1,
					'level' => $key,
					'module' => $k,
					);
				$this->table($this->tablePrefix . 'access')->field($field)->data($data)->add();
			}
		}

		action('role_editor', $id);
		return true;
	}

	/**
	 * 删除管理组
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

		$map = array('role_id' => $id);
		$this->table($this->tablePrefix . 'access')->where($map)->delete();

		action('role_remove', $id);
		return true;
	}

	/**
	 * 获得权限节点
	 * @access public
	 * @param
	 * @return array
	 */
	public function getNode($pid_=0)
	{
		$map = array('status' => 1, 'pid' => $pid_);
		if (!$pid_) {
			$map['id'] = 1;
		}
		$data = $this->table($this->tablePrefix . 'node')->where($map)->order('sort ASC')->select();
		$node = array();
		foreach ($data as $key => $val) {
			$node = $this->getNode($val['id']);
			if (!empty($node)) {
				$data[$key]['child'] = $node;
			}
		}
		return $data;
	}
}