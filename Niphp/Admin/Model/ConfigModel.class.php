<?php
/**
 *
 * 接口配置 - 微信公众平台 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ConfigModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class ConfigModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得接口配置数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getConfig()
	{
		$map = array(
			'name' => array(
				'in', 'wechat_token,wechat_encodingaeskey,wechat_appid,wechat_appsecret'
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
	 * 修改接口配置
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateConfig()
	{
		$rules = array(
			array('wechat_token', 'require', L('error_wechat_token')),
			array('wechat_encodingaeskey', 'require', L('error_wechat_encodingaeskey')),
			array('wechat_appid', 'require', L('error_wechat_appid')),
			array('wechat_appsecret', 'require', L('error_wechat_appsecret')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		foreach ($_POST as $key => $value) {
			$map = array('name' => $key);
			$data = array('value' => I($key, '', 'htmlspecialchars,trim'));
			$this->where($map)->data($data)->save();
		}

		action('wechat_config_update');
		return true;
	}
}