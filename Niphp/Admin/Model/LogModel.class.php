<?php
/**
 *
 * 日志 - 扩展 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: LogModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class LogModel extends Model
{
	protected $tableName = 'action_log';
	protected $pk = 'id';

	/**
	 * 获得系统日志
	 * @access public
	 * @param
	 * @return array
	 */
	public function getLog()
	{
		// 删除过期的日志(保留半年)
		$map = array('create_time' => array('ELT', strtotime('-180 days')));
		$this->where($map)->delete();

		$count = $this->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field = array(
				'l.action_ip',
				'l.model',
				'l.record_id',
				'l.remark',
				'l.create_time',
				'a.title' => 'title',
				'u.username',
				'r.name' => 'role_name'
				);
		$join = array(
			'__ACTION__ AS a ON a.id=l.action_id',
			'__ADMIN__ AS u ON u.id=l.user_id',
			'__ROLE_ADMIN__ AS ra ON ra.user_id=l.user_id',
			'__ROLE__ AS r ON r.id=ra.role_id'
			);
		$data['list'] =
		$this->table($this->tablePrefix . $this->tableName . ' AS l')
		->field($field)
		->join($join)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('create_time DESC')
		->select();

		return $data;
	}
}