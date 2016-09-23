<?php
/**
 *
 * 栏目管理 - 栏目管理 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: CategoryModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model
{
	protected $tableName = 'category';
	protected $fields = array(
						'id',
						'pid',
						'name',
						'aliases',
						'seo_title',
						'seo_keywords',
						'seo_description',
						'image',
						'type_id',
						'model_id',
						'is_show',
						'is_channel',
						'sort',
						'access',
						'url',
						'lang'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'pid',
						'name',
						'aliases',
						'seo_title',
						'seo_keywords',
						'seo_description',
						'image',
						'type_id',
						'model_id',
						'is_show',
						'is_channel',
						'sort',
						'access',
						'url',
						'lang'
						);
	protected $updateFields = array(
						'pid',
						'name',
						'aliases',
						'seo_title',
						'seo_keywords',
						'seo_description',
						'image',
						'type_id',
						'model_id',
						'is_show',
						'is_channel',
						'sort',
						'access',
						'url',
						'lang'
						);

	/**
	 * 获得栏目数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('c.name' => array('LIKE', '%' . $key . '%'));
		} else {
			$map = array('c.pid' => I('get.pid', 0, C('PRIMARY_FILTER')));
		}

		$map['c.lang'] = LANG_SET;

		$count = $this->table($this->tablePrefix . $this->tableName . ' AS c')->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

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
			'__MODEL__ AS m ON m.id=c.model_id',
			'LEFT JOIN __CATEGORY__ AS cc ON c.id=cc.pid'
			);
		$data['list'] =
		$this->table($this->tablePrefix . $this->tableName . ' AS c')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->group('c.id')
		->order('c.type_id ASC, c.sort ASC, c.id DESC')
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
		foreach ($_POST['sort'] as $key => $value) {
			$map = array('id' => floor(floatval($key)));
			$data = array('sort' => floor(floatval($value)));
			$this->where($map)->data($data)->save();
		}

		action('category_sort');
		return true;
	}

	/**
	 * 新增栏目
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_catename')),
			array('name', '', L('error_catename_unique'), 0, 'unique', 1),
			array('aliases', '', L('error_aliases_unique'), 2, 'unique', 1),
			array('aliases', 'english', L('error_aliases_abc'), 2),
			array('type_id', 'require', L('error_type')),
			array('type_id', 'number', L('error_type')),
			array('model_id', 'require', L('error_model')),
			array('model_id', 'number', L('error_model')),
			array('access', 'require', L('error_access')),
			array('access', 'number', L('error_access')),
			array('url', 'url', L('error_url'), 2),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'pid' => I('post.pid', 0, C('PRIMARY_FILTER')),
			'name' => I('post.name'),
			'aliases' => I('post.aliases'),
			'seo_title' => I('post.seo_title'),
			'seo_keywords' => I('post.seo_keywords'),
			'seo_description' => I('post.seo_description'),
			'image' => I('post.image'),
			'type_id' => I('post.type_id', 0, C('PRIMARY_FILTER')),
			'model_id' => I('post.model_id', 0, C('PRIMARY_FILTER')),
			'is_show' => I('post.is_show', 0, C('PRIMARY_FILTER')),
			'is_channel' => I('post.is_channel', 0, C('PRIMARY_FILTER')),
			'access' => I('post.access', 0, C('PRIMARY_FILTER')),
			'url' => I('post.url'),
			'lang' => LANG_SET,
			);

		$id = $this->data($data)->add();

		action('category_add', $id);
		return true;
	}

	/**
	 * 获得要编辑的数据
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function getDataOne()
	{
		if ($error = illegal()) {
			return $error;
		}

		$map = array('c.id' => I('get.id', 0, C('PRIMARY_FILTER')));
		$join = array(
			'LEFT JOIN __CATEGORY__ AS cc ON c.pid=cc.id'
			);
		$field = array('c.*', 'cc.name' => 'parentname');
		$data = $this->table($this->tablePrefix . $this->tableName . ' AS c')
		->field($field)
		->join($join)
		->where($map)->find();
		$data['parentname'] = !empty($data['parentname']) ? $data['parentname'] : L('select_parent');
		return $data;
	}

	/**
	 * 编辑栏目
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('name', 'require', L('error_catename')),
			array('name', '', L('error_catename_unique'), 0, 'unique', 1),
			array('aliases', '', L('error_aliases_unique'), 2, 'unique', 1),
			array('aliases', 'english', L('error_aliases_abc'), 2),
			array('type_id', 'require', L('error_type')),
			array('type_id', 'number', L('error_type')),
			array('model_id', 'require', L('error_model')),
			array('model_id', 'number', L('error_model')),
			array('access', 'require', L('error_access')),
			array('access', 'number', L('error_access')),
			array('url', 'url', L('error_url'), 2),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', '', C('PRIMARY_FILTER'));
		$map = array(
			'id' => array('neq', $id),
			'name' => I('post.name'),
			);
		$unique = $this->where($map)->find();
		if (!empty($unique)) {
			return L('error_catename_unique');
		}

		$data = array(
			'pid' => I('post.pid', 0, C('PRIMARY_FILTER')),
			'name' => I('post.name'),
			'aliases' => I('post.aliases'),
			'seo_title' => I('post.seo_title'),
			'seo_keywords' => I('post.seo_keywords'),
			'seo_description' => I('post.seo_description'),
			'image' => I('post.image'),
			'type_id' => I('post.type_id', 0, C('PRIMARY_FILTER')),
			'model_id' => I('post.model_id', 0, C('PRIMARY_FILTER')),
			'is_show' => I('post.is_show', 0, C('PRIMARY_FILTER')),
			'is_channel' => I('post.is_channel', 0, C('PRIMARY_FILTER')),
			'access' => I('post.access', 0, C('PRIMARY_FILTER')),
			'url' => I('post.url'),
			'lang' => LANG_SET,
			);
		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('category_editor', $id);
		return true;
	}

	/**
	 * 删除栏目
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		if ($error = illegal()) {
			return $error;
		}

		$id = I('get.id', 0, C('PRIMARY_FILTER'));

		$map = array('pid' => $id);
		$parent = $this->field('id')->where($map)->find();
		if (!empty($parent)) {
			return L('error_remove');
		}

		$map = array('id' => $id);
		$this->where($map)->delete();

		action('category_remove', $id);
		return true;
	}

	/**
	 * 获得父级栏目数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getParent()
	{
		$map = array('id' => I('get.pid', 0, C('PRIMARY_FILTER')));
		$data = $this->field(array('id', 'pid', 'name'))->where($map)->find();
		$data['id'] = !empty($data['id']) ? $data['id'] : 0;
		$data['pid'] = !empty($data['pid']) ? $data['pid'] : 0;
		$data['name'] = !empty($data['name']) ? $data['name'] : L('select_parent');
		return $data;
	}

	/**
	 * 获得导航类型
	 * @access public
	 * @param
	 * @return array
	 */
	public function getType()
	{
		return array(
			array('id' => 1, 'name' => L('_ctype_top')),
			array('id' => 2, 'name' => L('_ctype_main')),
			array('id' => 3, 'name' => L('_ctype_foot')),
			array('id' => 4, 'name' => L('_ctype_other'))
			);
	}

	/**
	 * 获得模型
	 * @access public
	 * @param
	 * @return array
	 */
	public function getModel()
	{
		$map = array('status' => 1);
		return $this->table($this->tablePrefix . 'model')
		->where($map)
		->order('sort DESC')
		->select();
	}

	/**
	 * 获得会员等级
	 * @access public
	 * @param
	 * @return array
	 */
	public function getLevel()
	{
		$map = array('status' => 1);
		return $this->table($this->tablePrefix . 'level')
		->where($map)
		->select();
	}
}