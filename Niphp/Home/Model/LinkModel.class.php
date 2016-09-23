<?php
/**
 *
 * 友链页 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: LinkModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class LinkModel extends Model
{
	protected $tableName = 'link';
	protected $pk = 'id';
	protected $updateFields = array('hits');

	/**
	 * 获得友情链接
	 * @access public
	 * @param
	 * @return array
	 */
	public function getLink()
	{
		$map = array(
			'l.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'l.is_pass' => 1,
			'l.recycle' => 0,
			'l.lang' => LANG_SET
			);

		if ($tid = I('get.tid', 0, C('PRIMARY_FILTER'))) {
			$map['l.type_id'] = $tid;
		}

		$order = 'l.sort DESC, l.type_id ASC, l.updatetime DESC';
		$field = array(
			'l.id', 'l.logo', 'l.title', 'l.category_id', 'l.type_id', 'l.description',
			't.name' => 'type_name'
			);
		$join = array(
			'LEFT JOIN __TYPE__ AS t ON t.id=l.type_id'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'llist' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->table($this->tablePrefix . $this->tableName . ' AS l')
		->join($join)
		->where($map)
		->field($field)
		->order($order)
		->select();
		foreach ($data as $key => $value) {
			$data[$key]['url'] = U('link', array('cid' => $value['category_id'], 'id' => $value['id']));
		}
		return $data;
	}

	/**
	 * 跳转
	 * @access public
	 * @param
	 * @return void
	 */
	public function toRedirect()
	{
		$map = array(
			'id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'is_pass' => 1,
			'recycle' => 0,
			'lang' => LANG_SET
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'link' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->field('url')
		->where($map)
		->find();

		if (empty($data)) {
			redirect('404.html');
		}

		$this->field('hits')
		->where($map)
		->setInc('hits', 1, 30);

		redirect($data['url']);
	}
}