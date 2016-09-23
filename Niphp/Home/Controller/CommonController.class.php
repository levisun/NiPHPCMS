<?php
/**
 *
 * 网站全局 - 控制器
 *
 * @category   Home\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: CommonController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller
{
	protected $config = array();
	protected $category = array();

	protected function _initialize()
	{
		$CACHE_KEY = LANG_SET . MODULE_NAME . CONTROLLER_NAME . ACTION_NAME . implode('', $_GET);
		$CACHE_KEY = !APP_DEBUG ? $CACHE_KEY : false;
		define('CACHE_KEY', $CACHE_KEY);

		D('Member/Account')->isUserLogin();

		// 用户信息
		$this->assign('__USER__', cookie('USER_DATA'));

		$this->config = D('Home/Config')->getConfig();
		$this->category = D('Home/Config')->getModelTableCategory();
		if (empty($this->config)) {
			redirect(U('home/index/index'));
		}

		$theme = MODULE_NAME;
		if (in_array(MODULE_NAME, array('Home', 'Comment'))) {
			$theme = 'Home';
		}

		$theme .= '/' . $this->config['home_theme'];

		// 安访问来源切换模板
		if (is_wechat() && is_dir('./Themes/' . $theme . '/wechat')) {
			$theme .= '/wechat';
		} elseif (is_mobile()) {
			$theme .= '/mobile';
		} else {
			$theme .= '/pc';
		}

		// $this->theme($theme);
		C('DEFAULT_THEME', $theme);

		// 网站常用变量
		$domain = domain();
		C('TMPL_PARSE_STRING.__STATIC__', $domain . 'Static/');
		C('TMPL_PARSE_STRING.__THEME__', $domain . 'Themes/' . $theme . '/');
		C('TMPL_PARSE_STRING.__CSS__', $domain . 'Themes/' . $theme . '/css/');
		C('TMPL_PARSE_STRING.__JS__', $domain . 'Themes/' . $theme . '/js/');
		C('TMPL_PARSE_STRING.__IMG__', $domain . 'Themes/' . $theme . '/img/');

		// 初始化网站信息
		C('TMPL_PARSE_STRING.__TITLE__', $this->config['website_name']);
		C('TMPL_PARSE_STRING.__KEYWORDS__', $this->config['website_keywords']);
		C('TMPL_PARSE_STRING.__DESCRIPTION__', $this->config['website_description']);
		C('TMPL_PARSE_STRING.__BOTTOM_MESSAGE__', $this->config['bottom_message']);
		C('TMPL_PARSE_STRING.__COPYRIGHT__', $this->config['copyright']);
		C('TMPL_PARSE_STRING.__SCRIPT__', $this->config['script']);

		// 域名
		C('TMPL_PARSE_STRING.__DOMAIN__', $domain);

		C('TMPL_PARSE_STRING.__MEMBER_URL__', U('member/index/my'));
		C('TMPL_PARSE_STRING.__MEMBER_REG__', U('member/account/reg'));
		C('TMPL_PARSE_STRING.__MEMBER_LOGOUT__', U('member/account/logout'));

		removeLogs();
		visit();
		searchengine();
	}

	/**
	 * 重置网站标题、关键词与描述
	 * @access protected
	 * @param  array $category_ 栏目数据
	 * @param  array $article_  文章数据
	 * @return void
	 */
	protected function ToTKD($category_, $article_=array())
	{
		$title = $this->config['website_name'];
		if (!empty($category_['seo_title'])) {
			$title = $category_['seo_title'] . ' - ' . $title;
		} elseif (!empty($category_['name'])) {
			$title = $category_['name'] . ' - ' . $title;
		}

		if (!empty($article_['seo_title'])) {
			$title = $article_['seo_title'] . ' - ' . $title;
		} elseif (!empty($article_['title'])) {
			$title = $article_['title'] . ' - ' . $title;
		}

		C('TMPL_PARSE_STRING.__TITLE__', $title);

		if (!empty($category_['seo_keywords'])) {
			C('TMPL_PARSE_STRING.__KEYWORDS__', $category_['seo_keywords']);
		}
		if (!empty($category_['seo_description'])) {
			C('TMPL_PARSE_STRING.__DESCRIPTION__', $category_['seo_description']);
		}

		if (!empty($article_['seo_keywords'])) {
			C('TMPL_PARSE_STRING.__KEYWORDS__', $article_['seo_keywords']);
		}
		if (!empty($article_['seo_description'])) {
			C('TMPL_PARSE_STRING.__DESCRIPTION__', $article_['seo_description']);
		}
	}

	/**
	 * 访问合法性验证
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function illegal($type_=1)
	{
		switch ($type_) {
			case '3':
				if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
					redirect('404.html');
				}
				break;

			case '2':
				if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
					redirect('404.html');
				}

			default:
				if (empty($_GET['cid']) || !is_numeric($_GET['cid'])) {
					redirect('404.html');
				}
				break;
		}
	}

	/**
	 * 验证码
	 * @access public
	 * @param
	 * @return void
	 */
	public function verify()
	{
		$verify = new \Think\Verify();
		$verify->fontSize = 14;
		$verify->imageW = 105;
		$verify->length = 4;
		$verify->fontttf = '4.ttf';
		$verify->codeSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$verify->entry();
	}
}