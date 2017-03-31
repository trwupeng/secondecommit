USE db_kkrpt;
DROP TABLE IF EXISTS `tb_copartners_trans`;
CREATE TABLE `tb_copartners_trans` (
  `autoid` int auto_increment,
  `copartnerName` varchar(100) not NULL COMMENT '旧的渠道名称',
  `contractId`  bigint(20) NOT NULL DEFAULT '0' COMMENT '议协ID',
  PRIMARY KEY (`autoid`),
  KEY `index_contractId` (`contractId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='40.llq' ON DUPLICATE KEY UPDATE v='40.llq';