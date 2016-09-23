<?php
/**
 *
 * 广告 - 模型
 *
 * @category   Home\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: AdsModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Model;
use Think\Model;
class AdsModel extends Model
{
	protected $tableName = 'ads';
	protected $pk = 'id';
	protected $updateFields = array('hits');

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
			'lang' => LANG_SET
			);
		$CACHE = CACHE_KEY ? CACHE_KEY . 'ads': CACHE_KEY;
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