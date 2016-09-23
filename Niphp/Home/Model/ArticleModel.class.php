<?php
/**
 *
 * 内容页 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ArticleModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class ArticleModel extends Model
{
	protected $tableName = '';
	protected $fields = array('id');
	protected $pk = 'id';
	protected $updateFields = array('hits');

	/**
	 * 内容
	 * @access public
	 * @param  sting $tableName_
	 * @return array
	 */
	public function getArticle($tableName_)
	{
		$map = array(
			'id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'showtime' => array('ELT', time())
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'isarticle' . $tableName_: CACHE_KEY;
		$data = $this->cache($CACHE)
		->field('id')
		->table($this->tablePrefix . $tableName_)
		->where($map)
		->find();
		if (empty($data)) {
			redirect('404.html');
		}

		$map = array(
			'a.id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'a.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'a.is_pass' => 1,
			'a.showtime' => array('ELT', time()),
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
		if ($tableName_ == 'download') {
			$field[] = 'a.down_url';
		}
		$join = array(
			'__CATEGORY__ AS c ON c.id=a.category_id',
			'LEFT JOIN __LEVEL__ AS l ON l.id=a.access',
			'LEFT JOIN __TYPE__ AS t ON t.id=a.type_id'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'article' . $tableName_: CACHE_KEY;
		$data = $this->cache($CACHE)
		->table($this->tablePrefix . $tableName_ . ' AS a')
		->field($field)
		->join($join)
		->where($map)
		->find();

		if ($data['is_link']) {
			redirect($data['url']);
		}

		$data['content'] = htmlspecialchars_decode($data['content']);
		$data['url'] = U('article', array('cid' => $data['category_id'], 'id' => $data['id']));
		$data['cat_url'] = U('entry', array('cid' => $data['category_id']));
		$data['comment_url'] = U('comment/index/entry', array('cid' => $data['category_id'], 'id' => $data['id']));

		$data['album'] = $this->getAlbum($data['id'], $tableName_);
		$data['fileds'] = $this->getFields($data['id'], $data['category_id'], $tableName_);
		$data['tags'] = $this->getTags($data['id'], $data['category_id']);

		$this->hits($tableName_);

		return $data;
	}

	public function getPiece($id_, $cid_, $sort_, $tableName_)
	{
		$field = array('id', 'title', 'thumb', 'category_id');
		$order = 'is_com DESC, is_top DESC, is_hot DESC, sort DESC, id DESC';

		// 前一篇
		$map = array('id' => array('GT', $id_, 'OR'), 'category_id' => $cid_);
		$map = array('category_id' => $cid_, 'id' => array('NEQ', $id_));
		$map['_string'] = 'id > ' . $id_ . ' OR sort >= ' . $sort_;
		$piece['up'] = $this->cache(!APP_DEBUG)
		->table($this->tablePrefix . $tableName_)
		->field($field)
		->where($map)
		->order($order)
		->limit(2)
		->select();
		echo $this->getLastSql();

		// 后一篇
		$map = array('id' => array('LT', $id_), 'category_id' => $cid_);
		$piece['dn'] = $this->cache(!APP_DEBUG)
		->table($this->tablePrefix . $tableName_)
		->field($field)
		->where($map)
		->order($order)
		->find();

		return $piece;
	}

	/**
	 * 获得相册
	 * @access public
	 * @param  intval $id_
	 * @param  string $tableName_
	 * @return array
	 */
	public function getAlbum($id_, $tableName_)
	{
		if ($tableName_ == 'picture' || $tableName_ == 'product') {
			$map = array('main_id' => $id_);
			$CACHE = CACHE_KEY ? CACHE_KEY . 'album' . $tableName_ : CACHE_KEY;
			return $this->cache($CACHE)->table($this->tablePrefix . $tableName_ . '_album')
			->where($map)->select();
		}
	}

	/**
	 * 获得标签
	 * @access public
	 * @param  intval $id_
	 * @param  intval $cid_
	 * @param  string $tableName_
	 * @return array
	 */
	public function getFields($id_, $cid_, $tableName_)
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
		$CACHE = CACHE_KEY ? CACHE_KEY . 'fields' . $tableName_ : CACHE_KEY;
		return $this->cache($CACHE)
		->table($this->tablePrefix . $tableName_ . '_data AS a')
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
		$tags = $this->cache($CACHE)
		->table($this->tablePrefix . 'tags_article AS a')
		->field($field)
		->join($join)
		->where($map)
		->select();
		foreach ($tags as $key => $value) {
			$tags[$key]['url'] = U('tags', array('id' => $value['id'], 'name' => $value['name']));
		}
		return $tags;
	}

	/**
	 * 更新浏览数量
	 * @access public
	 * @param  string $tableName_ 表名
	 * @return void
	 */
	public function hits($tableName_)
	{
		$map = array(
			'id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'lang' => LANG_SET
			);
		$this->table($this->tablePrefix . $tableName_)
		->field('hits')
		->where($map)
		->setInc('hits', 1, 30);
	}
}