<?php
/**
 *
 * 邮箱设置 - 系统设置 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: EmailModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class EmailModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得邮箱设置数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getEmail()
	{
		$map = array(
			'name' => array(
				'in', 'smtp_host,smtp_port,smtp_username,smtp_password,smtp_from_email,smtp_from_name'
				),
			'lang' => 'niphp'
			);
		$data = $this->field($this->fields)->where($map)->select();
		foreach ($data as $key => $value) {
			$arr[$value['name']] = $value['value'];
		}
		return $arr;
	}

	/**
	 * 修改邮箱设置
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateEmail()
	{
		$rules = array(
			array('smtp_host', 'require', L('error_emailsms_smtp_host')),
			array('smtp_port', 'require', L('error_emailsms_smtp_port')),
			array('smtp_port', 'number', L('error_emailsms_smtp_port')),
			array('smtp_username', 'require', L('error_emailsms_smtp_username')),
			array('smtp_password', 'require', L('error_emailsms_smtp_password')),
			array('smtp_from_email', 'require', L('error_emailsms_smtp_from_email')),
			array('smtp_from_name', 'require', L('error_emailsms_smtp_from_name')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
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