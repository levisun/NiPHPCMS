<?php
/**
 *
 * 反馈 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: FeedbackModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class FeedbackModel extends Model
{
	protected $tableName = 'feedback';
	protected $pk = 'id';
	protected $insertFields = array(
							'title',
							'username',
							'content',
							'category_id',
							'type_id',
							'mebmer_id',
							'addtime',
							'updatetime',
							'lang'
							);

	/**
	 * 添加反馈
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('username', 'require', L('error_form_username')),
			array('title', 'require', L('error_form_title')),
			array('content', 'require', L('error_form_content')),
			array('cid', 'require', L('error_form_cid')),
			);

		// 自定义字段验证
		$fields = $this->getFields();
		$fieldsName = array();
		foreach ($fields as $key => $value) {
			if ($value['is_require']) {
				$this->fields[] = 'fields-' . $value['id'];
				$this->insertFields[] = 'fields-' . $value['id'];
				$this->updateFields[] = 'fields-' . $value['id'];
				$rules[] = array(
					'fields-' . $value['id'],
					$value['type_regex'],
					$value['name'] . L('error_empty')
					);
				$fieldsName[$value['id']]['name'] = 'fields-' . $value['id'];
				$fieldsName[$value['id']]['type'] = $value['type_name'];
			}
		}

		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'title' => I('post.title'),
			'username' => I('post.username'),
			'content' => I('post.content'),
			'category_id' => I('post.cid', 0, C('PRIMARY_FILTER')),
			'type_id' => I('post.tid', 0, C('PRIMARY_FILTER')),
			'mebmer_id',
			'addtime' => time(),
			'updatetime' => time(),
			'lang' => LANG_SET
			);
		$id = $this->data($data)->add();

		$fields = array('main_id', 'fields_id', 'data');
		$fieldsData = array();
		foreach ($fieldsName as $key => $value) {
			$fieldsData['main_id'] = $id;
			$fieldsData['fields_id'] = $key;
			$filter = $value['type'] == 'date' ? 'strtotime' : C('DEFAULT_FILTER');
			$fieldsData['data'] = I($value['name'], '', $filter);

			$this->table($this->tablePrefix . $this->tableName . '_data')
			->field($fields)
			->data($fieldsData)
			->add();
		}

		return true;
	}

	/**
	 * 获得自定义字段
	 * @access public
	 * @param
	 * @return array
	 */
	public function getFields()
	{
		// 自定义字段
		$field = array(
			'f.id',
			'f.name' => 'name',
			'f.description',
			'f.is_require',
			'ft.name' => 'field_type',
			'ft.regex' => 'type_regex'
			);
		$join = array('__FIELDS_TYPE__ AS ft ON ft.id=f.type_id');
		$map = array('f.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')));

		$CACHE = CACHE_KEY ? CACHE_KEY . 'ffields' : CACHE_KEY;
		$fields = $this->cache($CACHE)
		->table($this->tablePrefix . 'fields AS f')
		->field($field)
		->join($join)
		->where($map)
		->select();
		return $fields;
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

		$CACHE = !APP_DEBUG ? 'type' . I('get.cid') . LANG_SET : false;
		$type = $this->cache($CACHE)->table($this->tablePrefix . 'type')
		->where($map)
		->select();
		return $type;
	}
}