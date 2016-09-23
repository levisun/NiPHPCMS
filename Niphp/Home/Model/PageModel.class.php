<?php
/**
 *
 * 单页 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: PageModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class PageModel extends Model
{
	protected $tableName = 'page';
	protected $fields = array('id');
	protected $pk = 'id';
	protected $updateFields = array('hits');

	/**
	 * 内容
	 * @access public
	 * @param  sting $tableName_
	 * @return array
	 */
	public function getPage()
	{
		$map = array('category_id' => I('get.cid', 0, C('PRIMARY_FILTER')));
		$CACHE = CACHE_KEY ? CACHE_KEY . 'ispage' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->field('id')
		->where($map)
		->find();
		if (empty($data)) {
			redirect('404.html');
		}

		$map = array(
			'a.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'a.is_pass' => 1,
			'a.recycle' => 0,
			'a.lang' => LANG_SET
			);

		$field = array(
			'a.id', 'a.title', 'a.seo_title', 'a.seo_keywords', 'a.seo_description',
			'a.thumb', 'a.category_id', 'a.type_id', 'a.hits', 'a.comment',
			'a.username', 'a.url', 'a.is_link', 'a.addtime', 'a.updatetime',
			'a.content', 'a.access', 'a.sort',
			'l.name' => 'level_name', 'c.name' => 'cat_name', 't.name' => 'type_name'
			 );
		$join = array(
			'__CATEGORY__ AS c ON c.id=a.category_id',
			'LEFT JOIN __LEVEL__ AS l ON l.id=a.access',
			'LEFT JOIN __TYPE__ AS t ON t.id=a.type_id'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'page' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->table($this->tablePrefix . $this->tableName . ' AS a')
		->field($field)
		->join($join)
		->where($map)
		->find();
		$data['content'] = htmlspecialchars_decode($data['content']);

		if ($data['is_link']) {
			echo '<meta charset="UTF-8">';
			redirect($data['url'], 3);
		}

		$data['fileds'] = $this->getFields($data['id'], $data['category_id']);
		$data['tags'] = $this->getTags($data['id'], $data['category_id']);

		$this->hits();
		return $data;
	}

	/**
	 * 获得标签
	 * @access public
	 * @param  intval $id_
	 * @param  intval $cid_
	 * @return array
	 */
	public function getFields($id_, $cid_)
	{
		// 自定义字段
		$field = array(
			'a.data',
			'f.name' => 'field_name',
			'f.description',
			'ft.name' => 'field_type',
			);
		$join = array(
			'__FIELDS__ AS f ON f.category_id=' . $cid_,
			'__FIELDS_TYPE__ AS ft ON ft.id=f.type_id'
			);
		$map = array('a.main_id' => $id_);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'fields' : CACHE_KEY;
		return $this->cache($CACHE)
		->table($this->tablePrefix . $this->tableName . '_data AS a')
		->field($field)
		->join($join)
		->where($map)
		->select();
	}

	/**
	 * 获得标签
	 * @access public
	 * @param  intval $id_
	 * @param  intval $cid_
	 * @return array
	 */
	public function getTags($id_, $cid_)
	{
		// 标签
		$field = array('t.name', 't.id');
		$join = array('__TAGS__ AS t ON t.id=a.tags_id',);
		$map = array(
			'a.category_id' => $cid_,
			'a.article_id' => $id_
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'tags' : CACHE_KEY;
		return $this->cache($CACHE)
		->table($this->tablePrefix . 'tags_article AS a')
		->field($field)
		->join($join)
		->where($map)
		->select();
	}

	/**
	 * 更新浏览数量
	 * @access public
	 * @param
	 * @return void
	 */
	public function hits()
	{
		$map = array(
			'category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'lang' => LANG_SET
			);
		$this->field('hits')
		->where($map)
		->setInc('hits', 1, 30);
	}
}