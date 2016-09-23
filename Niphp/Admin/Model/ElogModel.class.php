<?php
/**
 *
 * 错误日志 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ElogModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class ElogModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得错误日志
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		Vendor('File#class', COMMON_PATH . 'Library');
		$key = I('get.name', '');
		$key = ucfirst($key);
		$data = \File::get(LOG_PATH . $key . '/');
		array_multisort($data, SORT_DESC);

		foreach ($data as $key => $value) {
			$count = \File::get(LOG_PATH . $value['name'] . '/');
			$data[$key]['count'] = count($count);
		}

		return $data;
	}

	/**
	 * 获得日志内容
	 * @access public
	 * @param
	 * @return string
	 */
	public function getShow()
	{
		if (empty($_GET['id'])) {
			return L('illegal_operation');
		}

		$data = file_get_contents(LOG_PATH . I('get.name') . '/' . I('get.id'));
		return $data;
	}
}