CREATE TABLE `tb_december_wechat_public_activity` (
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '自增ID',
  `userId` varchar(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `realName` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `phone` varchar(32) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `reward` varchar(32) NOT NULL DEFAULT '' COMMENT '奖品来源',
  `ticketNo` varchar(255) DEFAULT '' COMMENT '电影券',
  `dtLastNotice` varchar(32) NOT NULL DEFAULT '' COMMENT '上次短信通知时间',
  `flag` varchar(32) NOT NULL DEFAULT '' COMMENT '是否已发送；0:未发;1:已发',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='12月微信公众号活动';

