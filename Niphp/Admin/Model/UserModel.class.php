<?php
/**
 *
 * 管理员 - 用户管理 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: UserModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class UserModel extends Model
{
	protected $tableName = 'admin';
	protected $fields = array(
						'id',
						'username',
						'password',
						'email',
						'lastloginip',
						'lastloginipattr',
						'lastlogintime',
						'addtime'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'username',
						'password',
						'not_password',
						'email',
						'role',
						'addtime'
						);
	protected $updateFields = array(
						'username',
						'password',
						'not_password',
						'email',
						'role'
						);

	/**
	 * 获得管理员数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('a.username' => array('LIKE', '%' . $key . '%'));
		}
		$map['a.id'] = array('neq', 1);

		$count = $this->table($this->tablePrefix . $this->tableName . ' AS a')
		->where($map)
		->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field = array(
			'a.id',
			'a.username',
			'a.lastloginip',
			'a.lastloginipattr',
			'a.lastlogintime',
			'r.name'
			);
		$join = array(
			'__ROLE_ADMIN__ AS ra ON ra.user_id=a.id',
			'__ROLE__ AS r ON r.id=ra.role_id'
			);

		$data['list'] =
		$this->table($this->tablePrefix . $this->tableName . ' AS a')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('a.id DESC')
		->select();

		return $data;
	}

	/**
	 * 新增管理员
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('username', 'require', L('error_username')),
			array('username', '', L('error_username_unique'), 0, 'unique', 1),
			array('password', 'require', L('error_password')),
			array('password', 'not_password', L('error_not_password'), 0, 'confirm'),
			array('password', '6,20', L('error_password_length'), 0, 'length'),
			array('role', 'require', L('error_userrole')),
			array('email', 'email', L('error_email'), 2)
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'username' => I('post.username'),
			'password' => I('post.password', '', 'md5,trim'),
			'email' => I('post.email'),
			'addtime' => time()
			);
		$id = $this->data($data)->add();

		$data = array(
			'role_id' => I('post.role', '', C('PRIMARY_FILTER')),
			'user_id' => $id,
			);
		$field = array('role_id', 'user_id');
		$this->table($this->tablePrefix . 'role_admin')
		->field($field)
		->data($data)
		->add();

		action('user_add', $id);
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

		$field = array('a.id', 'a.username', 'a.email', 'r.id' => 'role_id');
		$join = array(
			'__ROLE_ADMIN__ AS ra ON ra.user_id=a.id',
			'__ROLE__ AS r ON r.id=ra.role_id'
			);
		$map = array(
			'a.id' => array('neq', 1),
			'a.id' => I('get.id', 0, C('PRIMARY_FILTER'))
			);
		return $this->table($this->tablePrefix . $this->tableName . ' AS a')
		->field($field)
		->join($join)
		->where($map)
		->find();
	}

	/**
	 * 编辑管理员
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('username', 'require', L('error_username')),
			array('username', '', L('error_username_unique'), 0, 'unique', 1),
			array('password', 'require', L('error_password'), 2),
			array('password', 'not_password', L('error_not_password'), 2, 'confirm'),
			array('password', '6,20', L('error_password_length'), 2, 'length'),
			array('role', 'require', L('error_userrole')),
			array('email', 'email', L('error_email'), 2)
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$map = array(
			'id' => array('neq', $id),
			'username' => I('post.username'),
			);
		$unique = $this->where($map)->find();
		if (!empty($unique)) {
			return L('error_username_unique');
		}

		$data = array(
			'username' => I('post.username'),
			'email' => I('post.email')
			);
		if (I('post.password')) {
			$data['password'] = I('post.password', '', 'md5,trim');
		}
		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		$data = array(
			'role_id' => I('post.role', '', C('PRIMARY_FILTER')),
			);
		$field = array('role_id', 'user_id');
		$map = array('user_id' => $id);
		$this->table($this->tablePrefix . 'role_admin')
		->field($field)
		->where($map)
		->data($data)
		->save();

		action('user_editor', $id);
		return true;
	}


	/**
	 * 删除管理员
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

		$map = array('user_id' => $id);
		$this->table($this->tablePrefix . 'role_admin')->where($map)->delete();

		action('user_remove', $id);
		return true;
	}

	/**
	 * 获得管理员组数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getRole()
	{
		$map = array('status' => 1, 'id' => array('neq', 1));
		return $this->table($this->tablePrefix . 'role')->where($map)->select();
	}
}