use db_kkrpt;

CREATE TABLE `tb_temp` (
  `id` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `exp` varchar(255) NOT NULL DEFAULT '',
  `lastUpdate` bigint(11) NOT NULL DEFAULT '0',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='临时数据表';

