<?php
/**
 *
 * 图片设置 - 系统设置 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ImageModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class ImageModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 获得图片设置数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getImage()
	{
		$map = array(
			'name' => array(
				'in', 'auto_image,add_water,water_type,water_location,water_text,water_image,article_module_width,article_module_height,ask_module_width,ask_module_height,download_module_width,download_module_height,job_module_width,job_module_height,link_module_width,link_module_height,page_module_width,page_module_height,picture_module_width,picture_module_height,product_module_width,product_module_height'
				),
			'lang' => LANG_SET
			);
		$data = $this->field($this->fields)->where($map)->select();
		foreach ($data as $key => $value) {
			$arr[$value['name']] = $value['value'];
		}
		return $arr;
	}

	/**
	 * 修改图片设置
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateImage()
	{
		$rules = array(
			array('auto_image', 'require', L('error_image_auto_image')),
			array('auto_image', 'number', L('error_image_auto_image')),
			array('article_module_width', 'require', L('errot_image_article_module')),
			array('article_module_width', 'number', L('errot_image_article_module')),
			array('article_module_height', 'require', L('errot_image_article_module')),
			array('article_module_height', 'number', L('errot_image_article_module')),
			array('picture_module_width', 'require', L('errot_image_picture_module')),
			array('picture_module_width', 'number', L('errot_image_picture_module')),
			array('picture_module_height', 'require', L('errot_image_picture_module')),
			array('picture_module_height', 'number', L('errot_image_picture_module')),
			array('download_module_width', 'require', L('errot_image_download_module')),
			array('download_module_width', 'number', L('errot_image_download_module')),
			array('download_module_height', 'require', L('errot_image_download_module')),
			array('download_module_height', 'number', L('errot_image_download_module')),
			array('page_module_width', 'require', L('errot_image_page_module')),
			array('page_module_width', 'number', L('errot_image_page_module')),
			array('page_module_height', 'require', L('errot_image_page_module')),
			array('page_module_height', 'number', L('errot_image_page_module')),
			array('product_module_width', 'require', L('errot_image_product_module')),
			array('product_module_width', 'number', L('errot_image_product_module')),
			array('product_module_height', 'require', L('errot_image_product_module')),
			array('product_module_height', 'number', L('errot_image_product_module')),
			array('job_module_width', 'require', L('errot_image_job_module')),
			array('job_module_width', 'number', L('errot_image_job_module')),
			array('job_module_height', 'require', L('errot_image_job_module')),
			array('job_module_height', 'number', L('errot_image_job_module')),
			array('link_module_width', 'require', L('errot_image_link_module')),
			array('link_module_width', 'number', L('errot_image_link_module')),
			array('link_module_height', 'require', L('errot_image_link_module')),
			array('link_module_height', 'number', L('errot_image_link_module')),
			array('ask_module_width', 'require', L('errot_image_ask_module')),
			array('ask_module_width', 'number', L('errot_image_ask_module')),
			array('ask_module_height', 'require', L('errot_image_ask_module')),
			array('ask_module_height', 'number', L('errot_image_ask_module')),
			array('add_water', 'require', L('errot_image_add_water')),
			array('add_water', 'number', L('errot_image_add_water')),
			array('water_type', 'require', L('errot_image_water_type')),
			array('water_type', 'number', L('errot_image_water_type')),
			array('water_location', 'require', L('errot_image_water_location')),
			array('water_location', 'number', L('errot_image_water_location')),
			array('water_text', 'require', L('errot_image_water_text')),
			array('water_image', 'require', L('errot_image_water_image')),
			);
		$data = session('USER_DATA');
		if ($data['role_id'] == 1) {
			$rules[] = array('system_portal', 'require', L('error_safe_upload_file_type'));
		}
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		foreach ($_POST as $key => $value) {
			$map = array('name' => $key);
			$data = array('value' => I($key));
			$this->where($map)->data($data)->save();
		}

		action('config_update');
		return true;
	}
}