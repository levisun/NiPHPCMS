<?php
/**
 *
 * 系统信息 - 系统设置 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: InfoModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class InfoModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得系统信息
	 * @access public
	 * @param
	 * @return array
	 */
	public function getSysInfo()
	{
		$sysData = array();

		$sysData['os'] = PHP_OS;
		$sysData['env'] = $_SERVER['SERVER_SOFTWARE'];
		$sysData['phpversion'] = PHP_VERSION;
		$sysData['dbtype'] = C('DB_TYPE');
		$sysData['dbversion'] = $this->query('SELECT version()');
		$sysData['dbversion'] = $sysData['dbversion'][0]['version()'];
		$sysData['member'] = $this->table($this->tablePrefix . 'member')->count();
		$sysData['member_reg'] = $this->table($this->tablePrefix . 'member')
		->where(array('status' => 0))
		->count();
		$sysData['feedback'] = $this->table($this->tablePrefix . 'feedback')->count();
		$sysData['message'] = $this->table($this->tablePrefix . 'message')->count();
		$sysData['link'] = $this->table($this->tablePrefix . 'link')->count();
		$sysData['ads'] = $this->table($this->tablePrefix . 'ads')
		->where(array('endtime' => array('egt', time())))
		->count();

		return $sysData;
	}
}