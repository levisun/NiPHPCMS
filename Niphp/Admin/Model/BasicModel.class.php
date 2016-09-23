<?php
/**
 *
 * 基本设置 - 系统设置 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: BasicModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class BasicModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得基本设置数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getConfig()
	{
		$map = array(
			'name' => array(
				'in', 'website_name,website_keywords,website_description,bottom_message,copyright,script'
				),
			'lang' => LANG_SET
			);
		$data = $this->field($this->fields)->where($map)->select();
		foreach ($data as $key => $value) {
			$arr[$value['name']] = $value['value'];
			if ($value['name'] == 'bottom_message' OR $value['name'] == 'copyright' OR $value['name'] == 'script') {
				$arr[$value['name']] = htmlspecialchars_decode($value['value']);
			}
		}
		return $arr;
	}

	/**
	 * 修改基本设置
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateConfig()
	{
		$rules = array(
			array('website_name', 'require', L('error_basic_website_name')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		foreach ($_POST as $key => $value) {
			$map = array('name' => $key);
			if ($key == 'bottom_message' OR $key == 'copyright' OR $key == 'script') {
				$data = array('value' => I($key, '', 'htmlspecialchars,trim'));
			} else {
				$data = array('value' => I($key));
			}

			$this->where($map)->data($data)->save();
		}

		action('config_update');
		return true;
	}
}