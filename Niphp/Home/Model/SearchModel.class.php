<?php
/**
 *
 * 搜索 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: SearchModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class SearchModel extends Model
{
	protected $tableName = '';
	protected $fields = array('id');
	protected $pk = 'id';
	protected $updateFields = array('hits');

	/**
	 * 获得标签对应列表
	 * @access public
	 * @param
	 * @return array
	 */
	public function getList()
	{
		$table = array(
			$this->tablePrefix . 'article' => 'a',
			$this->tablePrefix . 'category' => 'c',
			);
		$key = I('get.key');
		$map = array(
			'a.title' => array('LIKE', '%' . $key . '%'),
			'a.lang' => LANG_SET
			);
		$map['_string'] = 'c.id=a.category_id';
		$field = array(
			'a.id', 'a.title', 'a.seo_title', 'a.seo_keywords', 'a.seo_description',
			'a.thumb', 'a.category_id', 'a.type_id', 'a.hits', 'a.comment',
			'a.username', 'a.url', 'a.is_link', 'a.addtime', 'a.updatetime',
			'a.access',
			'c.name' => 'cat_name'
			);

		$map_union = 'a.title LIKE \'%' . $key . '%\' AND ( c.id=a.category_id ) AND a.lang=\'' . LANG_SET . '\'';
		$CACHE = CACHE_KEY ? CACHE_KEY . 'slistcount' . $key : CACHE_KEY;
		$count = $this->cache($CACHE)->table($table)
		->field($field)
		->where($map)
		->union('SELECT count(*) FROM `' . $this->tablePrefix . 'download` `a`,`' . $this->tablePrefix . 'category` `c` WHERE ' . $map_union)
		->union('SELECT count(*) FROM `' . $this->tablePrefix . 'picture` `a`,`' . $this->tablePrefix . 'category` `c` WHERE ' . $map_union)
		->union('SELECT count(*) FROM `' . $this->tablePrefix . 'product` `a`,`' . $this->tablePrefix . 'category` `c` WHERE ' . $map_union)
		->count();

		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field_union = 'a.id,a.title,a.seo_title,a.seo_keywords,a.seo_description,a.thumb,a.category_id,a.type_id,a.hits,a.comment,a.username,a.url,a.is_link,a.addtime,a.updatetime,a.access,c.name AS `cat_name`';
		$CACHE = CACHE_KEY ? CACHE_KEY . 'slist' . $key : CACHE_KEY;
		$list = $this->cache($CACHE)->table($table)
		->field($field)
		->where($map)
		->union('(SELECT ' . $field_union . ' FROM `' . $this->tablePrefix . 'download` `a`,`' . $this->tablePrefix . 'category` `c` WHERE ' . $map_union . ')')
		->union('(SELECT ' . $field_union . ' FROM `' . $this->tablePrefix . 'picture` `a`,`' . $this->tablePrefix . 'category` `c` WHERE ' . $map_union . ')')
		->union('(SELECT ' . $field_union . ' FROM `' . $this->tablePrefix . 'product` `a`,`' . $this->tablePrefix . 'category` `c` WHERE ' . $map_union . ') order by updatetime DESC')
		->select();

		foreach ($list as $key => $value) {
			$list[$key]['url'] = U('article', array('cid' => $value['category_id'], 'id' => $value['id']));
		}

		$data['list'] = $list;
		return $data;
	}
}