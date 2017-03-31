CREATE TABLE `mb_department` (
  `id` bigint(20) NOT NULL,
  `supId` bigint(20) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `oa_sort` int(11) NOT NULL DEFAULT '0',
  `statusCode` int(11) NOT NULL DEFAULT '0',
  `updateTime` bigint(20) NOT NULL DEFAULT '0',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `tb_managers_0`
ADD COLUMN `checkUsers`  varchar(3000) NOT NULL DEFAULT '' COMMENT '可以查看的人员名单' AFTER `ec`;

ALTER TABLE `tb_managers_0`
ADD COLUMN `postName`  varchar(50) NOT NULL DEFAULT '' COMMENT '职位名称' AFTER `checkUsers`;

INSERT INTO `db_kkrpt`.`tb_menu` (`id`, `mark`, `name`, `value`, `iRecordVerID`, `statusCode`, `alias`) VALUES ('1005', '目标管理', '目标管理.EC统计', '[\"plan\",\"ecstat\",\"index\",[],[]]', '1', '0', 'index');


