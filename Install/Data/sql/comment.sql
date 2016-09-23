DROP TABLE IF EXISTS `np_comment`;
CREATE TABLE IF NOT EXISTS `np_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `content_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `content` varchar(1000) NOT NULL COMMENT '评论内容',
  `is_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核',
  `is_report` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '举报',
  `support` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '支持',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '评论IP',
  `ipattr` varchar(255) NOT NULL DEFAULT '' COMMENT '评论IP地区',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `lang` varchar(50) NOT NULL DEFAULT 'zh-cn' COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `content_id` (`content_id`),
  KEY `user_id` (`user_id`),
  KEY `pid` (`pid`),
  KEY `is_pass` (`is_pass`),
  KEY `is_report` (`is_report`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表';

DROP TABLE IF EXISTS `np_comment_support`;
CREATE TABLE IF NOT EXISTS `np_comment_support` (
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `comment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `ipattr` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP地区',
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论支持表';

DROP TABLE IF EXISTS `np_comment_report`;
CREATE TABLE IF NOT EXISTS `np_comment_report` (
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `comment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `ipattr` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP地区',
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论举报表';