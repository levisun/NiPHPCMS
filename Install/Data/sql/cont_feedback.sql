DROP TABLE IF EXISTS `np_feedback`;
CREATE TABLE IF NOT EXISTS `np_feedback` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `username` varchar(20) NOT NULL COMMENT '作者名',
  `content` mediumtext NOT NULL COMMENT '内容',
  `category_id` smallint(6) unsigned NOT NULL COMMENT '栏目ID',
  `type_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `mebmer_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `is_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核',
  `recycle` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '回收站',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `lang` varchar(50) NOT NULL DEFAULT 'zh-cn'  COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`),
  KEY `is_pass` (`is_pass`),
  KEY `recycle` (`recycle`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='反馈表';

DROP TABLE IF EXISTS `np_feedback_data`;
CREATE TABLE IF NOT EXISTS `np_feedback_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_id` int(11) unsigned NOT NULL COMMENT '反馈ID',
  `fields_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `data` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='反馈扩展表';