use db_kkrpt;

CREATE TABLE `hd_hp_log` (
  `id` bigint(20) NOT NULL,
  `customerId` varchar(50) NOT NULL,
  `oldHP` int(11) NOT NULL DEFAULT '0',
  `addHP` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT '',
  `statusCode` int(11) NOT NULL DEFAULT '0',
  `createYmd` bigint(20) NOT NULL DEFAULT '0',
  `exp` varchar(255) NOT NULL DEFAULT '',
  `iRecordVerID` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hd_red_log` (
  `id` bigint(20) NOT NULL,
  `customerId` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `statusCode` int(11) NOT NULL DEFAULT '0',
  `createYmd` bigint(20) NOT NULL DEFAULT '0',
  `exp` varchar(255) NOT NULL DEFAULT '',
  `iRecordVerID` int(11) DEFAULT '0',
  `hp` int(11) NOT NULL DEFAULT '0',
  `investAmount` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hd_user` (
  `customerId` varchar(50) NOT NULL COMMENT '用户ID',
  `customerName` varchar(50) NOT NULL DEFAULT '',
  `investHP` int(11) NOT NULL DEFAULT '0' COMMENT '投资营养值',
  `normalHP` int(11) NOT NULL DEFAULT '0' COMMENT '常规营养值',
  `lastCollectYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '上次浇灌时间',
  `createYmd` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hd_red_log`
ADD COLUMN `hplogid`  bigint NOT NULL AFTER `investAmount`;

ALTER TABLE `hd_user`
ADD COLUMN `sLockData`  varchar(255) NOT NULL DEFAULT '' AFTER `iRecordVerID`;

ALTER TABLE `hd_user`
ADD COLUMN `lastUpdateYmd`  bigint NOT NULL DEFAULT 0 AFTER `createYmd`;

