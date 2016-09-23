<?php
/**
 *
 * 网站界面 - 界面 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: TemplateModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class TemplateModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得模板数据
	 * @access public
	 * @param  sting $type_ 模板类型
	 * @return array
	 */
	public function getTheme($type_)
	{
		$map = array(
			'name' => strtolower($type_) . '_theme',
			'lang' => LANG_SET
			);
		$data = $this->field($this->fields)->where($map)->find();
		$themeData['config'] = $data['value'];

		Vendor('File#class', COMMON_PATH . 'Library');
		$data = \File::get('./Themes/' . $type_);
		$themeData['list'] = $data;
		return $themeData;
	}

	/**
	 * 编辑模板
	 * @access public
	 * @param  sting $type_ 模板类型
	 * @return mixed
	 */
	public function editor($type_)
	{
		if (empty($_GET['v'])) {
			return L('illegal_operation');
		}

		$map = array('name' => strtolower($type_) . '_theme');
		$data = array('value' => I('get.v'));
		$this->where($map)->data($data)->save();

		action('theme_update', '', $type_);
		return true;
	}
}