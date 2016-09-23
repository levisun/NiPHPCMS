<?php
/**
 *
 *  管理内容 - 内容 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ContentModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class ContentModel extends Model
{
	protected $tableName = '';
	protected $fields = array(
						'id',
						'title',
						'seo_title',
						'seo_keywords',
						'seo_description',
						'content',
						'thumb',
						'category_id',
						'type_id',
						'is_pass',
						'is_com',
						'is_top',
						'is_hot',
						'sort',
						'hits',
						'comment',
						'username',
						'origin',
						'user_id',
						'url',
						'is_link',
						'recycle',
						'showtime',
						'addtime',
						'updatetime',
						'access',
						'lang',
						'down_url',	// 下载模型
						'logo',		// 友情
						'description',
						'reply',	// 留言反馈
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'title',
						'seo_title',
						'seo_keywords',
						'seo_description',
						'content',
						'thumb',
						'category_id',
						'type_id',
						'is_pass',
						'is_com',
						'is_top',
						'is_hot',
						'sort',
						'username',
						'origin',
						'user_id',
						'url',
						'is_link',
						'showtime',
						'addtime',
						'updatetime',
						'access',
						'lang',
						'down_url',	// 下载模型
						'logo',		// 友情
						'description',
						'reply',	// 留言反馈
						);
	protected $updateFields = array(
						'title',
						'seo_title',
						'seo_keywords',
						'seo_description',
						'content',
						'thumb',
						'category_id',
						'type_id',
						'is_pass',
						'is_com',
						'is_top',
						'is_hot',
						'sort',
						'username',
						'origin',
						'user_id',
						'url',
						'is_link',
						'recycle',
						'showtime',
						'updatetime',
						'access',
						'down_url',	// 下载模型
						'logo',		// 友情
						'description',
						'reply',	// 留言反馈
						);

	/**
	 * 获得栏目数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getCategory()
	{
		$map = array('c.pid' => 0);
		if (I('get.pid')) {
			$map = array('c.pid' => I('get.pid', 0, C('PRIMARY_FILTER')));
		}

		$map['c.lang'] = LANG_SET;

		$field = array(
			'c.id',
			'c.pid',
			'c.name',
			'c.type_id',
			'c.model_id',
			'c.is_show',
			'c.is_channel',
			'c.sort',
			'm.name' => 'model_name',
			'cc.id' => 'child'
			);
		$join = array(
			'__MODEL__ AS m ON m.id=c.model_id AND m.name!=\'external\'',
			'LEFT JOIN __CATEGORY__ AS cc ON c.id=cc.pid'
			);
		$data['list'] =
		$this->table($this->tablePrefix . 'category AS c')
		->field($field)
		->join($join)
		->where($map)
		->group('c.id')
		->order('c.type_id ASC, c.sort ASC, c.id DESC')
		->select();
		return $data;
	}

	/**
	 * 获得栏目内容数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		if (empty($_GET['cid']) || !is_numeric($_GET['cid'])) {
			return L('illegal_operation');
		}

		$map = array();
		if ($key = I('get.key')) {
			$map = array('title' => array('LIKE', '%' . $key . '%'));
		}

		$map['category_id'] = I('get.cid');
		$map['recycle'] = 0;
		$map['lang'] = LANG_SET;

		// 模型表
		$tableName = $this->getModelTable();
		$data['tableName'] = $tableName;

		$count = $this->table($this->tablePrefix . $tableName)->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		if ($tableName == 'link') {
			$order = 'is_pass ASC, sort DESC, updatetime DESC';
		} elseif ($tableName != 'message' && $tableName != 'feedback') {
			$order = 'is_pass ASC, is_com DESC, is_top DESC, is_hot DESC, sort DESC, updatetime DESC';
		} else {
			$order = 'is_pass ASC, updatetime DESC';
		}

		$data['list'] = $this->table($this->tablePrefix . $tableName)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order($order)
		->select();

		return $data;
	}

	/**
	 * 排序
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function upSort()
	{
		if (empty($_POST['sort'])) {
			return L('illegal_operation');
		}

		// 模型表
		$_GET['cid'] = I('post.cid', 0, C('PRIMARY_FILTER'));
		$tableName = $this->getModelTable();
		foreach ($_POST['sort'] as $key => $value) {
			$map = array('id' => floor(floatval($key)));
			$data = array('sort' => floor(floatval($value)));
			$this->table($this->tablePrefix . $tableName)
			->where($map)->data($data)->save();
		}

		action('content_sort', '', L('_model_' . $tableName));
		return true;
	}

	/**
	 * 新增内容
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		// 模型表
		$tableName = $this->getModelTable();

		$rules = array(
			array('title', 'require', L('error_title')),
			array('category_id', 'require', L('error_category')),
			array('category_id', 'number', L('error_category')),
			array('is_pass', 'require', L('error_pass')),
			array('is_pass', 'number', L('error_pass')),
			array('is_com', 'require', L('error_com')),
			array('is_com', 'number', L('error_com')),
			array('is_top', 'require', L('error_top')),
			array('is_top', 'number', L('error_top')),
			array('is_hot', 'require', L('error_hot')),
			array('is_hot', 'number', L('error_hot')),
			array('access', 'require', L('error_access')),
			array('access', 'number', L('error_access')),
			array('url', 'url', L('error_url'), 2),
			array('origin', 'url', L('error_origin'), 2),
			// array('down_url', 'require', L('error_down_url')),
			// array('logo', 'require', L('error_logo')),
			);

		// 自定义字段验证
		$fields = $this->getFields();
		$fieldsName = array();
		foreach ($fields as $key => $value) {
			if ($value['is_require']) {
				$this->fields[] = 'fields-' . $value['id'];
				$this->insertFields[] = 'fields-' . $value['id'];
				$this->updateFields[] = 'fields-' . $value['id'];
				$rules[] = array('fields-' . $value['id'], $value['type_regex'], $value['name'] . L('error_empty'));
				$fieldsName[$value['id']]['name'] = 'fields-' . $value['id'];
				$fieldsName[$value['id']]['type'] = $value['type_name'];
			}
		}

		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'title' => I('post.title'),
			'seo_title' => I('post.seo_title'),
			'seo_keywords' => I('post.seo_keywords'),
			'seo_description' => I('post.seo_description'),
			'content' => I('post.content', '', C('CONTENT_FILTER')),
			'thumb' => I('post.thumb'),
			'category_id' => I('post.category_id', 0, C('PRIMARY_FILTER')),
			'type_id' => I('post.type_id', 0, C('PRIMARY_FILTER')),
			'is_pass' => I('post.is_pass', 1, C('PRIMARY_FILTER')),
			'is_com' => I('post.is_com', 0, C('PRIMARY_FILTER')),
			'is_top' => I('post.is_top', 0, C('PRIMARY_FILTER')),
			'is_hot' => I('post.is_hot', 0, C('PRIMARY_FILTER')),
			'username' => I('post.username'),
			'origin' => I('post.origin'),
			'user_id' => session(C('USER_AUTH_KEY')),
			'url' => I('post.url'),
			'is_link' => I('post.is_link', 0, C('PRIMARY_FILTER')),
			'recycle' => I('post.recycle', 0, C('PRIMARY_FILTER')),
			'showtime' => I('post.showtime', time(), 'strtotime'),
			'addtime' => time(),
			'updatetime' => time(),
			'access' => I('post.access', 0, C('PRIMARY_FILTER')),
			'lang' => LANG_SET,
			);

		if ($tableName == 'link') {
			unset(
				$data['seo_title'],
				$data['seo_keywords'],
				$data['seo_description'],
				$data['content'],
				$data['thumb'],
				$data['is_com'],
				$data['is_top'],
				$data['is_hot'],
				$data['origin'],
				$data['username'],
				$data['is_link'],
				$data['showtime'],
				$data['access']
				);
			$data['logo'] = I('post.logo');
			$data['description'] = I('post.description');
		}
		if ($tableName == 'download') {
			$data['down_url'] = I('post.down_url');
		}

		$id = $this->table($this->tablePrefix . $tableName)
		->data($data)
		->add();

		/*
		 * 自定义字段
		 */
		if ($tableName != 'link') {
			$fields = array('main_id', 'fields_id', 'data');
			$fieldsData = array();
			foreach ($fieldsName as $key => $value) {
				$fieldsData['main_id'] = $id;
				$fieldsData['fields_id'] = $key;
				$filter = $value['type'] == 'date' ? 'strtotime' : C('DEFAULT_FILTER');
				$fieldsData['data'] = I($value['name'], '', $filter);

				$this->table($this->tablePrefix . $tableName . '_data')
				->field($fields)
				->data($fieldsData)
				->add();
			}
		}

		/*
		 * 相册
		 * 图文模型 产品模型
		 */
		if ($tableName == 'picture' || $tableName == 'product') {
			$fields = array('main_id', 'image', 'thumb');
			$fieldsData = array();
			foreach ($_POST['album-image'] as $key => $value) {
				$albumData['main_id'] = $id;
				$albumData['image'] = strip_tags(escape_xss(trim($value)));
				$albumData['thumb'] = strip_tags(escape_xss(trim($_POST['album-thumb'][$key])));
				$this->table($this->tablePrefix . $tableName . '_album')
				->field($fields)
				->data($albumData)
				->add();
			}
		}

		/*
		 * 标签
		 */
		if (I('post.tags')) {
			// $tags = $this->scws(I('post.tags'));

			// 搜索关联标签
			$tags = explode(' ', I('post.tags'));
			$map = array('name' => array('in', implode(',', $tags)));
			$data = $this->table($this->tablePrefix . 'tags')
			->where($map)
			->select();
			$tags_id = $tags_name = array();
			foreach ($data as $key => $value) {
				if (in_array($value['name'], $tags)) {
					$tags_id[] = $value['id'];
					$tags_name[] = $value['name'];
				}
			}
			// 关联标签数小于标签数，说明有新的标签，插入新标签
			if (count($data) < count($tags)) {
				$fields = array('name');
				foreach ($tags as $key => $value) {
					if (!in_array($value, $tags_name)) {
						$tags_id[] = $this->table($this->tablePrefix . 'tags')
						->field($fields)->data(array('name' => $value))->add();
					}
				}
			}
			$fields = array('tags_id', 'category_id', 'article_id');
			foreach ($tags_id as $key => $value) {
				$data = array(
					'tags_id' => $value,
					'category_id' => I('post.category_id', 0, C('PRIMARY_FILTER')),
					'article_id' => $id
					);
				$this->table($this->tablePrefix . 'tags_article')
				->field($fields)->data($data)->add();

				$count = $this->table($this->tablePrefix . 'tags_article')
				->where(array('tags_id' => $value))->count();
				$this->table($this->tablePrefix . 'tags')
				->field(array('number'))
				->where(array('id' => $value))
				->data(array('number' => $count))
				->save();
			}
		}

		action('content_add', $id, L('_model_' . $tableName));
		return true;
	}

	/**
	 * 获得要修改的数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getDataOne()
	{
		if (empty($_GET['cid']) || !is_numeric($_GET['cid'])) {
			return L('illegal_operation');
		}

		if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
			return L('illegal_operation');
		}

		// 模型表
		$tableName = $this->getModelTable();

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		$data = $this->table($this->tablePrefix . $tableName)
		->where($map)->find();
		if (!empty($data['content'])) {
			$data['content'] = escape_xss(htmlspecialchars_decode($data['content']));
		}

		/*
		 * 相册
		 * 图文模型 产品模型
		 */
		if ($tableName == 'picture' || $tableName == 'product') {
			$map = array('main_id' => $data['id']);
			$data['albumData'] = $this->table($this->tablePrefix . $tableName . '_album')
			->where($map)->select();
		}

		// 获得自己定义字段数据
		if ($tableName != 'link') {
			$map = array('main_id' => $data['id']);
			$fields = $this->table($this->tablePrefix . $tableName . '_data')
			->where($map)->select();
			$arr = array();
			foreach ($fields as $key => $value) {
				$arr[$value['fields_id']] = $value['data'];
			}
			$data['fieldsData'] = $this->getFields($arr);
		}


		$fields = array('t.name');
		$join = array('__TAGS__ AS t ON a.tags_id=t.id');
		$map = array(
			'a.category_id' => $data['category_id'],
			'a.article_id' => $data['id']
			);
		$tags = $this->table($this->tablePrefix . 'tags_article AS a')
		->field($fields)
		->join($join)
		->where($map)
		->select();
		$data['tags'] = '';
		foreach ($tags as $key => $value) {
			$data['tags'] .= $value['name'] . ' ';
		}

		return $data;
	}

	/**
	 * 编辑内容
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		// 模型表
		$tableName = $this->getModelTable();

		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('title', 'require', L('error_title')),
			array('category_id', 'require', L('error_category')),
			array('category_id', 'number', L('error_category')),
			array('is_pass', 'require', L('error_pass')),
			array('is_pass', 'number', L('error_pass')),
			array('is_com', 'require', L('error_com')),
			array('is_com', 'number', L('error_com')),
			array('is_top', 'require', L('error_top')),
			array('is_top', 'number', L('error_top')),
			array('is_hot', 'require', L('error_hot')),
			array('is_hot', 'number', L('error_hot')),
			array('access', 'require', L('error_access')),
			array('access', 'number', L('error_access')),
			array('url', 'url', L('error_url'), 2),
			array('origin', 'url', L('error_origin'), 2),

			// array('down_url', 'require', L('error_down_url')),
			// array('logo', 'require', L('error_logo')),
			);

		// 自定义字段验证
		$fields = $this->getFields();
		$fieldsName = array();
		foreach ($fields as $key => $value) {
			if ($value['is_require']) {
				$this->fields[] = 'fields-' . $value['id'];
				$this->insertFields[] = 'fields-' . $value['id'];
				$this->updateFields[] = 'fields-' . $value['id'];
				$rules[] = array('fields-' . $value['id'], $value['type_regex'], $value['name'] . L('error_empty'));
				$fieldsName[$value['id']]['name'] = 'fields-' . $value['id'];
				$fieldsName[$value['id']]['type'] = $value['type_name'];
			}
		}

		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'title' => I('post.title'),
			'seo_title' => I('post.seo_title'),
			'seo_keywords' => I('post.seo_keywords'),
			'seo_description' => I('post.seo_description'),
			'content' => I('post.content', '', C('CONTENT_FILTER')),
			'thumb' => I('post.thumb'),
			'category_id' => I('post.category_id', 0, C('PRIMARY_FILTER')),
			'type_id' => I('post.type_id', 0, C('PRIMARY_FILTER')),
			'is_pass' => I('post.is_pass', 1, C('PRIMARY_FILTER')),
			'is_com' => I('post.is_com', 0, C('PRIMARY_FILTER')),
			'is_top' => I('post.is_top', 0, C('PRIMARY_FILTER')),
			'is_hot' => I('post.is_hot', 0, C('PRIMARY_FILTER')),
			'username' => I('post.username'),
			'origin' => I('post.origin'),
			'user_id' => session(C('USER_AUTH_KEY')),
			'url' => I('post.url'),
			'is_link' => I('post.is_link', 0, C('PRIMARY_FILTER')),
			'recycle' => I('post.recycle', 0, C('PRIMARY_FILTER')),
			'showtime' => I('post.showtime', time(), 'strtotime'),
			'updatetime' => time(),
			'access' => I('post.access', 0, C('PRIMARY_FILTER')),
			'lang' => LANG_SET,
			);

		if ($tableName == 'link') {
			unset(
				$data['seo_title'],
				$data['seo_keywords'],
				$data['seo_description'],
				$data['content'],
				$data['thumb'],
				$data['is_com'],
				$data['is_top'],
				$data['is_hot'],
				$data['origin'],
				$data['username'],
				$data['is_link'],
				$data['showtime'],
				$data['access']
				);
			$data['logo'] = I('post.logo');
			$data['description'] = I('post.description');
		}
		if ($tableName == 'download') {
			$data['down_url'] = I('post.down_url');
		}
		if ($tableName == 'message' || $tableName == 'feedback') {
			unset(
				$data['title'],
				$data['category_id'],
				$data['type_id'],
				$data['user_id'],
				$data['url'],
				$data['seo_title'],
				$data['seo_keywords'],
				$data['seo_description'],
				$data['content'],
				$data['thumb'],
				$data['is_com'],
				$data['is_top'],
				$data['is_hot'],
				$data['origin'],
				$data['username'],
				$data['is_link'],
				$data['showtime'],
				$data['access']
				);
			$data['reply'] = I('post.reply');
		}

		$id = I('post.id', 0, C('PRIMARY_FILTER'));
		$map = array('id' => $id);
		$this->table($this->tablePrefix . $tableName)
		->where($map)
		->data($data)
		->save();

		/*
		 * 自定义字段
		 */
		if ($tableName != 'link') {
			$fields = array('main_id', 'fields_id', 'data');
			$fieldsData = array();
			foreach ($fieldsName as $key => $value) {
				$map = array('main_id' => $id, 'fields_id' => $key);
				$filter = $value['type'] == 'date' ? 'time' : C('DEFAULT_FILTER');
				$fieldsData['data'] = I($value['name'], '', $filter);

				$this->table($this->tablePrefix . $tableName . '_data')
				->field($fields)
				->where($map)
				->data($fieldsData)
				->save();
			}
		}

		/*
		 * 相册
		 * 图文模型 产品模型
		 */
		if ($tableName == 'picture' || $tableName == 'product') {
			$fields = array('main_id', 'image', 'thumb');
			$map = array('main_id' => $id);
			$data = $this->table($this->tablePrefix . $tableName . '_album')
			->where($map)
			->delete();

			foreach ($_POST['album-image'] as $key => $value) {
				if (!empty($value)) {
					$albumData['main_id'] = $id;
					$albumData['image'] = strip_tags(escape_xss(trim($value)));
					$albumData['thumb'] = strip_tags(escape_xss(trim($_POST['album-thumb'][$key])));
					$this->table($this->tablePrefix . $tableName . '_album')
					->field($fields)
					->data($albumData)
					->add();
				}
			}
		}

		/*
		 * 标签
		 */
		if (I('post.tags')) {
			// $tags = $this->scws(I('post.tags'));
			$tags = explode(' ', I('post.tags'));
			// 搜索关联标签
			$map = array('name' => array('in', implode(',', $tags)));
			$data = $this->table($this->tablePrefix . 'tags')
			->where($map)
			->select();
			$tags_id = $tags_name = array();
			foreach ($data as $key => $value) {
				if (in_array($value['name'], $tags)) {
					$tags_id[] = $value['id'];
					$tags_name[] = $value['name'];
				}
			}
			// 关联标签数小于标签数，说明有新的标签，插入新标签
			if (count($data) < count($tags)) {
				$fields = array('name');
				foreach ($tags as $key => $value) {
					if (!in_array($value, $tags_name)) {
						$tags_id[] = $this->table($this->tablePrefix . 'tags')
						->field($fields)->data(array('name' => $value))->add();
					}
				}
			}

			$fields = array('tags_id', 'category_id', 'article_id');
			$this->table($this->tablePrefix . 'tags_article')
			->where(array('article_id' => $id))->delete();
			foreach ($tags_id as $key => $value) {
				$data = array(
					'tags_id' => $value,
					'category_id' => I('post.category_id', 0, C('PRIMARY_FILTER')),
					'article_id' => $id
					);
				$this->table($this->tablePrefix . 'tags_article')
				->field($fields)->data($data)->add();

				$count = $this->table($this->tablePrefix . 'tags_article')
				->where(array('tags_id' => $value))->count();
				$this->table($this->tablePrefix . 'tags')
				->field(array('number'))
				->where(array('id' => $value))
				->data(array('number' => $count))
				->save();
			}
		} else {
			$fields = array('tags_id', 'category_id', 'article_id');
			$this->table($this->tablePrefix . 'tags_article')
			->where(array('article_id' => $id))->delete();
		}

		action('content_editor', $id, L('_model_' . $tableName));
		return true;
	}

	/**
	 * 获得单页数据
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function getPageDataOne()
	{
		if (empty($_GET['cid']) || !is_numeric($_GET['cid'])) {
			return L('illegal_operation');
		}

		// 模型表
		$tableName = $this->getModelTable();

		$map = array('cid' => I('get.cid', 0, C('PRIMARY_FILTER')));
		$data = $this->table($this->tablePrefix . $tableName)
		->where($map)->find();
		$data['content'] = escape_xss(htmlspecialchars_decode($data['content']));

		// 获得自己定义字段数据
		$map = array('main_id' => $data['id']);
		$fields = $this->table($this->tablePrefix . $tableName . '_data')
		->where($map)->select();
		$arr = array();
		foreach ($fields as $key => $value) {
			$arr[$value['fields_id']] = $value['data'];
		}
		$data['fieldsData'] = $this->getFields($arr);

		return $data;
	}

	/**
	 * 单页面添加编辑
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function pageEditor()
	{
		$data = $this->getPageDataOne();
		if (!empty($data['id'])) {
			return $this->editor();
		} else {
			return $this->added();
		}
	}

	/**
	 * 删除到回收站
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		if (empty($_GET['cid']) || !is_numeric($_GET['cid'])) {
			return L('illegal_operation');
		}

		if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
			return L('illegal_operation');
		}

		// 模型表
		$tableName = $this->getModelTable();

		$id = I('get.id', 0, C('PRIMARY_FILTER'));
		$map = array('id' => $id);
		$this->table($this->tablePrefix . $tableName)
		->where($map)
		->data(array('recycle' => 1))
		->save();

		action('content_recycle', $id, L('_model_' . $tableName));
		return true;
	}

	/**
	 * 获得分类
	 * @access public
	 * @param
	 * @return array
	 */
	public function getType()
	{
		$map = array('category_id' => I('get.cid', 0, C('PRIMARY_FILTER')));
		return $this->table($this->tablePrefix . 'type')
		->where($map)
		->select();
	}

	/**
	 * 获得模型表
	 * @access public
	 * @param
	 * @return array
	 */
	public function getModelTable()
	{
		$map = array('c.id' => I('get.cid', 0, C('PRIMARY_FILTER')));
		$map['c.lang'] = LANG_SET;

		$field = array(
			'm.name' => 'model_name',
			);
		$join = array(
			'__MODEL__ AS m ON m.id=c.model_id AND m.name!=\'external\'',
			'LEFT JOIN __CATEGORY__ AS cc ON c.id=cc.pid'
			);
		$data = $this->table($this->tablePrefix . 'category AS c')
		->field($field)
		->join($join)
		->where($map)
		->find();

		return $data['model_name'];
	}

	/**
	 * 获得自定义字段
	 * @access public
	 * @param  array $filedsData_ 自定义字段数据 用于编辑内容
	 * @return array
	 */
	public function getFields($filedsData_=array())
	{
		$map = array('f.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')));

		$field = array(
			'f.id',
			'f.category_id',
			'f.type_id',
			'f.name',
			'f.description',
			'f.is_require',
			't.name' => 'type_name',
			't.regex' => 'type_regex'
			);
		$join = array(
			'__FIELDS_TYPE__ AS t ON t.id=f.type_id'
			);
		$data = $this->table($this->tablePrefix . 'fields AS f')
		->field($field)
		->join($join)
		->where($map)
		->select();

		foreach ($data as $key => $value) {
			switch ($value['type_name']) {
				case 'number':
				case 'email':
				case 'phone':
					$data[$key]['input'] = '<input type="' . $value['type_name'] . '" id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control">';

					if (!empty($filedsData_[$value['id']])) {
						$data[$key]['input'] = '<input type="' . $value['type_name'] . '" id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control" value="' . $filedsData_[$value['id']] . '">';
					}
					break;

				case 'url':
				case 'currency':
				case 'abc':
				case 'idcards':
				case 'landline':
				case 'age':
					$data[$key]['input'] = '<input type="text" id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control">';

					if (!empty($filedsData_[$value['id']])) {
						$data[$key]['input'] = '<input type="text" id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control" value="' . $filedsData_[$value['id']] . '">';
					}
					break;

				case 'date':
					$data[$key]['input'] = '<input type="text" id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control">';

					if (!empty($filedsData_[$value['id']])) {
						$data[$key]['input'] = '<input type="text" id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control" value="' . date('Y-m-d', $filedsData_[$value['id']]) . '">';
					}

					$data[$key]['input'] .= '<script type="text/javascript">
						$(function () {
							$("#fields-' . $value['id'] . '").datetimepicker(
								{format: "Y-M-D"}
								);
						});
						</script>';
					break;

				case 'text':
					// $data[$key]['input'] = '<input type="text" name="fields[' . $value['id'] . ']">';
					$data[$key]['input'] = '<textarea id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control"></textarea>';

					if (!empty($filedsData_[$value['id']])) {
						$data[$key]['input'] = '<textarea id="fields-' . $value['id'] . '" name="fields-' . $value['id'] . '" class="form-control">' . $filedsData_[$value['id']] . '</textarea>';
					}
					break;
			}
		}
		return $data;
	}

	/**
	 * 是否要审核
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function getCheck()
	{
		$map = array(
			'name' => 'content_check',
			'lang' => 'niphp'
			);
		$data = $this->table($this->tablePrefix . 'config')
		->where($map)
		->find();
		return $data['value'];
	}

	/**
	 * 中文分词
	 * @access public
	 * @param  string $string_
	 * @return array
	 */
	public function scws($string_)
	{
		Vendor('Scws.pscws4' . MODULE_PATH . 'Library');
		$pscws = new \PSCWS4();
		$pscws->set_dict(MODULE_PATH . 'Library/Scws/lib/dict.utf8.xdb');
		$pscws->set_rule(MODULE_PATH . 'Library/Scws/lib/rules.utf8.ini');
		$pscws->set_ignore(true);
		$pscws->send_text($string_);
		$words = $pscws->get_tops(5);
		$tags = array();
		foreach ($words as $val) {
			$tags[] = $val['word'];
		}
		$pscws->close();
		return $tags;
	}
}