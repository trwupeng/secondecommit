CREATE TABLE `tb_rights_role` (
  `roleId` bigint(11) NOT NULL COMMENT '角色ID',
  `roleName` varchar(60) NOT NULL DEFAULT '' COMMENT '角色名',
  `rightsIds` varchar(255) NOT NULL DEFAULT '' COMMENT '权限集',
  `statusCode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`roleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

