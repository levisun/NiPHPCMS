DROP TABLE IF EXISTS `np_product`;
CREATE TABLE IF NOT EXISTS `np_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` varchar(555) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `content` mediumtext NOT NULL COMMENT '内容',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `category_id` smallint(6) unsigned NOT NULL COMMENT '栏目ID',
  `type_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `is_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核',
  `is_com` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_top` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '置顶',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '最热',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `hits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点击量',
  `comment` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论量',
  `username` varchar(20) NOT NULL COMMENT '作者名',
  `origin` varchar(255) NOT NULL DEFAULT '' COMMENT '来源',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布人ID',
  `url` varchar(500) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `is_link` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '跳转',
  `recycle` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '回收站',
  `showtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '显示时间',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `access` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '访问权限',
  `lang` varchar(50) NOT NULL DEFAULT 'zh-cn'  COMMENT '语言',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`),
  KEY `is_pass` (`is_pass`),
  KEY `is_com` (`is_com`),
  KEY `is_top` (`is_top`),
  KEY `is_hot` (`is_hot`),
  KEY `recycle` (`recycle`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品表';

DROP TABLE IF EXISTS `np_product_album`;
CREATE TABLE IF NOT EXISTS `np_product_album` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_id` int(11) unsigned NOT NULL COMMENT '图文ID',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '原图',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品相册表';

DROP TABLE IF EXISTS `np_product_data`;
CREATE TABLE IF NOT EXISTS `np_product_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_id` int(11) unsigned NOT NULL COMMENT '产品ID',
  `fields_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `data` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品扩展表';