<?php
/**
 *
 * 模型 - 栏目 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ModelModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class ModelModel extends Model
{
	protected $tableName = 'model';
	protected $fields = array('id', 'name', 'tablename', 'status', 'remark', 'sort');
	protected $pk = 'id';
	protected $insertFields = array('name', 'tablename', 'status', 'remark', 'sort');
	protected $updateFields = array('name', 'tablename', 'status', 'remark', 'sort');

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
			$map = array('name' => array('LIKE', '%' . $key . '%'));
		}

		$count = $this->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$data['list'] =
		$this->field($this->fields)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('sort DESC')
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

		action('model_sort');
		return true;
	}

	/**
	 * 获得模型列表
	 * @access public
	 * @param
	 * @return array
	 */
	public function getModel()
	{
		$fields = array('id', 'name', 'tablename');
		$map = array(
			'tablename' => array(
				'in', 'article,picture,download,page,product,feedback,message'
				)
			);
		return $this->field($fields)
		->where($map)
		->order('sort DESC')
		->select();
	}

	/**
	 * 新增模型
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_name')),
			array('name', '', L('error_name_unique'), 0, 'unique', 1),
			array('tablename', 'require', L('error_tablename')),
			array('tablename', '', L('error_tablename_unique'), 0, 'unique', 1),
			array('tablename', 'english', L('error_tablename_abc'), 0),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			array('modeltable', 'require', L('error_modeltable')),
			array('modeltable', 'english', L('error_modeltable_abc'), 0),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'name' => I('post.name'),
			'tablename' => I('post.tablename'),
			'remark' => I('post.remark'),
			'status' => I('post.status')
			);
		$id = $this->data($data)->add();

		$tableRs = $this->query('SHOW CREATE TABLE `' . $this->tablePrefix . I('post.modeltable') . '`');
		$createTable = $tableRs[0]['create table'] . ';';
		$tableRs = $this->query('SHOW CREATE TABLE `' . $this->tablePrefix . I('post.modeltable') . '_data' . '`');
		$createTable .= $tableRs[0]['create table'] . ';';
		$createTable = str_ireplace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createTable);
		$createTable = str_ireplace(I('post.modeltable'), I('post.tablename'), $createTable);
		$this->execute($createTable);

		action('model_add', $id);
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
		$id = I('get.id', '', C('PRIMARY_FILTER'));

		$map = array('id' => $id);
		return $this->field($this->fields)->where($map)->find();
	}

	/**
	 * 新增节点
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('name', 'require', L('error_name')),
			array('name', '', L('error_name_unique'), 0, 'unique', 1),
			array('tablename', 'require', L('error_tablename')),
			array('tablename', '', L('error_tablename_unique'), 0, 'unique', 1),
			array('tablename', 'english', L('error_tablename_abc'), 0),
			array('status', 'require', L('error_status')),
			array('status', 'number', L('error_status')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'name' => I('post.name'),
			'remark' => I('post.remark'),
			'status' => I('post.status')
			);

		$id = I('get.id', '', C('PRIMARY_FILTER'));
		$map = array('id' => $id);
		$this->where($map)->data($data)->save();

		action('model_editor', $id);
		return true;
	}


	/**
	 * 删除模型
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		if ($error = illegal()) {
			return $error;
		}
		$id = I('get.id', '', C('PRIMARY_FILTER'));

		$map = array('id' => $id);
		$table = $this->field('tablename')->where($map)->find();

		$sql = 'DROP TABLE IF EXISTS `' . $this->tablePrefix . $table['tablename'] . '`;';
		$sql .= 'DROP TABLE IF EXISTS `' . $this->tablePrefix . $table['tablename'] . '_data`;';
		$this->execute($sql);

		$this->where($map)->delete();

		action('model_remove', $id);
		return true;
	}
}