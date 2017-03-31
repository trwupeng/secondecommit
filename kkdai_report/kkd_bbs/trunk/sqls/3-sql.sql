CREATE TABLE `pre_a_klb_log` (
  `sn` varchar(32) NOT NULL,
  `time` bigint(20) NOT NULL DEFAULT '0',
  `uid` bigint(20) NOT NULL DEFAULT '0',
  `kkdId` bigint(20) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  `statusCode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

