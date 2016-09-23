<?php
/**
 *
 * 帐户 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: AccountModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class AccountModel extends Model
{
	protected $tableName = 'admin';
	protected $fields = array(
						'id',
						'username',
						'password',
						'email',
						'lastloginip',
						'lastloginipattr',
						'lastlogintime'
						);
	protected $pk = 'id';
	protected $updateFields = array(
						'lastloginip',
						'lastloginipattr',
						'lastlogintime'
						);

	/**
	 * 验证用户是否登录
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function isUserLogin()
	{
		$action = explode(',', C('NOT_AUTH_ACTION'));
		if (!in_array(ACTION_NAME, $action) && !session(C('USER_AUTH_KEY'))) {
			redirect(U('account/login'));
		}

		if (in_array(ACTION_NAME, $action) && session(C('USER_AUTH_KEY'))) {
			redirect(U('settings/info'));
		}

		\Org\Util\Rbac::checkLogin();
		if (\Org\Util\Rbac::AccessDecision()) {
			session('_ACCESS_LIST', \Org\Util\Rbac::getAccessList(session(C('USER_AUTH_KEY'))));
			return true;
		}
		return false;
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

		$table = array(
			$this->tablePrefix . 'admin' => 'a',
			$this->tablePrefix . 'role_admin' => 'ra',
			$this->tablePrefix . 'role' => 'r'
			);
		$map = array(
			'a.username' => I('post.username'),
			'a.password' => I('post.password', '', 'md5,trim'),
			'a.id=ra.user_id',
			'r.id=ra.role_id'
			);
		$field = array(
			'a.id',
			'a.username',
			'a.email',
			'a.lastloginip',
			'a.lastloginipattr',
			'a.lastlogintime',
			'r.id' => 'role_id',
			'r.name' => 'role_name'
			);
		$userData = $this->table($table)->field($field)->where($map)->find();
		if (!$userData) {
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

		session('USER_DATA', $userData);
		session(C('USER_AUTH_KEY'), $userData['id']);

		action('admin_login');
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
		action('admin_logout');

		session('USER_DATA', null);
		session(C('USER_AUTH_KEY'), null);
		session('_ACCESS_LIST', null);
		redirect('?m=admin');
	}

	/**
	 * 获得用户权限菜单
	 * @access public
	 * @param
	 * @return array
	 */
	public function getAccountMenu()
	{
		// 管理员登录页网站标题
		if (ACTION_NAME == 'login' || ACTION_NAME == 'logout') {
			$data['title'] = L('manage_login') . ' - NIPHPCMS';
			return $data;
		}

		if (!session('_ACCESS_LIST')) {
			return false;
		}
		$account = $_SESSION['_ACCESS_LIST'][strtoupper(MODULE_NAME)];
		$_nav = L('_nav');
		$_menu = L('_menu');
		$accountMenu = array();
		foreach ($account as $key => $value) {
			$controller = strtolower($key);
			foreach ($value as $k => $val) {
				$action = strtolower($k);
				$accountMenu[$controller]['name'] = $_nav[$key];
				$accountMenu[$controller]['menu'][] = array(
					'action' => $action,
					'url' => U($controller . '/' . $action),
					'lang' => $_menu[$key . '_' . $k],
					);
			}
		}
		$data['accountMenu'] = $accountMenu;

		if (ACTION_NAME == 'upload') {
			// 上传页面网站标题
			$title = L('upload_file') . ' - NIPHPCMS';
		} else {
			$title = $_menu[strtoupper(CONTROLLER_NAME . '_' . ACTION_NAME)];
			$title .= ' - ' . $_nav[strtoupper(CONTROLLER_NAME)] . ' - NIPHPCMS';
		}

		$data['title'] = $title;

		$bn = array(
			'Settings' => 'info',
			'Theme' => 'template',
			'Category' => 'category',
			'Content' => 'content',
			'User' => 'member',
			'Wechat' => 'keyword',
			'Shop' => 'goods',
			'Expand' => 'log',
			);
		$breadcrumb = '<li><a href="' . U('settings/info') . '">' . L('home') . '</a></li>';
		$breadcrumb .= '<li><a href="' . U(CONTROLLER_NAME . '/' . $bn[CONTROLLER_NAME]) . '">' . $_nav[strtoupper(CONTROLLER_NAME)] . '</a></li>';
		$breadcrumb .= '<li><a href="' . U(CONTROLLER_NAME . '/' . ACTION_NAME) . '">' . $_menu[strtoupper(CONTROLLER_NAME . '_' . ACTION_NAME)] . '</a></li>';

		if (I('get.cid')) {
			$bread = $this->getParent(I('get.cid'));
		}
		if (I('get.pid')) {
			$bread = $this->getParent(I('get.pid'));
		}
		if (!empty($bread)) {
			$count = count($bread);
			foreach ($bread as $key => $value) {
				if ($key+1 == $count) {
					$breadcrumb .= '<li class="active"><a>' . $value['name'] . '</a></li>';
				} else {
					$breadcrumb .= '<li><a href="' . U('content/content', array('pid' => $value['id'])) . '">' . $value['name'] . '</a></li>';
				}
			}
		}

		$data['breadcrumb'] = $breadcrumb;
		$data['sub_title'] = $_menu[strtoupper(CONTROLLER_NAME . '_' . ACTION_NAME)];
		return $data;
	}

	/**
	 * 获得父级栏目
	 * @access public
	 * @param  intval $pid_
	 * @return intval
	 */
	public function getParent($pid_)
	{
		$breadcrumb = array();
		$map = array('id' => $pid_, 'lang' => LANG_SET);
		$data = $this->table($this->tablePrefix . 'category')
		->where($map)
		->find();

		if (!empty($data['pid'])) {
			$breadcrumb = $this->getParent($data['pid']);
		}
		$breadcrumb[] = $data;
		return $breadcrumb;
	}

	/**
	 * 获得模型缩略图尺寸
	 * @access public
	 * @param  string $model_
	 * @return array
	 */
	public function getModelSize($model_)
	{
		if (empty($model_)) {
			return array();
		}

		$map = array(
			'name' => array('in', $model_ . '_module_width,' . $model_ . '_module_height'),
			'lang' => LANG_SET,
			);
		$data = $this->table($this->tablePrefix . 'config')->field('name, value')
		->where($map)
		->limit(2)
		->select();
		foreach ($data as $key => $value) {
			$config[$value['name']] = $value['value'];
		}
		return $config;
	}
}