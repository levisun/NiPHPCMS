DROP TABLE IF EXISTS `np_member`;
CREATE TABLE IF NOT EXISTS `np_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `email` varchar(40) NOT NULL COMMENT '邮箱',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `portrait` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别',
  `birthday` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
  `province` smallint(5) NOT NULL DEFAULT '0' COMMENT '省',
  `city` smallint(5) NOT NULL DEFAULT '0' COMMENT '市',
  `area` smallint(5) NOT NULL DEFAULT '0' COMMENT '区',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '电话',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `lastloginip` varchar(15) NOT NULL COMMENT '登录IP',
  `lastloginipattr` varchar(255) NOT NULL COMMENT '登录IP地区',
  `lastlogintime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `regtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  KEY `password` (`password`),
  KEY `gender` (`gender`),
  KEY `birthday` (`birthday`),
  KEY `province` (`province`),
  KEY `city` (`city`),
  KEY `area` (`area`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '会员';

DROP TABLE IF EXISTS `np_level_member`;
CREATE TABLE IF NOT EXISTS `np_level_member` (
  `user_id` smallint(6) unsigned NOT NULL COMMENT '会员ID',
  `level_id` smallint(6) unsigned DEFAULT NULL COMMENT '组ID',
  PRIMARY KEY (`user_id`),
  KEY `level_id` (`level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '会员组关系表';

DROP TABLE IF EXISTS `np_level`;
CREATE TABLE IF NOT EXISTS `np_level` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '组名',
  `integral` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `integral` (`integral`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '会员组';
INSERT INTO np_level(`name`, `status`, `integral`) VALUES
('钻石会员', 1, 500000000),
('黄金会员', 1, 30000000),
('白金会员', 1, 500000),
('VIP会员', 1, 3000),
('高级会员', 1, 500),
('普通会员', 1, 0);