CREATE TABLE `tb_business_num` (
  `month` int(6) NOT NULL COMMENT '月份',
  `num` int(11) NOT NULL COMMENT '月初户数',
  `updateUser` varchar(20) NOT NULL,
  `updateTime` bigint(20) NOT NULL,
  `iRecordVerID` int(11) NOT NULL,
  `sLockData` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='月初户数 ';

ALTER TABLE `tb_business_num`
ADD COLUMN `numAfter`  int NOT NULL DEFAULT 0 COMMENT '月末户数' AFTER `num`;

ALTER TABLE `tb_rights_0`
ADD COLUMN `status`  smallint NOT NULL DEFAULT 0 COMMENT '-4 禁用' AFTER `sLockData`;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='33.tgh' ON DUPLICATE KEY UPDATE v='33.tgh';