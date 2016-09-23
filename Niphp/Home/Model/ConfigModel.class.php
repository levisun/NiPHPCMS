<?php
/**
 *
 * 网站设置 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ConfigModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class ConfigModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 网站设置
	 * @access public
	 * @param
	 * @return array
	 */
	public function getConfig()
	{
		$map = array(
			'name' => array(
				'in', 'website_name,website_keywords,website_description,bottom_message,copyright,script,article_module_width,article_module_height,ask_module_width,ask_module_height,download_module_width,download_module_height,job_module_width,job_module_height,link_module_width,link_module_height,page_module_width,page_module_height,picture_module_width,picture_module_height,product_module_width,product_module_height,home_theme,member_theme,shop_theme'
				),
			'lang' => LANG_SET
			);

		$CACHE = CACHE_KEY ? CACHE_KEY . 'config' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->field($this->fields)
		->where($map)
		->select();
		$config = array();
		foreach ($data as $key => $value) {
			$config[$value['name']] = $value['value'];
			if ($value['name'] == 'bottom_message' OR $value['name'] == 'copyright' OR $value['name'] == 'script') {
				$config[$value['name']] = htmlspecialchars_decode($value['value']);
			}
		}

		return $config;
	}

	/**
	 * 获得模型表和栏目信息
	 * @access public
	 * @param
	 * @return array
	 */
	public function getModelTableCategory()
	{
		$cid = I('get.cid', 0, C('PRIMARY_FILTER'));
		if (!$cid) {
			return array();
		}
		$map = array(
			'c.id' => $cid,
			'c.lang' => LANG_SET
			);

		$field = array(
			'c.name',
			'c.aliases',
			'c.seo_title',
			'c.seo_keywords',
			'c.seo_description',
			'c.is_channel',
			'c.access',
			'c.url',
			'm.name' => 'model_name',
			);
		$join = array(
			'__MODEL__ AS m ON m.id=c.model_id',
			'LEFT JOIN __CATEGORY__ AS cc ON c.id=cc.pid'
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'mtc' : CACHE_KEY;
		$data = $this->cache($CACHE)
		->table($this->tablePrefix . 'category AS c')
		->cache(!APP_DEBUG)
		->field($field)
		->join($join)
		->where($map)
		->find();
		if (empty($data)) {
			redirect('404.html');
		}

		return $data;
	}
}