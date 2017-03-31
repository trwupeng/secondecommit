CREATE TABLE `tb_activity_nov_20161130reward` (
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '自增ID',
  `userId` varchar(50) NOT NULL DEFAULT '' COMMENT '用户Id',
  `realName` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `phone` varchar(32) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `reward` varchar(32) NOT NULL DEFAULT '' COMMENT '奖品来源',
  `ticketNo` varchar(255) DEFAULT '' COMMENT '电影券',
  `kahao` varchar(255) DEFAULT '' COMMENT '卡号',
  `mima` varchar(255) DEFAULT '' COMMENT '密码',
  `dtLastNotice` varchar(32) NOT NULL DEFAULT '' COMMENT '上次短信通知时间',
  `flag` varchar(32) NOT NULL DEFAULT '' COMMENT '是否已发送；0:未发;1:已发',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='11月活动获奖用户表';

