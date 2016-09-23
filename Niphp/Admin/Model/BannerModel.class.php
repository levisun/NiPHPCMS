<?php
/**
 *
 * 幻灯片 - 内容 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: BannerModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class BannerModel extends Model
{
	protected $tableName = 'banner';
	protected $fields = array(
						'id',
						'pid',
						'name',
						'title',
						'width',
						'height',
						'image',
						'url',
						'sort',
						'lang'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'pid',
						'name',
						'title',
						'width',
						'height',
						'image',
						'url',
						'sort',
						'lang'
						);
	protected $updateFields = array(
						'pid',
						'name',
						'title',
						'width',
						'height',
						'image',
						'url',
						'sort',
						'lang'
						);

	/**
	 * 获得幻灯片数据
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

		$map['pid'] = I('get.pid', 0, C('PRIMARY_FILTER'));
		$map['lang'] = LANG_SET;

		$count = $this->where($map)->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$data['list'] =
		$this->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('id DESC')
		->select();

		return $data;
	}

	/**
	 * 添加幻灯片
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		if (I('post.pid')) {
			$rules = array(
				array('pid', 'require', L('illegal_operation')),
				array('title', 'require', L('error_bannetitle')),
				array('image', 'require', L('error_banneimage')),
				array('url', 'url', L('error_banneurl')),
				);
			if ($this->validate($rules)->create() === false) {
				return $this->getError();
			}

			$data = array(
				'pid' => I('post.pid', 0, C('PRIMARY_FILTER')),
				'title' => I('post.title'),
				'image' => I('post.image'),
				'url' => I('post.url'),
				'lang' => LANG_SET
				);
			$id = $this->data($data)->add();

			action('banner_image_add', $id);
		} else {
			$rules = array(
				array('name', 'require', L('error_bannername')),
				array('name', '', L('error_bannername_unique'), 0, 'unique', 1),
				array('width', 'require', L('error_width')),
				array('width', 'number', L('error_width')),
				array('height', 'require', L('error_height')),
				array('height', 'number', L('error_height')),
				);
			if ($this->validate($rules)->create() === false) {
				return $this->getError();
			}

			$data = array(
				'name' => I('post.name'),
				'width' => I('post.width', 0, 'intval'),
				'height' => I('post.height', 0, 'intval'),
				'pid' => 0,
				'lang' => LANG_SET
				);
			$id = $this->data($data)->add();

			action('banner_add', $id);
		}
		return true;
	}

	/**
	 * 获得要编辑的数据
	 * @access public
	 * @param
	 * @retunr mixed
	 */
	public function getDataOne()
	{
		if ($error = illegal()) {
			return $error;
		}

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->where($map)->find();
	}

	/**
	 * 编辑幻灯片
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		if (I('post.pid')) {
			$rules = array(
				array('id', 'require', L('illegal_operation')),
				array('pid', 'require', L('illegal_operation')),
				array('title', 'require', L('error_bannetitle')),
				array('image', 'require', L('error_banneimage')),
				array('url', 'url', L('error_banneurl')),
				);
			if ($this->validate($rules)->create() === false) {
				return $this->getError();
			}

			$id = I('post.id', 0, C('PRIMARY_FILTER'));
			$map = array('id' => $id);

			$data = array(
				'title' => I('post.title'),
				'image' => I('post.image'),
				'url' => I('post.url'),
				);
			$this->where($map)->data($data)->save();

			action('banner_image_editor', $id);
		} else {
			$rules = array(
				array('id', 'require', L('illegal_operation')),
				array('name', 'require', L('error_name')),
				array('name', '', L('error_name_unique'), 0, 'unique', 1),
				array('width', 'require', L('error_width')),
				array('width', 'number', L('error_width')),
				array('height', 'require', L('error_height')),
				array('height', 'number', L('error_height')),
				);
			if ($this->validate($rules)->create() === false) {
				return $this->getError();
			}

			$id = I('post.id', 0, C('PRIMARY_FILTER'));
			$map = array('id' => $id);

			$data = array(
				'name' => I('post.name'),
				'width' => I('post.width', 0, 'intval'),
				'height' => I('post.height', 0, 'intval'),
				);

			$this->where($map)->data($data)->save();

			action('banner_editor', $id);
		}
		return true;
	}

	/**
	 * 删除幻灯片
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		$data = $this->getDataOne();

		$id = I('get.id', '', C('PRIMARY_FILTER'));

		$map = array('id' => $id);
		$this->where($map)->delete();

		if ($data['pid'] == 0) {
			$map = array('pid' => $id);
			$this->where($map)->delete();

			action('banner_remove', $id);
		} else {
			action('banner_image_remove', $id);
		}

		return true;
	}
}