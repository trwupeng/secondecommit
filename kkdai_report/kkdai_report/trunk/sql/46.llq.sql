use db_kkrpt;
DROP TABLE IF EXISTS `tb_activity_luckpag`;
CREATE TABLE `tb_activity_luckpag` (
	`ymdStart` int(11) NOT NULL COMMENT '福袋活动开启日期',
	`userId` varchar(50) NOT NULL COMMENT '用户userId',
	`dtLastNotice` bigint(20) NOT NULL DEFAULT '0' COMMENT '上次短信通知时间',
	`flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户是否玩福袋了',
	`tmpCusId` varchar(50) NOT NULL DEFAULT '' COMMENT '用户福袋账户id',
	PRIMARY KEY (`ymdStart`, `userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='福袋用户营销';