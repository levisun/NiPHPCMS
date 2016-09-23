<?php
/**
 *
 * 列表页 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ListModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class ListModel extends Model
{
	protected $tableName = '';
	protected $fields = array('id');
	protected $pk = 'id';
	protected $updateFields = array();

	/**
	 * 获得栏目内容列表
	 * @access public
	 * @param  string $tableName_ 表名
	 * @return array
	 */
	public function getList($tableName_)
	{
		$pid = I('get.cid', 0, C('PRIMARY_FILTER'));
		$pid .= ',' . $this->getChild();

		$map = array(
			'a.category_id' => array('in', "$pid"),
			'a.is_pass' => 1,
			'a.showtime' => array('ELT', time()),
			'a.recycle' => 0,
			'a.lang' => LANG_SET
			);
		if ($tid = I('get.tid', 0, C('PRIMARY_FILTER'))) {
			$map['a.type_id'] = $tid;
		}
		$CACHE = CACHE_KEY ? CACHE_KEY . 'alistcount' . $tableName_ : CACHE_KEY;
		$count = $this->cache($CACHE)
		->table($this->tablePrefix . $tableName_ . ' AS a')
		->where($map)
		->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field = array(
			'a.id', 'a.title', 'a.seo_title', 'a.seo_keywords', 'a.seo_description',
			'a.thumb', 'a.category_id', 'a.type_id', 'a.hits', 'a.comment',
			'a.username', 'a.url', 'a.is_link', 'a.addtime', 'a.updatetime',
			'a.access', 'l.name' => 'level_name', 'c.name' => 'cat_name', 't.name' => 'type_name'
			 );
		$join = array(
			'__CATEGORY__ AS c ON c.id=a.category_id',
			'LEFT JOIN __LEVEL__ AS l ON l.id=a.access',
			'LEFT JOIN __TYPE__ AS t ON t.id=a.type_id'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'alist' . $tableName_ : CACHE_KEY;
		$list = $this->cache($CACHE)
		->table($this->tablePrefix . $tableName_ . ' AS a')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('a.is_com DESC, a.is_top DESC, a.is_hot DESC, a.sort DESC, a.updatetime DESC')
		->select();
		foreach ($list as $key => $value) {
			$list[$key]['url'] = U('home/index/article', array('cid' => $value['category_id'], 'id' => $value['id']));
			$list[$key]['cat_url'] = U('home/index/entry', array('cid' => $value['category_id']));
			$list[$key]['comment_url'] = U('comment/index/entry', array('cid' => $value['category_id'], 'id' => $value['id']));
		}

		$data['list'] = $list;
		return $data;
	}

	/**
	 * 获得子栏目ID
	 * @access protected
	 * @param  string $pid_
	 * @return string
	 */
	protected function getChild($pid_='')
	{
		$pid_ = !empty($pid_) ? $pid_ : I('get.cid', 0, C('PRIMARY_FILTER'));
		$map = array(
			'pid' => array('in', "$pid_"),
			'lang' => LANG_SET
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'achild' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->table($this->tablePrefix . 'category')
		->field(array('id'))
		->where($map)
		->select();
		if (!empty($data)) {
			$arr = array();
			foreach ($data as $key => $value) {
				$arr[] = $value['id'];
			}
			$_pid = implode(',', $arr);
			$data = $this->getChild($_pid);
			if (!empty($data)) {
				$_pid .= ',' . $data;
			}
			return $_pid;
		}
	}
}