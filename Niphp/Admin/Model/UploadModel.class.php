<?php
/**
 *
 * 上传 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: UploadModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class UploadModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';
	protected $updateFields = array('value');

	/**
	 * 上传文件
	 * @access public
	 * @param  string  $dir_      保存目录
	 * @param  intval  $width_    缩略图宽
	 * @param  intval  $height_   缩略图高
	 * @param  boolean $isWater_  是否加水印
	 * @param  boolean $isDelete_ 是否删除原始图片
	 * @param  string  $extname_  缩略图扩展名
	 * @return mixed
	 */
	public function upload($dir_='', $width_=0, $height_=0, $isWater_=false, $isDelete_=false, $extname_='_thumb')
	{
		$map = array(
			'name' => array('in', 'upload_file_max,upload_file_type'),
			'lang' => 'niphp',
			);
		$data = $this->field('name, value')
		->where($map)
		->limit(2)
		->select();
		foreach ($data as $key => $value) {
			$config[$value['name']] = $value['value'];
		}

		$upload = new \Think\Upload();
		$upload->rootPath = './Uploads/' . $dir_;
		$upload->maxSize = $config['upload_file_max'] * 1024 * 1024;
		$upload->exts = explode('|', $config['upload_file_type']);
		$upload->autoSub = true;
		$upload->subName = array('date','Ym');
		$info = $upload->upload();
		if ($info) {
			$num = 0;
			foreach ($info as $value) {
				$filename = $upload->rootPath . $value['savepath'] . $value['savename'];
				$file[$num]['thumb'] = $this->thumb($filename, $width_, $height_, $isWater_, $isDelete_, $extname_);
				if (!$isDelete_) {
					$file[$num]['file'] = $filename;
				}
				$num++;
			}
			action('upload_file');
			return $file;
		} else {
			action('upload_file');
			return $upload->getError();
		}
	}

	/**
	 * 缩略图与水印
	 * @access protected
	 * @param  string  $file_     要处理的图片
	 * @param  intval  $width_    缩略图宽
	 * @param  intval  $height_   缩略图高
	 * @param  boolean $isWater_  是否加水印
	 * @param  boolean $isDelete_ 是否删除原始图片
	 * @param  string  $extname_  缩略图扩展名
	 * @return void
	 */
	protected function thumb($file_, $width_=0, $height_=0, $isWater_=false, $isDelete_=false, $extname_='_thumb')
	{
		$image = new \Think\Image();
		$image->open($file_);

		if ($width_ && $height_) {
			$pathinfo = pathinfo($file_);
			$savedir = $pathinfo['dirname'] . '/';
			$savename = $pathinfo['filename'] . $extname_ . '.' . $pathinfo['extension'];
			if (!is_dir($savedir)) {
				mkdir($savedir, 0777, true);
			}
			$image->thumb($width_, $height_, \Think\Image::IMAGE_THUMB_FILLED)
			->save($savedir . $savename);
			if ($isWater_) {
				// 查询水印
				$map = array('name' => 'water_image');
				$water = $this->field('name, value')
				->where($map)
				->find();

				$image->open($savedir . $savename)
				->water($water['value'])
				->save($savedir . $savename);
			}
			if ($isDelete_) {
				unlink($file_);
			}
			return $savedir . $savename;
		}
		if ($isWater_) {
			// 查询水印
			$map = array('name' => 'water_image');
			$water = $this->field('name, value')
			->where($map)
			->find();

			$image->open($file_)
			->water($water['value'])
			->save($file_);
		}
		return $file_;
	}
}