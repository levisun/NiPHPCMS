<?php
/**
 *
 * 语言设置 - 系统设置 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: LangModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class LangModel extends Model
{
	protected $tableName = 'config';

	/**
	 * 获得语言设置数据
	 * @access public
	 * @param
	 * @return array
	 */
	public function getLang()
	{
		$data['LANG_AUTO_DETECT'] = C('LANG_AUTO_DETECT');
		$data['LANG_LIST'] = explode(',', C('LANG_LIST'));
		$data['SYS_DEFAULT_LANG'] = C('DEFAULT_LANG');
		$data['WEB_DEFAULT_LANG'] = include(CONF_PATH . 'lang.php');
		$data['WEB_DEFAULT_LANG'] = $data['WEB_DEFAULT_LANG']['DEFAULT_LANG'];
		return $data;
	}

	/**
	 * 修改语言设置
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function updateLang()
	{
		$rules = array(
			array('system', 'require', L('error_system_default_lang')),
			array('website', 'require', L('error_website_default_lang')),
			array('domain_auto', 'require', L('error_domain_auto')),
			);
		if ($this->validate($rules)->create() === false) {
			return $this->getError();
		}

		$data = array();
		$data['LANG_AUTO_DETECT'] = I('post.domain_auto') ? true : false;
		$data['LANG_LIST'] = C('LANG_LIST');
		$data['DEFAULT_LANG'] = I('post.website');
		file_put_contents(CONF_PATH . 'lang.php', '<?php defined(\'THINK_PATH\') or die(); return ' . var_export($data, true) . ';');

		$data = array();
		$data['DEFAULT_LANG'] = I('post.system');
		file_put_contents(THINK_PATH . APP_PATH . 'Admin/Conf/lang.php', '<?php defined(\'THINK_PATH\') or die(); return ' . var_export($data, true) . ';');

		action('config_update');
		return true;
	}
}