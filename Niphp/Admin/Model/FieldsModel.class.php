<?php
/**
 *
 * 自定义字段 - 栏目 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: FieldsModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class FieldsModel extends Model
{
	protected $tableName = 'fields';
	protected $fields = array(
						'id',
						'category_id',
						'type_id',
						'name',
						'description',
						'is_require'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'category_id',
						'type_id',
						'name',
						'description',
						'is_require'
						);
	protected $updateFields = array(
						'category_id',
						'type_id',
						'name',
						'description',
						'is_require'
						);

	/**
	 * 获得模型数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('f.name' => array('LIKE', '%' . $key . '%'));
		}

		if ($cid = I('get.cid', 0, C('PRIMARY_FILTER'))) {
			$map['category_id'] = $cid;
		}

		$count = $this->table($this->tablePrefix . $this->tableName . ' AS f')
		->where($map)
		->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();


		$field = array(
					'f.id',
					'f.category_id',
					'f.type_id',
					'f.name',
					'f.description',
					'f.is_require',
					'c.name' => 'cname',
					't.name' => 'tname'
					);
		$join = array(
			'__CATEGORY__ AS c ON c.id=f.category_id',
			'__FIELDS_TYPE__ AS t ON t.id=f.type_id'
			);

		$data['list'] =
		$this->table($this->tablePrefix . $this->tableName . ' AS f')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('f.id DESC')
		->select();

		return $data;
	}

	/**
	 * 新增字段
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_fieldsname')),
			array('category_id', 'isCategoryId', L('error_fieldscategory'), 1, 'function'),
			array('type_id', 'require', L('error_fieldstype')),
			array('type_id', 'number', L('error_fieldstype')),
			array('is_require', 'require', L('error_isrequire')),
			array('is_require', 'number', L('error_isrequire')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$category_id = I('post.category_id', 0, C('PRIMARY_FILTER'));
		$category_id = array_pop($category_id);
		$data = array(
			'name' => I('post.name'),
			'category_id' => $category_id,
			'type_id' => I('post.type_id', 0, C('PRIMARY_FILTER')),
			'is_require' => I('post.is_require', 0, C('PRIMARY_FILTER')),
			'description' => I('post.description')
			);
		$id = $this->data($data)->add();

		action('fields_add', $id);
		return true;
	}

	/**
	 * 获得将要修改的数据
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function getDataOne()
	{
		if ($error = illegal()) {
			return $error;
		}

		$field = array(
					'f.id',
					'f.category_id',
					'f.type_id',
					'f.name',
					'f.description',
					'f.is_require',
					'c.name' => 'cname'
					);
		$join = array(
			'__CATEGORY__ AS c ON c.id=f.category_id',
			);
		$map = array('f.id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->table($this->tablePrefix . $this->tableName . ' AS f')
		->field($field)
		->join($join)
		->where($map)
		->find();
		return $this->field($this->fields)->where($map)->find();
	}

	/**
	 * 编辑字段
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('name', 'require', L('error_fieldsname')),
			array('type_id', 'require', L('error_fieldstype')),
			array('type_id', 'number', L('error_fieldstype')),
			array('is_require', 'require', L('error_isrequire')),
			array('is_require', 'number', L('error_isrequire')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'name' => I('post.name'),
			// 'type_id' => I('post.type_id', 0, C('PRIMARY_FILTER')),
			'is_require' => I('post.is_require', 0, C('PRIMARY_FILTER')),
			'description' => I('post.description')
			);
		$id = I('post.id', 0, C('PRIMARY_FILTER'));
		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('fields_editor', $id);
		return true;
	}

	/**
	 * 删除字段
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

		$field = array('m.tablename');
		$join = array(
			'__CATEGORY__ AS c ON c.id=f.category_id',
			'__MODEL__ AS m ON m.id=c.model_id'
			);
		$map = array('f.id' => $id);
		$data = $this->table($this->tablePrefix . $this->tableName . ' AS f')
		->field($field)
		->join($join)
		->where($map)
		->find();

		$map = array('fields_id' => $id);
		$this->table($this->tablePrefix . $data['tablename'] . '_data')
		->where($map)
		->delete();

		$map = array('id' => $id);
		$this->where($map)->delete();

		action('fields_remove', $id);
		return true;
	}

	/**
	 * 获得栏目
	 * @access public
	 * @param
	 * @return array
	 */
	public function getCategory($pid_=0)
	{
		$map = array('pid' => $pid_);
		return $this->table($this->tablePrefix . 'category')
		->where($map)->select();
	}

	/**
	 * 获得字段类型
	 * @access public
	 * @param
	 * @return array
	 */
	public function getType()
	{
		$field = array('id', 'name');
		return $this->table($this->tablePrefix . 'fields_type')
		->field($field)
		->select();
	}
}