<?php
/**
 *
 * 帐户 - 模型
 *
 * @category   Member\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: AccountModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Member\Model;
use Think\Model;
class AccountModel extends Model
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
						'lastloginip',
						'lastloginipattr',
						'lastlogintime',
						'regtime',
						'verify'
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
						'lastloginip',
						'lastloginipattr',
						'lastlogintime',
						);

	/**
	 * 验证用户是否登录
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function isUserLogin()
	{
		if (MODULE_NAME == 'Member' && CONTROLLER_NAME != 'Account') {
			$action = array('login', 'logout', 'verify');
			if (!in_array(ACTION_NAME, $action) && !cookie(C('USER_AUTH_KEY'))) {
				redirect(U('member/account/login'));
			}
		}
	}

	/**
	 * 用户登录验证
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function checkLogin()
	{
		$rules = array(
			array('username', 'require', L('error_username')),
			array('password', 'require', L('error_password')),
			array('verify', 'require', L('error_verify')),
			array('verify', 'check_verify', L('error_confirm_verify'), 1, 'function'),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$userData = $this->getUserInfo();
		if ($userData === false) {
			return L('error_login');
		}

		$dir = '../../../../';
		$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
		$area = $ip->getlocation();

		$userData['lastloginip'] = $area['ip'];
		$userData['lastloginipattr'] = $area['country'] . $area['area'];
		$userData['lastlogintime'] = time();

		$map = array('id' => $userData['id']);
		$this->field($this->updateFields)->where($map)->data($userData)->save();

		cookie('USER_DATA', $userData);
		cookie(C('USER_AUTH_KEY'), $userData['id']);

		return true;
	}

	/**
	 * 注销
	 * @access public
	 * @param
	 * @return void
	 */
	public function logout()
	{
		cookie('USER_DATA', null);
		cookie(C('USER_AUTH_KEY'), null);
		redirect(U('account/login'));
	}

	/**
	 * 注册
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function reg()
	{
		$rules = array(
			array('username', 'require', L('error_username')),
			array('username', '', L('error_username_unique'), 0, 'unique', 1),
			array('password', 'require', L('error_password')),
			array('password', 'not_password', L('error_not_password'), 0, 'confirm'),
			array('password', '6,20', L('error_password_length'), 0, 'length'),
			array('verify', 'require', L('error_verify')),
			array('verify', 'check_verify', L('error_confirm_verify'), 1, 'function'),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$dir = '../../../../';
		$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
		$area = $ip->getlocation();

		$data = array(
			'username' => I('post.username'),
			'password' => I('post.password', '', 'md5,trim'),
			'status' => 1,
			'lastloginip' => $area['ip'],
			'lastloginipattr' => $area['country'] . $area['area'],
			'lastlogintime' => time(),
			'regtime' => time(),
			);
		$id = $this->data($data)->add();

		$table = $this->tablePrefix . 'level';
		$levle_id = $this->table($table)->field('id')->order('id DESC')->find();

		$data = array('user_id' => $id, 'level_id' => $levle_id['id']);
		$field = array('user_id', 'level_id');
		$table = $this->tablePrefix . 'level_member';
		$this->table($table)->field($field)->data($data)->add();

		$userData = $this->getUserInfo();
		if ($userData === false) {
			return L('error_login');
		}

		cookie('USER_DATA', $userData);
		cookie(C('USER_AUTH_KEY'), $userData['id']);

		return true;
	}

	/**
	 * 获得用户登录或注册信息
	 * @access private
	 * @param
	 * @return mixed
	 */
	private function getUserInfo()
	{
		$table = array(
			$this->tablePrefix . 'member' => 'm',
			$this->tablePrefix . 'level_member' => 'lm',
			$this->tablePrefix . 'level' => 'l'
			);
		$map = array(
			'm.username' => I('post.username'),
			'm.password' => I('post.password', '', 'md5,trim'),
			'm.status' => 1,
			'm.id=lm.user_id',
			'l.id=lm.level_id'
			);
		$field = array(
			'm.id',
			'm.username',
			'm.email',
			'm.lastloginip',
			'm.lastloginipattr',
			'm.lastlogintime',
			'l.id' => 'level_id',
			'l.name' => 'level_name'
			);

		$userData = $this->table($table)->field($field)->where($map)->find();
		if (!$userData) {
			return false;
		}
		return $userData;
	}
}