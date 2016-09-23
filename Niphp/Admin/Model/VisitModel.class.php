<?php
/**
 *
 * 访问统计 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: VisitModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class VisitModel extends Model
{
	protected $tableName = 'searchengine';
	protected $fields = array('date', 'name', 'count');
	protected $pk = 'id';
	protected $updateFields = array();

	/**
	 *
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$this->delLog();

		if (I('get.d')) {
			$table = $this->tablePrefix . 'searchengine';
		} else {
			$table = $this->tablePrefix . 'visit';
		}

		$count = $this->table($table)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$data['list'] =
		$this->table($table)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('date DESC')
		->select();

		return $data;
	}

	/**
	 * 删除过期日志
	 * @access public
	 * @param
	 * @return array
	 */
	private function delLog()
	{
		$map = array('date' => array('ELT', strtotime('-90 days')));

		// 删除过期的搜索日志(保留三个月)
		$table = $this->tablePrefix . 'visit';
		$this->table($table)->where($map)->delete();

		// 删除过期的访问日志(保留三个月)
		$table = $this->tablePrefix . 'visit';
		$this->table($table)->where($map)->delete();
	}
}