<?php
/**
 *
 * 安全与效率 - 系统设置 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: SafeModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class SafeModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得安全与效率设置数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getSafe()
	{
		$map = array(
			'name' => array(
				'in', 'system_portal,content_check,member_login_captcha,website_submit_captcha,upload_file_max,upload_file_type,website_static'
				),
			'lang' => 'niphp'
			);
		$data = $this->field($this->fields)->where($map)->select();
		foreach ($data as $key => $value) {
			$arr[$value['name']] = $value['value'];
		}
		$data = session('USER_DATA');
		$arr['founder'] = $data['role_id'] == 1 ? 1 : 0;

		return $arr;
	}

	/**
	 * 修改安全与效率设置
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateSafe()
	{
		$rules = array(
			array('content_check', 'require', L('error_safe_content_check')),
			array('member_login_captcha', 'require', L('error_safe_member_login_captcha')),
			array('website_submit_captcha', 'require', L('error_safe_website_submit_captcha')),
			array('website_static', 'require', L('error_safe_website_static')),
			array('upload_file_max', 'require', L('error_safe_upload_file_max')),
			array('upload_file_max', 'number', L('error_safe_upload_file_max_number')),
			array('upload_file_type', 'require', L('error_safe_upload_file_type')),
			);
		$data = session('USER_DATA');
		if ($data['role_id'] == 1) {
			$rules[] = array('system_portal', 'require', L('error_safe_upload_file_type'));
		}
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = session('USER_DATA');
		if ($data['role_id'] == 1) {
			$map = array('name' => 'system_portal');
			$data = $this->field($this->fields)->where($map)->find();
			if ($_POST['system_portal'] != $data['value']) {
				rename(THINK_PATH . '../' . $data['value'] . '.php', THINK_PATH . '../' . $_POST['system_portal'] . '.php');
			}
		}

		foreach ($_POST as $key => $value) {
			$map = array('name' => $key);
			$data = array('value' => I($key));
			$this->where($map)->data($data)->save();
		}

		action('config_update');
		return true;
	}
}