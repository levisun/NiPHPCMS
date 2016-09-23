<?php
/**
 *
 * 会员 - 用户管理 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: MemberModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class MemberModel extends Model
{
	protected $tableName = 'member';
	protected $fields = array(
						'id',
						'username',
						'password',
						'email',
						'realname',
						'nickname',
						'portrait',
						'gender',
						'birthday',
						'province',
						'city',
						'area',
						'address',
						'phone',
						'status',
						'lastloginip',
						'lastloginipattr',
						'lastlogintime',
						'regtime'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'username',
						'password',
						'not_password',
						'email',
						'realname',
						'nickname',
						'portrait',
						'gender',
						'birthday',
						'province',
						'city',
						'area',
						'address',
						'phone',
						'status',
						'regtime',
						'level'
						);
	protected $updateFields = array(
						'username',
						'password',
						'not_password',
						'email',
						'realname',
						'nickname',
						'portrait',
						'gender',
						'birthday',
						'province',
						'city',
						'area',
						'address',
						'phone',
						'status',
						'level'
						);

	/**
	 * 获得会员数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('m.name' => array('LIKE', '%' . $key . '%'));
		}

		$count = $this->table($this->tablePrefix . $this->tableName . ' AS m')->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field = array(
				'm.id',
				'm.username',
				'm.realname',
				'm.nickname',
				'm.email',
				'm.phone',
				'm.status',
				'm.phone',
				'l.name' => 'levelname'
				);
		$join = array(
			'__LEVEL_MEMBER__ AS lm ON lm.user_id=m.id',
			'__LEVEL__ AS l ON l.id=lm.level_id'
			);
		$data['list'] =
		$this->table($this->tablePrefix . $this->tableName . ' AS m')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('id DESC')
		->select();

		return $data;
	}

	/**
	 * 新增会员
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('username', 'require', L('error_membername')),
			array('username', '', L('error_membername_unique'), 0, 'unique', 1),
			array('password', 'require', L('error_password')),
			array('password', 'not_password', L('error_not_password'), 0, 'confirm'),
			array('password', '6,20', L('error_password_length'), 0, 'length'),
			array('level', 'require', L('error_level')),
			array('email', 'email', L('error_email'), 2)
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'username' => I('post.username'),
			'password' => I('post.password', '', 'md5,trim'),
			'email' => I('post.email'),
			'realname' => I('post.realname'),
			'nickname' => I('post.nickname'),
			'portrait' => I('post.portrait'),
			'gender' => I('post.gender', 1, 'intval'),
			'birthday' => I('post.birthday', 0, 'strtotime'),
			'province' => I('post.province', 0, C('PRIMARY_FILTER')),
			'city' => I('post.city', 0, C('PRIMARY_FILTER')),
			'area' => I('post.area', 0, C('PRIMARY_FILTER')),
			'address' => I('post.address'),
			'phone' => I('post.phone'),
			'status' => I('post.status', 0, 'intval'),
			'regtime' => time(),
			);

		$id = $this->data($data)->add();

		$data = array(
			'user_id' => $id,
			'level_id' => I('post.level', 0, 'intval')
			);
		$field = array('user_id', 'level_id');
		$this->table($this->tablePrefix . 'level_member')->field($field)->data($data)->add();

		action('member_add', $id);
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

		$field = array(
					'm.id',
					'm.username',
					'm.email',
					'm.realname',
					'm.nickname',
					'm.portrait',
					'm.gender',
					'm.birthday',
					'm.province',
					'm.city',
					'm.area',
					'm.address',
					'm.phone',
					'm.status',
					'm.lastloginip',
					'm.lastloginipattr',
					'm.lastlogintime',
					'm.regtime',
					'l.id' => 'level_id',
					'l.name' => 'levelname'
					);
		$join = array(
			'__LEVEL_MEMBER__ AS lm ON lm.user_id=m.id',
			'__LEVEL__ AS l ON l.id=lm.level_id'
			);
		$map = array('m.id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->table($this->tablePrefix . $this->tableName . ' AS m')
		->field($field)
		->join($join)
		->where($map)
		->find();
	}

	/**
	 * 编辑会员
	 * @access public
	 * @param
	 * @return mxied
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('username', 'require', L('error_membername')),
			array('username', '', L('error_membername_unique'), 0, 'unique', 1),
			array('password', 'require', L('error_password'), 2),
			array('password', 'not_password', L('error_not_password'), 2, 'confirm'),
			array('password', '6,20', L('error_password_length'), 2, 'length'),
			array('level', 'require', L('error_level')),
			array('email', 'email', L('error_email'), 2)
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'username' => I('post.username'),
			'email' => I('post.email'),
			'realname' => I('post.realname'),
			'nickname' => I('post.nickname'),
			'portrait' => I('post.portrait'),
			'gender' => I('post.gender', 1, 'intval'),
			'birthday' => I('post.birthday', 0, 'strtotime'),
			'province' => I('post.province', 0, C('PRIMARY_FILTER')),
			'city' => I('post.city', 0, C('PRIMARY_FILTER')),
			'area' => I('post.area', 0, C('PRIMARY_FILTER')),
			'address' => I('post.address'),
			'phone' => I('post.phone'),
			'status' => I('post.status', 0, 'intval'),
			);

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$map = array(
			'id' => array('neq', $id),
			'username' => I('post.username'),
			);
		$unique = $this->where($map)->find();
		if (!empty($unique)) {
			return L('error_membername_unique');
		}

		if (I('post.password')) {
			$data['password'] = I('post.password', '', 'md5,trim');
		}

		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		$data = array(
			'level_id' => I('post.level', '', C('PRIMARY_FILTER')),
			);
		$field = array('level_id', 'user_id');
		$map = array('user_id' => $id);
		$this->table($this->tablePrefix . 'level_member')
		->field($field)
		->where($map)
		->data($data)
		->save();

		action('member_editor', $id);
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

		$map = array('user_id' => $id);
		$this->table($this->tablePrefix . 'level_member')->where($map)->delete();

		action('member_remove', $id);
		return true;
	}

	/**
	 * 获得地址
	 * @access public
	 * @param  intval $pid_ 父级地区ID
	 * @return array
	 */
	public function region($pid_=1)
	{
		$field = array('id', 'pid', 'name');
		$map = array('pid' => $pid_);
		return M('region')->field($field)->where($map)->select();
	}

	/**
	 * 获得会员组数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getLevel()
	{
		$map = array('status' => 1);
		$order = array('id' => 'desc');
		return $this->table($this->tablePrefix . 'level')
		->where($map)->order($order)->select();
	}
}