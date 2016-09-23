<?php
/**
 *
 * 节点 - 用户管理 - 模型
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
class NodeModel extends Model
{
	protected $tableName = 'node';
	protected $fields = array(
						'id',
						'name',
						'title',
						'status',
						'remark',
						'sort',
						'pid',
						'level'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'name',
						'title',
						'status',
						'remark',
						'sort',
						'pid',
						'level'
						);
	protected $updateFields = array(
						'name',
						'title',
						'status',
						'remark',
						'sort',
						'pid',
						'level'
						);

	/**
	 * 获得权限节点
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$data['page'] = '';
		$data['list'] = $this->getNode();
		foreach ($data['list'] as $key => $value) {
			$ext = '';
			for ($i=1; $i < $value['level']; $i++) {
				$ext .= '|__';
			}
			$data['list'][$key]['title'] = $ext . $value['title'];
		}
		return $data;
	}

	/**
	 * 获得权限节点
	 * @access public
	 * @param
	 * @return array
	 */
	public function getNode($pid_=0)
	{
		if ($key = I('get.key')) {
			$map = array('title' => array('LIKE', '%' . $key . '%'));
			return $this->field($this->fields)->where($map)->order('sort ASC')->select();
		}

		$map = array('pid' => $pid_);

		$data = $this->field($this->fields)->where($map)->order('sort ASC')->select();
		$node = array();
		$array = array();
		foreach ($data as $key => $val) {
			$node[] = $val;
			$array = $this->getNode($val['id']);
			if (!empty($array)) {
				$node = array_merge_recursive($node, $array);
			}
		}
		return $node;
	}

	/**
	 * 排序
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function upSort()
	{
		if (empty($_POST['sort'])) {
			return L('illegal_operation');
		}
		foreach ($_POST['sort'] as $key => $value) {
			$map = array('id' => floor(floatval($key)));
			$data = array('sort' => floor(floatval($value)));
			$this->where($map)->data($data)->save();
		}

		action('node_sort');
		return true;
	}

	/**
	 * 新增节点
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_name')),
			array('name', '', L('error_name_unique'), 0, 'unique', 1),
			array('name', 'english', L('error_name_abc'), 0),
			array('level', 'require', L('error_nodelevel')),
			array('level', 'number', L('error_nodelevel')),
			array('pid', 'require', L('error_pid')),
			array('pid', 'number', L('error_pid')),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}
		$data = array(
			'title' => I('post.title'),
			'name' => I('post.name'),
			'pid' => I('post.pid', 0, C('PRIMARY_FILTER')),
			'level' => I('post.level', 1, 'intval'),
			'status' => I('post.status', 0, 'intval'),
			'remark' => I('post.remark'),
			);
		$id = $this->data($data)->add();

		action('node_add', $id);
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

		return $this->field($this->fields)->where($map)->find();
	}

	/**
	 * 新增节点
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('name', 'require', L('error_name')),
			array('name', '', L('error_name_unique'), 0, 'unique', 1),
			array('name', 'english', L('error_name_abc'), 0),
			array('level', 'require', L('error_nodelevel')),
			array('level', 'number', L('error_nodelevel')),
			array('pid', 'require', L('error_pid')),
			array('pid', 'number', L('error_pid')),
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
			return L('error_name_unique');
		}

		$map = array('id' => $id);
		$data = array(
			'title' => I('post.title'),
			'name' => I('post.name'),
			'pid' => I('post.pid', 0, C('PRIMARY_FILTER')),
			'level' => I('post.level', 1, 'intval'),
			'status' => I('post.status', 0, 'intval'),
			'remark' => I('post.remark'),
			);
		$this->where($map)->data($data)->save();

		action('node_editor', $id);
		return true;
	}

	public function remove()
	{
		if ($error = illegal()) {
			return $error;
		}
		$id = I('get.id', '', C('PRIMARY_FILTER'));

		$map = array('id' => $id);
		$this->where($map)->delete();

		$map = array('node_id' => $id);
		$this->table($this->tablePrefix . 'access')
		->where($map)->delete();

		action('node_remove', $id);
		return true;
	}
}