<?php
/**
 *
 * 分类 - 栏目 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: TypeModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class TypeModel extends Model
{
	protected $tableName = 'type';
	protected $fields = array(
						'id',
						'category_id',
						'name',
						'description'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'category_id',
						'name',
						'description'
						);
	protected $updateFields = array(
						'category_id',
						'name',
						'description'
						);

	/**
	 * 获得分类数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getData()
	{
		$map = array();
		if ($key = I('get.key')) {
			$map = array('t.name' => array('LIKE', '%' . $key . '%'));
		}

		if ($cid = I('get.cid', 0, C('PRIMARY_FILTER'))) {
			$map['category_id'] = $cid;
		}

		$count = $this->table($this->tablePrefix . $this->tableName . ' AS t')
		->where($map)
		->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$field = array(
					't.id',
					't.category_id',
					't.name',
					't.description',
					'c.name' => 'cname',
					);
		$join = array(
			'__CATEGORY__ AS c ON c.id=t.category_id',
			);
		$data['list'] =
		$this->table($this->tablePrefix . $this->tableName . ' AS t')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('t.id DESC')
		->select();

		return $data;
	}

	/**
	 * 新增分类
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_typename')),
			array('category_id', 'isCategoryId', L('error_fieldscategory'), 1, 'function'),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$category_id = I('post.category_id', 0, C('PRIMARY_FILTER'));
		$category_id = array_pop($category_id);
		$data = array(
			'name' => I('post.name'),
			'category_id' => $category_id,
			'description' => I('post.description')
			);
		$id = $this->data($data)->add();

		action('type_add', $id);
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
					't.id',
					't.category_id',
					't.name',
					't.description',
					'c.name' => 'cname',
					);
		$join = array(
			'__CATEGORY__ AS c ON c.id=t.category_id',
			);
		$map = array('t.id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->table($this->tablePrefix . $this->tableName . ' AS t')
		->field($field)
		->join($join)
		->where($map)
		->find();
		return $this->field($this->fields)->where($map)->find();
	}

	/**
	 * 编辑分类
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('name', 'require', L('error_typename')),
			// array('category_id', 'isCategoryId', L('error_fieldscategory'), 1, 'function'),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		// $category_id = I('post.category_id', 0, C('PRIMARY_FILTER'));
		// $category_id = array_pop($category_id);
		$data = array(
			'name' => I('post.name'),
			// 'category_id' => $category_id,
			'description' => I('post.description')
			);
		$id = I('post.id', 0, C('PRIMARY_FILTER'));
		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('type_editor', $id);
		return true;
	}

	/**
	 * 删除分类
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

		$field = array('type_id');
		$map = array('type_id' => $id);
		$data = $this->table($this->tablePrefix . $data['tablename'])
		->field($field)
		->where($map)
		->data(array('type_id' => 0))
		->save();

		$map = array('id' => $id);
		$this->where($map)->delete();

		action('type_remove', $id);
		return true;
	}
}