USE db_kkrpt;
DROP TABLE IF EXISTS `tb_yuebao_out`;
CREATE TABLE `tb_yuebao_out` (
  `ordersId` varchar(50) NOT NULL COMMENT 'ordersId',
  `waresId` varchar(50) NOT NULL COMMENT 'yuebao id',
  `userId` varchar(50) NOT NULL DEFAULT '' COMMENT 'userId',
  `amount` bigint(20) DEFAULT '0' COMMENT '取现/投标金额',
  `ymd` int(11) DEFAULT NULL,
  `hhiiss` int(11) NOT NULL DEFAULT '0',
  `yieldStatic` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '年利率 bid_interest',
  `type` tinyint(4) DEFAULT NULL COMMENT '1 投标 -1 取现 2.复投',
  `orderStatus` tinyint(4) DEFAULT NULL COMMENT '状态',
  `shelfId` smallint(4) NOT NULL DEFAULT '0' COMMENT '货架 product_type',
  `clientType` smallint(4) NOT NULL DEFAULT '0' COMMENT '端类型',
  PRIMARY KEY (`ordersId`),
  KEY `index_userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='39.llq' ON DUPLICATE KEY UPDATE v='39.llq';