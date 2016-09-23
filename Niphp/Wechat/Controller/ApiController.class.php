<?php
/**
 *
 * 微信接口 - 控制器
 *
 * @category   Wechat\Controller
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ApiController.class.php 2016-06 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 0.1
 */
namespace Wechat\Controller;
use Think\Controller;
class ApiController extends Controller
{
	protected $_api = '';

	protected function _initialize()
	{
		Vendor('Wechat#class', COMMON_PATH . 'Library');
		$this->_api = D('api');
	}

	public function index()
	{
		$this->_api->init();

		// 事件推送
		if ($this->_api->_type == \Wechat::MSGTYPE_EVENT) {
			// 关注事件
			if ($this->_api->_event['event'] == \Wechat::EVENT_SUBSCRIBE) {
				// 获取二维码的场景值
				if ($this->_api->_key['sceneId']) {

				}

				// 关注回复信息
				$this->subscribe();
			}

			// 取消关注事件
			if ($this->_api->_event['event'] == \Wechat::EVENT_UNSUBSCRIBE) {
				$this->unsubscribe();
			}

			// 上报地理位置事件
			if ($this->_api->_event['event'] == \Wechat::EVENT_LOCATION) {
				$this->_api->_key['eventLocation'];
			}

			// 点击菜单跳转链接
			if ($this->_api->_event['event'] == \Wechat::EVENT_MENU_VIEW) {
				# code...
			}

			// 点击菜单拉取消息
			if ($this->_api->_event['event'] == \Wechat::EVENT_MENU_CLICK) {
				# code...
			}
		}

		// 文字信息
		if ($this->_api->_type == \Wechat::MSGTYPE_TEXT) {
			// 关键词回复信息
			$this->replyKeyword();
		}

		// 图片信息
		if ($this->_api->_type == \Wechat::MSGTYPE_IMAGE) {
			$this->_api->_key['image'];
			$this->_api->_wechat
			->image($this->_api->_key['image']['mediaid'])
			->reply();
		}

		// 地址信息
		if ($this->_api->_type == \Wechat::MSGTYPE_LOCATION) {
			$this->_api->_key['location'];
		}

		// 链接信息
		if ($this->_api->_type == \Wechat::MSGTYPE_LINK) {
			$this->_api->_key['link'];
		}

		// 音频信息
		if ($this->_api->_type == \Wechat::MSGTYPE_VOICE) {
			$this->_api->_key['voice'];
		}

		// 视频信息
		if ($this->_api->_type == \Wechat::MSGTYPE_VIDEO ||
			$this->_api->_type == \Wechat::MSGTYPE_SHORTVIDEO) {
			$this->_api->_key['video'];
		}

		// 音乐信息
		if ($this->_api->_type == \Wechat::MSGTYPE_MUSIC) {
			# code...
		}

		// 图文信息
		if ($this->_api->_type == \Wechat::MSGTYPE_NEWS) {
			# code...
		}
	}

	/**
	 * 关注回复信息
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function subscribe()
	{
		$data = $this->_api->getAttention();
		if (isset($data['item'])) {
			$this->_api->_wechat->news($data['item'])->reply();
		} else {
			$this->_api->_wechat->text($data[0])->reply();
		}
	}

	/**
	 * 取消关注回复信息
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function unsubscribe()
	{
		# code...
	}

	/**
	 * 关键词回复信息
	 * @access protected
	 * @param
	 * @return void
	 */
	protected function replyKeyword()
	{
		$data = $this->_api->getKey($this->_api->_key['text']);
		if (isset($data['item'])) {
			$this->_api->_wechat->news($data['item'])->reply();
		} else {
			$this->_api->_wechat->text($data[0])->reply();
		}
	}
}