<?php
/**
 *
 * 节点 - 用户管理 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: RecycleModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class RecycleModel extends Model
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
						'tag_id',
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
						);
	protected $pk = 'id';
	protected $updateFields = array(
						'recycle',
						);

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
		$map['recycle'] = 1;
		$map['lang'] = LANG_SET;

		// 模型表
		$tableName = $this->getModelTable();

		$count = $this->table($this->tablePrefix . $tableName)->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$data['list'] =
		$this->table($this->tablePrefix . $tableName)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('updatetime DESC')
		->select();

		return $data;
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

		return $data;
	}

	/**
	 * 编辑内容
	 * 还原内容
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
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('post.id', 0, C('PRIMARY_FILTER'));
		$map = array('id' => $id);
		$data = array('recycle' => 0);
		$this->table($this->tablePrefix . $tableName)
		->where($map)
		->data($data)
		->save();

		action('content_reduction', $id, L('_model_' . $tableName));
		return true;
	}

	/**
	 * 删除内容
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		$data = $this->getDataOne();
		if (is_string($data)) {
			return $data;
		}

		Vendor('File#class', COMMON_PATH . 'Library');

		// 删除友链LOGO图
		if (!empty($data['logo'])) {
			\File::delete($data['logo']);
		}
		// 删除缩略图
		if (!empty($data['thumb'])) {
			\File::delete($data['thumb']);
		}
		// 删除相册图片
		if (!empty($data['albumData'])) {
			foreach ($data['albumData'] as $key => $value) {
				\File::delete($value['image']);
				\File::delete($value['thumb']);
			}
		}
		// 删除内容中的图片
		if (!empty($data['content'])) {
			preg_match_all('/<img(.*?)src=("|\'|\s)?(.*?)(?="|\'|\s)/', $data['content'], $matches);
			foreach ($matches[3] as $value) {
				\File::delete($value);
			}
		}

		$this->table($this->tablePrefix . 'tags_article')
		->where(array('article_id' => $data['id']))->delete();

		// 模型表
		$tableName = $this->getModelTable();

		$map = array('id' => $data['id']);
		$this->table($this->tablePrefix . $tableName)
		->where($map)->delete();

		if ($tableName != 'link') {
			$map = array('main_id' => $data['id']);
			$this->table($this->tablePrefix . $tableName . '_data')
			->where($map)->delete();
		}

		action('content_remove', $id, L('_model_' . $tableName));
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
}