-- 在快快贷活动服务器使用

use db_kkrpt;
DROP TABLE IF EXISTS `tb_activity_spider_20160621`;
CREATE TABLE `tb_activity_spider_20160621` (
	`ticketSerialNo` varchar(50) NOT NULL COMMENT '电影票序列号',
	`userId` varchar(50) NOT NULL COMMENT '用户userId',
	`realname` varchar(30) NOT NULL DEFAULT '' COMMENT '姓名',
	`phone` bigint(16) NOT NULL DEFAULT '0' COMMENT '手机号',
	`createTime` bigint(11) DEFAULT NULL default 0 COMMENT '发放时间',
	`flagGranted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已经发放给用户了',
	`flagMsg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发短信0 否，1 是',
	`activityName` varchar(50) NOT NULL DEFAULT '' COMMENT '活动名称',
	`num` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '第几张',
	PRIMARY KEY (`ticketSerialNo`),
	UNIQUE KEY `uk_uid_name_num` (`userId`, `activityName`, `num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='6月21蜘蛛网活动表';
