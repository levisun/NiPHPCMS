<?php
/**
 *
 * 广告 - 内容 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: AdsModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class AdsModel extends Model
{
	protected $tableName = 'ads';
	protected $fields = array(
						'id',
						'name',
						'width',
						'height',
						'image',
						'url',
						'starttime',
						'endtime',
						'addtime',
						'lang'
						);
	protected $pk = 'id';
	protected $insertFields = array(
						'name',
						'width',
						'height',
						'image',
						'url',
						'starttime',
						'endtime',
						'addtime',
						'lang'
						);
	protected $updateFields = array(
						'id',
						'name',
						'width',
						'height',
						'image',
						'url',
						'starttime',
						'endtime',
						);

	/**
	 * 获得广告数据
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
	 * 添加广告
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added()
	{
		$rules = array(
			array('name', 'require', L('error_adsname')),
			array('width', 'require', L('error_size')),
			array('width', 'number', L('error_size')),
			array('height', 'require', L('error_size')),
			array('height', 'number', L('error_size')),
			array('starttime', 'require', L('error_time')),
			array('endtime', 'require', L('error_time')),
			// array('image', 'require', L('error_adsimage')),
			array('url', 'url', L('error_adsurl')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array(
			'name' => I('post.name'),
			'width' => I('post.width', 0, 'intval'),
			'height' => I('post.height', 0, 'intval'),
			'starttime' => I('post.starttime', 0, 'strtotime'),
			'endtime' => I('post.endtime', 0, 'strtotime'),
			'addtime' => time(),
			'image' => I('post.image'),
			'url' => I('post.url'),
			'lang' => LANG_SET
			);
		$id = $this->data($data)->add();

		action('ads_add', $id);
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

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		return $this->where($map)->find();
	}

	/**
	 * 编辑广告
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function editor()
	{
		$rules = array(
			array('id', 'require', L('illegal_operation')),
			array('name', 'require', L('error_adsname')),
			array('width', 'require', L('error_size')),
			array('width', 'number', L('error_size')),
			array('height', 'require', L('error_size')),
			array('height', 'number', L('error_size')),
			array('starttime', 'require', L('error_time')),
			array('endtime', 'require', L('error_time')),
			// array('image', 'require', L('error_adsimage')),
			array('url', 'url', L('error_adsurl')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$id = I('id', 0, C('PRIMARY_FILTER'));
		$map = array('id' => $id);

		$data = array(
			'name' => I('post.name'),
			'width' => I('post.width', 0, 'intval'),
			'height' => I('post.height', 0, 'intval'),
			'starttime' => I('post.starttime', 0, 'strtotime'),
			'endtime' => I('post.endtime', 0, 'strtotime'),
			'image' => I('post.image'),
			'url' => I('post.url'),
			);
		$id = $this->where($map)->data($data)->save();

		action('ads_editor', $id);
		return true;
	}

	/**
	 * 删除广告
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
		$map = array('get.id' => $id);

		$this->where($map)->delete();

		action('ads_remove', $id);
		return true;
	}
}