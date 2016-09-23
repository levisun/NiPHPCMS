<?php
/**
 *
 * 微信接口 - 模型
 *
 * @category   Wechat\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: ApiModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Wechat\Model;
use Think\Model;
class ApiModel extends Model
{
	protected $tableName = 'config';
	protected $fields = array('id', 'name', 'value', 'lang');
	protected $pk = 'id';

	public $_wechat;
	public $_type;					// 消息类型
	public $_event = array();		// 事件类型
	public $_sceneid;				// 二维码场景值
	public $_formuser;				// 请求用户ID
	public $_userdata = array();	// 请求用户信息
	public $_key = array();			// 请求内容

	/**
	 * 关键词回复
	 * @access public
	 * @param
	 * @return array
	 */
	public function getKey($key_)
	{
		$map = array(
			'type' => 0,
			'keyword' => array('LIKE', '%' . $key_ . '%'),
			'lang' => LANG_SET
			);
		$data = $this->table($this->tablePrefix . 'reply')
		->where($map)
		->select();
		if (empty($data)) {
			$data = $this->getAuto();
			return $data;
		}

		$item = array();
		foreach ($data as $key => $value) {
			if (!empty($value['image']) && !empty($value['url'])) {
				if (file_exists($value['image'])) {
					$value['image'] = domain() . $value['image'];
				}
				$item['item'][] = array(
					'Title' => $value['title'],
					'Description' => $value['content'],
					'PicUrl' => $value['image'],
					'Url' => $value['url']
					);
			} elseif(!empty($value['url'])) {
				$item = array('<a href="' . $value['url'] . '">' . $value['content'] . '</a>');
			} else {
				$item = array($value['content']);
			}
		}
		return $item;
	}

	/**
	 * 自动回复内容信息
	 * @access public
	 * @param
	 * @return array
	 */
	public function getAuto()
	{
		$map = array(
			'type' => 1,
			'keyword' => '_AUTO_',
			'lang' => LANG_SET
			);
		$data = $this->table($this->tablePrefix . 'reply')
		->where($map)
		->select();

		$item = array();
		foreach ($data as $key => $value) {
			if (!empty($value['image']) && !empty($value['url'])) {
				if (file_exists($value['image'])) {
					$value['image'] = domain() . $value['image'];
				}
				$item['item'][] = array(
					'Title' => $value['title'],
					'Description' => $value['content'],
					'PicUrl' => $value['image'],
					'Url' => $value['url']
					);
			} elseif(!empty($value['url'])) {
				$item = array('<a href="' . $value['url'] . '">' . $value['content'] . '</a>');
			} else {
				$item = array($value['content']);
			}
		}
		if (empty($item)) {
			$item = array('<a href="' . domain() . '">' . L('home') . '</a>');
		}
		return $item;
	}

	/**
	 * 关注回复内容信息
	 * @access public
	 * @param
	 * @return array
	 */
	public function getAttention()
	{
		$map = array(
			'type' => 2,
			'keyword' => '_ATTENTION_',
			'lang' => LANG_SET
			);
		$data = $this->table($this->tablePrefix . 'reply')
		->where($map)
		->select();

		$item = array();
		foreach ($data as $key => $value) {
			if (!empty($value['image']) && !empty($value['url'])) {
				if (file_exists($value['image'])) {
					$value['image'] = domain() . $value['image'];
				}
				$item['item'][] = array(
					'Title' => $value['title'],
					'Description' => $value['content'],
					'PicUrl' => $value['image'],
					'Url' => $value['url']
					);
			} elseif(!empty($value['url'])) {
				$item = array('<a href="' . $value['url'] . '">' . $value['content'] . '</a>');
			} else {
				$item = array($value['content']);
			}
		}
		return $item;
	}

	/**
	 * 微信接口初始化
	 * @access public
	 * @param
	 * @return void
	 */
	public function init()
	{
		$data = $this->getConfig();
		$option = array(
			'token' => $data['wechat_token'],
			'encodingaeskey' => $data['wechat_encodingaeskey'],
			'appid' => $data['wechat_appid'],
			'appsecret' => $data['wechat_appsecret']
			);

		$this->_wechat = new \Wechat($option);
		$this->_wechat->valid();

		$this->_type = $this->_wechat->getRev()->getRevType();
		$this->_event = $this->_wechat->getRevEvent();
		$this->_form = $this->_wechat->getRevFrom();
		$this->_userdata = $this->_wechat->getUserInfo($this->_form);
		$this->_key['sceneId'] = $this->_wechat->getRevSceneId();			// 扫公众号二维码返回值
		$this->_key['eventLocation'] = $this->_wechat->getRevEventGeo();	// 获得的地理信息
		$this->_key['text'] = $this->_wechat->getRevContent();				// 文字信息
		$this->_key['image'] = $this->_wechat->getRevPic();					// 图片信息
		$this->_key['location'] = $this->_wechat->getRevGeo();				// 地理信息
		$this->_key['link'] = $this->_wechat->getRevLink();					// 链接信息
		$this->_key['voice'] = $this->_wechat->getRevVoice();				// 音频信息
		$this->_key['video'] = $this->_wechat->getRevVideo();				// 视频信息
	}

	/**
	 * 获得微信接口配置
	 * @access private
	 * @param
	 * @return array
	 */
	private function getConfig()
	{
		$map = array(
			'name' => array(
				'in', 'wechat_token,wechat_encodingaeskey,wechat_appid,wechat_appsecret'
				),
			'lang' => 'niphp'
			);
		$data = $this->field($this->fields)->where($map)->select();
		foreach ($data as $key => $value) {
			$arr[$value['name']] = $value['value'];
		}
		return $arr;
	}
}