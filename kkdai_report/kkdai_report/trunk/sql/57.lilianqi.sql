drop table IF EXISTS `tb_bill_repay_history`;
create table `tb_bill_repay_history` (
  `historyId` varchar(255) not null comment '还款Id',
  `billId` varchar(255) NOT NULL default '' comment '账单Id',
  `billNum`int(11) DEFAULT NULL default 0 comment '第几期',
  `ymdShouldPay` int(11) not null default 0 comment '合约还款日',
  `ymdPayment` int(11) not null default 0 comment '实际还款日期',
  `userId` varchar(255) not null DEFAULT ''  comment '用户Id',
  `finish` int(11) not NULL default 0 comment '是否所有已经还清: 0 否, 1 是',

  `orderAmount` bigint(20) not null default 0 comment '实际投资金额',
  `orderAmountExt` bigint(20) not null default 0 comment '使用红包',
  `orderAmountSum` bigint(20) not null default 0 comment '总投资金额',

  `amount`bigint(20) not NULL default 0 comment '还款金额',
  `interest` bigint(20) DEFAULT 0 comment '利息',
  `addInterest` bigint(20) NOT NULL DEFAULT '0' comment '加息',
  `penaltyInteret` bigint(20) not NULL default 0 comment '罚息',
  `ordersId` varchar(255) not null DEFAULT '' comment '订单Id',

  `waresId` varchar(255) not null DEFAULT '' comment '产品Id',

  `principal` bigint(20) not NULL default 0 comment '本金',
  `serialId` varchar(255) not null DEFAULT '',
  `status` int(11) not null default 5 comment '5 成功, 6失败',
  `poi_type` tinyint(4) not null DEFAULT 0 comment '是否是天天赚资金购买 1 是',
  `order_Id` varchar(255) not null default '',

  PRIMARY KEY (`historyId`),
  KEY `index_repay_history_billid`(`billId`),
  KEY `index_ymdPayment` (`ymdPayment`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='还款详情';;
insert into db_kkrpt.tb_config set k='dbsql.ver',v='57.lilianqi' ON DUPLICATE KEY UPDATE v='57.lilianqi';