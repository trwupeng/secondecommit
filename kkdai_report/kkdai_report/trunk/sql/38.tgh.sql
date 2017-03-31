CREATE TABLE `tb_file` (
  `fileId` bigint(20) NOT NULL,
  `data` mediumblob NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被占用 0占用  -1没用',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `tb_img` (
  `imgId` bigint(20) NOT NULL,
  `exp` varchar(100) NOT NULL DEFAULT '',
  `type` smallint(6) NOT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `fileId` bigint(20) NOT NULL,
  `updateUser` varchar(20) NOT NULL DEFAULT '',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) NOT NULL DEFAULT '',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`imgId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



insert into db_kkrpt.tb_config set k='dbsql.ver',v='38' ON DUPLICATE KEY UPDATE v='38';