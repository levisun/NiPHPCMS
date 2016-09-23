<?php
/**
 *
 * 标签
 *
 * @category   Common\Library
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: Label.class.php 2016-01 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 0.1
 */
namespace Common\Library;
use Think\Template\TagLib;
class Label extends TagLib
{
	// 标签定义
	protected $tags = array(
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'list' => array('attr'=>'id,num,order,com,top,hot', 'close'=>1, 'level'=>3, 'alias'=>'entry'),
		'nav' => array('attr'=>'type', 'close'=>1, 'level'=>3, 'alias'=>'category'),
		'sidebar' => array('attr'=>'id', 'close'=>1, 'level'=>3, 'alias'=>'subnav'),
		'ads' => array('attr'=>'id', 'close'=>1, 'level'=>3, 'alias'=>'guanggao'),
		'banner' => array('attr'=>'id', 'close'=>1, 'level'=>3, 'alias'=>'huandengpian'),
		'article' => array('attr'=>'id,cid', 'close'=>1, 'level'=>3, 'alias'=>'neirong'),
		'breadcrumb' => array('attr'=>'id', 'close'=>1, 'level'=>3, 'alias'=>'map'),
		);

	/**
	 * 文章标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _breadcrumb($tag, $content)
	{
		$cid = strip_tags(trim($tag['id']));
		if (empty($cid)) {
			return ;
		}
		$cid = $this->autoBuildVar($cid);
		eval('$cid = !empty(' . $cid . ') ? ' . $cid . ' : 0;');

		$map = array('id' => $cid, 'lang' => LANG_SET);
		$breadcrumb = $this->getParent($cid);

		$parseStr = '<?php ';
		$parseStr .= ' $label = ' . var_export($breadcrumb, true) . ';';
		$parseStr .= ' foreach ($label as $key => $vo) {';
		$parseStr .= ' $vo["url"] = U("home/index/entry", array("cid"=>$vo["id"]));?>';
		$parseStr .= $this->tpl->parse($content);
		$parseStr .= '<?php } unset($label); ?>';
		return $parseStr;
	}

	/**
	 * 文章标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _article($tag, $content)
	{
		$cid = floor(floatval(strip_tags(trim($tag['cid']))));
		$id = floor(floatval(strip_tags(trim($tag['id']))));
		if (empty($id) || empty($cid)) {
			return ;
		}

		$tableName = $this->getModelTable($cid);
		if (empty($tableName)) {
			return ;
		}

		$map = array(
			'id' => $id,
			'category_id' => $cid,
			'is_pass' => 1,
			'showtime' => array('ELT', time()),
			'recycle' => 0,
			'lang' => LANG_SET
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelarticle' . implode('', $tag) : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . $tableName)
		->where($map)
		->find();
		if (empty($data)) {
			return ;
		}

		$GLOBALS['article_' . $id] = $data;
		$parseStr = '<?php ';
		$parseStr .= ' $article = ' . var_export($data, true) . ';';
		$parseStr .= ' $article["url"] = U("home/index/entry", array("cid"=>$article["category_id"], "id"=>$article["id"]));?>';
		$parseStr .= $this->tpl->parse($content);
		return $parseStr;
	}

	/**
	 * 幻灯片标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _banner($tag, $content)
	{
		$id = floor(floatval(strip_tags(trim($tag['id']))));
		if (empty($id)) {
			return ;
		}

		$map = array('id' => $id, 'pid' => 0, 'lang' => LANG_SET);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelbanner' . implode('', $tag) : CACHE_KEY;
		$banner = M()->cache($CACHE)->table(C('DB_PREFIX') . 'banner')
		->where($map)
		->find();
		if (empty($banner)) {
			return ;
		}

		$map = array('pid' => $id, 'lang' => LANG_SET);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelbannerpid' . implode('', $tag) : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . 'banner')
		->where($map)
		->select();
		if (empty($data)) {
			return ;
		}

		$parseStr = '<?php ';
		$parseStr .= ' $label = ' . var_export($data, true) . ';';
		$parseStr .= ' foreach ($label as $key => $vo) {';
		$parseStr .= ' $vo["url"] = U("home/index/banner", array("id"=>$vo["id"]));';
		$parseStr .= ' $vo["width"] = ' . $banner['width'] . ';';
		$parseStr .= ' $vo["height"] = ' . $banner['height'] . ';?>';
		$parseStr .= $this->tpl->parse($content);
		$parseStr .= '<?php } unset($label); ?>';
		return $parseStr;
	}

	/**
	 * 广告标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _ads($tag, $content)
	{
		$id = floor(floatval(strip_tags(trim($tag['id']))));
		if (empty($id)) {
			return ;
		}

		$map = array(
			'id' => $id,
			'endtime' => array('EGT', time()),
			'starttime' => array('ELT', time()),
			'lang' => LANG_SET
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelads' . implode('', $tag) : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . 'ads')
		->where($map)
		->find();
		if (empty($data)) {
			return ;
		}

		$parseStr = '<?php ';
		$parseStr .= ' $ads = ' . var_export($data, true) . ';';
		$parseStr .= ' $ads["url"] = U("home/index/ads", array("id"=>$ads["id"]));?>';
		$parseStr .= $this->tpl->parse($content);
		return $parseStr;
	}

	/**
	 * list标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _list($tag, $content)
	{
		$id = strip_tags(trim($tag['id']));
		if (empty($id)) {
			return ;
		}
		if (false === strpos($id, ',') && !is_numeric($id)) {
			$id = $this->autoBuildVar($id);
			eval('$id = !empty(' . $id . ') ? ' . $id . ' : 0;');
		}


		$num = !empty($tag['num']) ?floor(floatval(strip_tags(trim($tag['num'])))) : 10;
		$order = !empty($tag['order']) ? strip_tags(trim($tag['order'])) : 'a.sort DESC, a.id DESC';

		$tableName = $this->getModelTable($id);
		if (empty($tableName)) {
			return ;
		}
		$map = array(
			'a.category_id' => array('in', "$id"),
			// 'a.category_id' => $id,
			'a.is_pass' => 1,
			'a.showtime' => array('ELT', time()),
			'a.recycle' => 0,
			'a.lang' => LANG_SET
			);

		// 推荐
		if (!empty($tag['com'])) {
			$map['a.is_com'] = 1;
		}
		// 置顶
		if (!empty($tag['top'])) {
			$map['a.is_top'] = 1;
		}
		// 最热
		if (!empty($tag['hot'])) {
			$map['a.is_hot'] = 1;
		}

		$field = array(
			'a.id', 'a.title', 'a.seo_title', 'a.seo_keywords', 'a.seo_description',
			'a.thumb', 'a.category_id', 'a.type_id', 'a.hits', 'a.comment',
			'a.username', 'a.url', 'a.is_link', 'a.addtime', 'a.updatetime',
			'a.access', 'l.name'=>'level_name', 'c.name'=>'cat_name', 't.name'=>'type_name'
			 );
		$join = array(
			'__CATEGORY__ AS c ON c.id=a.category_id',
			'LEFT JOIN __LEVEL__ AS l ON l.id=a.access',
			'LEFT JOIN __TYPE__ AS t ON t.id=a.type_id'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labellist' . implode('', $tag) : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . $tableName . ' AS a')
		->field($field)
		->join($join)
		->where($map)
		->order($order)
		->limit($num)
		->select();
		if (empty($data)) {
			return ;
		}
		foreach ($data as $key => $value) {
			$data[$key]['url'] = U('home/index/article', array('cid'=>$value['category_id'], 'id'=>$value['id']));
			$data[$key]['cat_url'] = U('home/index/entry', array('cid' => $value['category_id']));
			$data[$key]['comment_url'] = U('comment/index/entry', array('cid' => $value['category_id'], 'id' => $value['id']));
		}

		$parseStr = '<?php ';
		$parseStr .= ' $label = ' . var_export($data, true) . ';';
		$parseStr .= ' foreach ($label as $key => $vo) { ?>';
		$parseStr .= $this->tpl->parse($content);
		$parseStr .= '<?php } unset($label); ?>';
		return $parseStr;
	}

	/**
	 * nav标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _nav($tag, $content)
	{
		$type = !empty($tag['type']) ? strip_tags(trim($tag['type'])) : 'main';
		switch ($type) {
			case 'top':
				$type_id = 1;
				break;

			case 'foot':
				$type_id = 3;
				break;

			case 'other':
				$type_id = 4;
				break;

			default:
				$type_id = 2;
				break;
		}
		$map = array('type_id' => $type_id, 'is_show' => 1, 'pid' => 0, 'lang' => LANG_SET);
		$order = 'sort ASC,id DESC';
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelnav' . implode('', $tag) : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . 'category')
		->where($map)
		->order($order)
		->select();
		if (empty($data)) {
			return ;
		}
		$data = $this->GetChild($data);

		$parseStr = '<?php ';
		$parseStr .= ' $label = ' . var_export($data, true) . ';';
		$parseStr .= ' foreach ($label as $key => $vo) { ?>';
		$parseStr .= $this->tpl->parse($content);
		$parseStr .= '<?php } unset($label); ?>';
		return $parseStr;
	}

	/**
	 * sidebar标签解析 循环输出数据集
	 * @access public
	 * @param  array  $tag     标签属性
	 * @param  string $content 标签内容
	 * @return string|void
	 */
	public function _sidebar($tag, $content)
	{
		$cid = strip_tags(trim($tag['id']));
		if (empty($cid)) {
			return ;
		}
		$cid = $this->autoBuildVar($cid);
		eval('$cid = !empty(' . $cid . ') ? ' . $cid . ' : 0;');

		$cid = $this->ToParent($cid);

		$map = array('id' => $cid, 'is_show' => 1, 'pid' => 0, 'lang' => LANG_SET);
		$order = 'sort ASC,id DESC';
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelsidebar' . $cid : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . 'category')
		->where($map)
		->order($order)
		->select();
		if (empty($data)) {
			return ;
		}
		$data = $this->GetChild($data);

		$parseStr = '<?php ';
		$parseStr .= ' $label_sidebar_name = "' . $data[0]['name'] . '";';
		$parseStr .= ' $label = ' . var_export($data, true) . ';';
		$parseStr .= ' foreach ($label as $key => $vo) { ?>';
		$parseStr .= $this->tpl->parse($content);
		$parseStr .= '<?php } unset($label); ?>';
		return $parseStr;
	}

	/**
	 * 获得父级栏目
	 * @access private
	 * @param  intval $pid_
	 * @return intval
	 */
	private function getParent($pid_)
	{
		$breadcrumb = array();
		$map = array('id' => $pid_, 'lang' => LANG_SET);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelparent' . $pid_ : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . 'category')
		->where($map)
		->find();

		if (!empty($data['pid'])) {
			$breadcrumb = $this->getParent($data['pid']);
		}
		$breadcrumb[] = $data;
		return $breadcrumb;
	}

	/**
	 * 获得父级ID
	 * @access private
	 * @param  intval $cid_
	 * @return intval
	 */
	private function ToParent($cid_)
	{
		if (empty($cid_)) {
			return 0;
		}
		$map = array('id' => $cid_, 'lang' => LANG_SET);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labeltoparent' . $cid_ : CACHE_KEY;
		$parent = M()->cache($CACHE)->table(C('DB_PREFIX') . 'category')
		->field(array('id','pid'))
		->where($map)
		->find();
		if (!empty($parent['pid'])) {
			return $this->ToParent($parent['pid']);
		}
		return $parent['id'];
	}

	/**
	 * 获得子导航
	 * @access private
	 * @param  array $data_
	 * @return array
	 */
	private function GetChild($data_)
	{
		$id = array();
		$nav = array();
		foreach ($data_ as $key => $value) {
			$nav[$key]['name'] = $value['name'];
			$nav[$key]['aliases'] = $value['aliases'];
			$nav[$key]['seo_title'] = $value['seo_title'];
			$nav[$key]['seo_keywords'] = $value['seo_keywords'];
			$nav[$key]['seo_description'] = $value['seo_description'];
			$nav[$key]['image'] = $value['image'];
			$nav[$key]['access'] = $value['access'];
			$nav[$key]['url'] = U('home/index/entry', array('cid'=>$value['id']));

			// 查询子类
			$id[] = $value['id'];
			$map = array(
				'pid' => array('in', implode(',', $id)),
				'lang' => LANG_SET
				);
			$id = array();
			$order = 'sort ASC,id DESC';
			$CACHE = CACHE_KEY ? CACHE_KEY . 'labelchild' . implode('', $id) : CACHE_KEY;
			$child = M()->cache($CACHE)->table(C('DB_PREFIX') . 'category')
			->where($map)
			->order($order)
			->select();
			if (!empty($child)) {
				// 递归查询子类
				$c = $this->GetChild($child);
				$child = !empty($c) ? $c : $child;
				$nav[$key]['child'] = $child;
			}
		}
		return $nav;
	}

	/**
	 * 获得模型表
	 * @access public
	 * @param
	 * @return array
	 */
	private function getModelTable($cid_)
	{
		$map = array('c.id' => $cid_, 'c.lang' => LANG_SET);

		$field = array(
			'm.name' => 'model_name',
			);
		$join = array(
			'__MODEL__ AS m ON m.id=c.model_id AND m.name!=\'external\'',
			'LEFT JOIN __CATEGORY__ AS cc ON c.id=cc.pid'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'labelmodeltable' . $cid_ : CACHE_KEY;
		$data = M()->cache($CACHE)->table(C('DB_PREFIX') . 'category AS c')
		->field($field)
		->join($join)
		->where($map)
		->find();

		return $data['model_name'];
	}
}