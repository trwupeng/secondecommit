use kkdbbs;

-- kkd头像表
CREATE TABLE `pre_ucenter_avatar` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT '主键，用户ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '头像地址',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '头像类型：1默认',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态：1正常',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- kkd昵称表
CREATE TABLE `pre_common_member_nickname` (
  `uid` varchar(36) NOT NULL COMMENT '用户ID',
  `nickname` varchar(80) NOT NULL DEFAULT '' COMMENT '昵称',
  `pwd` varchar(40) NOT NULL DEFAULT '' COMMENT '登录密码',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;