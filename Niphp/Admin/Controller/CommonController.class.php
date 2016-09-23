<?php
/**
 *
 * 系统全局 - 控制器
 *
 * @category   Admin\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: CommonController.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller
{
	protected $_model = '';

	protected function _initialize()
	{
		if (!D('account')->isUserLogin()) {
			$this->error(L('error_account'), U('settings/info'));
		}
		// 用户信息
		$this->assign('__USER__', session('USER_DATA'));

		// 用户权限
		$data = D('account')->getAccountMenu();
		if (!empty($data['accountMenu'])) {
			$this->assign('__MENU__', $data['accountMenu']);
			$this->assign('__SUB_TITLE__', $data['sub_title']);
			$this->assign('__BREADCRUMB__', $data['breadcrumb']);
		}

		// 网站标题
		C('TMPL_PARSE_STRING.__TITLE__', $data['title']);
		// 模板
		C('TMPL_PARSE_STRING.__THEME__', C('DEFAULT_THEME'));
		// 域名
		$domain = domain();
		C('TMPL_PARSE_STRING.__DOMAIN__', $domain);
		C('TMPL_PARSE_STRING.__STATIC__', $domain . 'Static/');

		// 网站常用变量
		$theme = $domain . 'Niphp/Admin/View/' . C('DEFAULT_THEME') . '/';
		C('TMPL_PARSE_STRING.__THEME__', $theme);
		C('TMPL_PARSE_STRING.__CSS__', $theme . 'css/');
		C('TMPL_PARSE_STRING.__JS__', $theme . 'js/');
		C('TMPL_PARSE_STRING.__IMG__', $theme . 'img/');

		// 操作分支
		$this->_model = I('get.d', 'list');

		// 父ID
		$_GET['pid'] = I('get.pid', 0, C('PRIMARY_FILTER'));

		// 搜索和添加按钮
		$this->assign('submenu', 0);
		$this->assign('submenu_button_added', 1);

		removeLogs();
	}

	/**
	 * 上传文件
	 * @access public
	 * @param
	 * @return void
	 */
	public function upload()
	{
		if (IS_POST || I('get.type') == 'ckeditor') {
			$dir = '';				// 保存目录
			$width = $height = 0;	// 缩略图宽高
			$isWater = false;		// 是否加水印
			$isDelete = false;		// 是否删除原始图片
			$extname = '_thumb';	// 缩略图扩展名
			$type = I('post.type') ? I('post.type') : I('get.type');
			switch ($type) {
				// 水印
				case 'water':
					$dir = 'water/';
					break;

				// 广告
				case 'ads';
					$dir = 'ads/';
					break;

				// 幻灯片
				case 'banner';
					$dir = 'banner/';
					break;

				// 评论
				case 'comment';
					$dir = 'comment/';
					break;

				// 头像
				case 'portrait':
					$dir = 'portrait/';
					$width = $height = 200;
					$extname = '';
					break;

				// 栏目图标
				case 'category':
					$dir = 'category/';
					$width = $height = 200;
					$extname = '';
					break;

				// 图片
				case 'image':
					$dir = 'images/';
					if (I('post.model')) {
						$isWater = I('post.model') != 'link' ? true : false;
						$isDelete = true;
						$size = D('account')->getModelSize(I('post.model'));
						$width = !empty($size[I('post.model') . '_module_width']) ? $size[I('post.model') . '_module_width'] : 0;
						$height = !empty($size[I('post.model') . '_module_height']) ? $size[I('post.model') . '_module_height'] : 0;
					}
					break;

				// 内容图
				case 'ckeditor':
					$dir = 'images/';
					$isWater = true;
					break;

				// 相册
				case 'album':
					$dir = 'album/';
					$width = $height = 250;
					break;
			}

			$upload = D('upload')->upload($dir, $width, $height, $isWater, $isDelete, $extname);
			if (!is_array($upload)) {
				$this->error($upload);
			}

			if (I('get.type') == 'ckeditor') {
				$ckefn = I('get.CKEditorFuncNum');
				$js = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction(' . $ckefn . ',\'' . $upload[0]['thumb'] . '\',\'' . L('_success_upload') . '\');</script>';
				exit($js);
			}

			if (I('get.type') == 'album') {
				$js = '<script type="text/javascript">';
				$js .= 'opener.document.getElementById("album-image-' . I('post.id') . '").value="' . $upload[0]['file'] . '";';
				$js .= 'opener.document.getElementById("album-thumb-' . I('post.id') . '").value="' . $upload[0]['thumb'] . '";';
				$js .= 'opener.document.getElementById("img-album-' . I('post.id') . '").style.display="";';
				$js .= 'opener.document.getElementById("img-album-' . I('post.id') . '").src="' . $upload[0]['thumb'] . '";';
				$js .= 'window.close();';
				$js .= '</script>';
				exit($js);
			}

			$js = '<script type="text/javascript">';
			$js .= 'opener.document.getElementById("' . I('post.id') . '").value="' . $upload[0]['thumb'] . '";';
			$js .= 'opener.document.getElementById("img-' . I('post.id') . '").style.display="";';
			$js .= 'opener.document.getElementById("img-' . I('post.id') . '").src="' . $upload[0]['thumb'] . '";';
			$js .= 'window.close();';
			$js .= '</script>';

			/*$js = '<script type="text/javascript">';
			$js .= 'window.parent.document.getElementById("' . I('id') . '").value="' . $upload[0]['thumb'] . '";';
			$js .= 'window.parent.document.getElementById("img-' . I('id') . '").style.display="";';
			$js .= 'window.parent.document.getElementById("img-' . I('id') . '").src="' . $upload[0]['thumb'] . '";';
			$js .= 'var node = window.parent.document.getElementsByClassName("modal-backdrop")[0];';
			$js .= 'window.parent.document.body.removeChild(node);';
			$js .= 'window.parent.document.getElementById("myModal").style.display="none";';
			$js .= '</script>';*/
			exit($js);
		}
		$this->display('Common/upload');
	}

	/**
	 * 列表
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function listing()
	{
		if ($this->_model == 'list') {
			C('TOKEN_ON', false);

			// 排序
			if (IS_POST) {
				$return = D(ACTION_NAME)->upSort();
				if ($return === true) {
					$this->success(L('_success_sort'));
					exit();
				} else {
					$this->error($return);
				}
			}

			$data = D(ACTION_NAME)->getData();
			$this->assign('list', $data['list']);
			$this->assign('page', $data['page']);
			$this->display();
		}
	}

	/**
	 * 新增
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function added()
	{
		if ($this->_model == 'added') {
			if (IS_POST) {
				$return = D(ACTION_NAME)->added();
				if ($return === true) {
					$this->success(L('_success_added'), U(ACTION_NAME));
					exit();
				} else {
					$this->error($return);
				}
			}
			$this->display(ACTION_NAME . '_' . $this->_model);
		}
	}

	/**
	 * 编辑
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function editor()
	{
		if ($this->_model == 'editor') {
			if (IS_POST) {
				$return = D(ACTION_NAME)->editor();
				if ($return === true) {
					$this->success(L('_success_editor'));
					exit();
				} else {
					$this->error($return);
				}
			}
			$data = D(ACTION_NAME)->getDataOne();
			if (is_string($data)) {
				$this->error($data);
			}
			$this->assign('data', $data);
			$this->display(ACTION_NAME . '_' . $this->_model);
		}
	}

	/**
	 * 删除
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function remove()
	{
		if ($this->_model == 'remove') {
			$data = D(ACTION_NAME)->remove();
			if (is_string($data)) {
				$this->error($data);
			} else {
				$this->success(L('_success_delete'), U(ACTION_NAME));
			}
		}
	}
}