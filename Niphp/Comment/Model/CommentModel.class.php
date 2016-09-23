<?php
/**
 *
 * 评论操作 - 模型
 *
 * @category   Comment\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: CommentModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Comment\Model;
use Think\Model;
class CommentModel extends Model
{
	protected $tableName = 'comment';
	protected $fields = array(
					'id',
					'category_id',
					'content_id',
					'user_id',
					'pid',
					'content',
					'is_pass',
					'is_report',
					'support',
					'reporttime',
					'ip',
					'ipattr',
					'addtime',
					'lang',
					'verify',
					'comment_id'
					);
	protected $pk = 'id';
	protected $insertFields = array(
					'category_id',
					'content_id',
					'user_id',
					'pid',
					'content',
					'ip',
					'ipattr',
					'addtime',
					'lang',
					'verify',
					'comment_id'
					);
	protected $updateFields = array(
					'support',
					'is_report',
					'reporttime',
					'verify',
					'comment_id'
					);

	/**
	 * 添加评论
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function added($tableName_)
	{
		$rules = array(
			array('category_id', 'require', L('illegal_operation')),
			array('content_id', 'require', L('illegal_operation')),
			array('content', 'require', L('error_content')),
			array('verify', 'require', L('error_verify')),
			array('verify', 'check_verify', L('error_confirm_verify'), 1, 'function'),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$dir = '../../../../';
		$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
		$area = $ip->getlocation();

		$data = array(
			'category_id' => I('post.category_id', 0, C('PRIMARY_FILTER')),
			'content_id' => I('post.content_id', 0, C('PRIMARY_FILTER')),
			'pid' => I('post.pid', 0, C('PRIMARY_FILTER')),
			'content' => I('post.content'),
			'ip' => $area['ip'],
			'ipattr' => $area['country'] . $area['area'],
			'addtime' => time(),
			'lang' => LANG_SET
			);
		$data['user_id'] = cookie(C('USER_AUTH_KEY')) ? cookie(C('USER_AUTH_KEY')) : 0;
		$id = $this->data($data)->add();

		// 自增评论量
		$map = array(
			'id' => I('post.content_id', 0, C('PRIMARY_FILTER')),
			'category_id' =>  I('post.category_id', 0, C('PRIMARY_FILTER')),
			'lang' => LANG_SET
			);
		$this->table($this->tablePrefix . $tableName_)
		->field('comment')
		->where($map)
		->setInc('comment', 1);

		return true;
	}

	/**
	 * 评论列表信息
	 * @access public
	 * @param
	 * @return void
	 */
	public function getList($tableName_)
	{
		$map = array(
			'a.id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'a.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'a.is_pass' => 1,
			'a.recycle' => 0,
			'a.lang' => LANG_SET
			);
		$field = array(
			'a.id',
			'a.title',
			'a.seo_title',
			'a.seo_keywords',
			'a.seo_description',
			'a.category_id',
			'a.comment',
			'a.hits',
			'a.updatetime',
			'c.name' => 'cat_name'
			);
		$join = array(
			'__CATEGORY__ AS c ON c.id=a.category_id',
			'LEFT JOIN __LEVEL__ AS l ON l.id=a.access',
			'LEFT JOIN __TYPE__ AS t ON t.id=a.type_id'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'comment_article' . $tableName_ : CACHE_KEY;
		$article = $this->cache($CACHE)
		->table($this->tablePrefix . $tableName_ . ' AS a')
		->field($field)
		->join($join)
		->where($map)->find();
		if (empty($article)) {
			return L('illegal_operation');
		}
		$data['article'] = $article;
		$map = array(
			'c.category_id' => I('get.cid', 0, C('PRIMARY_FILTER')),
			'c.content_id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'c.is_pass' => 1,
			'c.lang' => LANG_SET
			);
		$field = array(
			'c.id',
			'c.category_id',
			'c.content_id',
			'c.user_id',
			'c.pid',
			'c.content',
			'c.is_pass',
			'c.is_report',
			'c.support',
			'c.ip',
			'c.ipattr',
			'c.addtime',
			'c.lang',
			'm.username'
			);
		$join = array(
			'LEFT JOIN __MEMBER__ AS m ON m.id=c.user_id',
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'comment_count' . $tableName_ : CACHE_KEY;
		$count = $this->cache($CACHE)
		->table($this->tablePrefix . $this->tableName . ' AS c')
		->where($map)
		->count();
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$CACHE = CACHE_KEY ? CACHE_KEY . 'comment_list' . $tableName_ : CACHE_KEY;
		$list = $this->cache($CACHE)
		->table($this->tablePrefix . $this->tableName . ' AS c')
		->field($field)
		->join($join)
		->where($map)
		->limit($page->firstRow . ',' . $page->listRows)
		->order('c.id DESC')
		->select();
		foreach ($list as $key => $value) {
			$list[$key]['content'] = $value['is_report'] >= 1000 ? L('comment_error') : $value['content'];
		}
		$data['list'] = $list;

		return $data;
	}

	/**
	 * 支持
	 * @access public
	 * @param
	 * @return void
	 */
	public function support()
	{
		$dir = '../../../../';
		$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
		$area = $ip->getlocation();

		$data = array(
			'comment_id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'ip' => $area['ip'],
			'ipattr' => $area['country'] . $area['area'],
			);
		$data['user_id'] = cookie(C('USER_AUTH_KEY')) ? cookie(C('USER_AUTH_KEY')) : 0;
		$tableName = $this->tablePrefix . $this->tableName . '_support';
		$isSupport = $this->table($tableName)
		->where($data)->find();

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		if (!empty($isSupport)) {
			$this->table($tableName)
			->where($data)->delete();
			$this->where($map)->where($map)->setInc('support', -1);
		} else {
			$data['time'] = time();
			$this->table($tableName)
			->data($data)->add();
			$this->where($map)->where($map)->setInc('support', 1);
		}
	}

	/**
	 * 支持
	 * @access public
	 * @param
	 * @return void
	 */
	public function report()
	{
		$dir = '../../../../';
		$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
		$area = $ip->getlocation();

		$data = array(
			'comment_id' => I('get.id', 0, C('PRIMARY_FILTER')),
			'ip' => $area['ip'],
			'ipattr' => $area['country'] . $area['area'],
			);
		$data['user_id'] = cookie(C('USER_AUTH_KEY')) ? cookie(C('USER_AUTH_KEY')) : 0;
		$tableName = $this->tablePrefix . $this->tableName . '_report';
		$isSupport = $this->table($tableName)
		->where($data)->find();

		$map = array('id' => I('get.id', 0, C('PRIMARY_FILTER')));
		if (empty($isSupport)) {
			$data['time'] = time();
			$this->table($tableName)
			->data($data)->add();
			$this->where($map)->where($map)->setInc('is_report', 1);
		}
	}
}