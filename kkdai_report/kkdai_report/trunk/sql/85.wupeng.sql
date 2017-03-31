CREATE TABLE `tb_activity_september_20161019reward` (
  `ymdStart` varchar(32) NOT NULL DEFAULT '' COMMENT '活动开始时间',
  `userId` varchar(50) NOT NULL DEFAULT '' COMMENT '用户Id',
  `realName` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `phone` varchar(32) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `reward` varchar(32) NOT NULL DEFAULT '' COMMENT '奖品',
  `ticketNo` varchar(255) NOT NULL DEFAULT '' COMMENT '电影券',
  `dtLastNotice` varchar(32) NOT NULL DEFAULT '' COMMENT '上次短信通知时间',
  `flag` varchar(32) NOT NULL DEFAULT '' COMMENT '是否已发送；0:未发;1:已发',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='9月活动获奖用户表';