
-- ------------------------------
-- tb_zhuanhua
-- ------------------------------
DROP TABLE IF EXISTS  `tb_zhuanhua`;
CREATE TABLE `tb_zhuanhua` (
  `ymd` int(11) not null comment '日期',
  `contractId` bigint(20) not null default 0 comment '协议号',
  `copartnerId` smallint(4) not null default 0 comment '渠道号',
  `registerCount` int(11) not null default 0 comment '当日注册人数',
  `realnameCount` int(11) not null default 0 comment '当日注册当日实名人数',
  `bindcardCount` int(11) not null default 0 comment '当日注册当日绑卡人数',
  `newRechargeCount` int(11) not null default 0 comment '当日注册当日充值人数',
  `newBuyCount` int(11) not null default 0 comment '当日注册当日购买人数',
  PRIMARY KEY (`ymd`, `contractId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='转化';




-- ------------------------------
-- tb_financial_situation
-- ------------------------------
DROP TABLE IF EXISTS `tb_financial_situation`;
CREATE TABLE `tb_financial_situation` (
  `ymd` int(11) not null comment '日期',
  `contractId` bigint(20) not null default 0 comment '协议号',
  `flagUser`  smallint(4) not null default 0 comment '0:普通用户 1:超级用户 2:员工 3:员工推荐',
  `copartnerId` smallint(4) not null default 0 comment '渠道号',
  `rechargeAmount` bigint(20) not null default 0 comment '充值金额',
  `withdrawAmount` bigint(20) not null default 0 comment '提现金额',
  `investmentingAmount` bigint(20) not null default 0 comment '在投金额',
  `withdrawRate` int(11) not null default 0 comment '提现率(=提现金额/在投金额)',
  PRIMARY KEY (`ymd`, `contractId`, `flagUser`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='资金情况';

-- ------------------------------
-- tb_futou
-- ------------------------------
DROP TABLE IF EXISTS `tb_futou`;
CREATE TABLE `tb_futou` (
  `ymd` int(11) not null comment '截至日期',
  `contractId` bigint(20) not null default 0 comment '协议号',
  `copartnerId` smallint(4) not null default 0 comment '渠道号',
  `n1` int(11) not null default 0 comment '首投人数',
  `n2` int(11) not null default 0 comment '二投人数',
  `n3` int(11) not null default 0 comment '三投人数',
  `n4` int(11) not null default 0 comment '四投人数',
  `n5` int(11) not null default 0 comment '五投人数',
  PRIMARY KEY (`ymd`, `contractId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='复投情况';

-- ------------------------------
-- tb_liucun
-- ------------------------------
DROP TABLE IF EXISTS `tb_liucun`;
CREATE TABLE `tb_liucun` (
  `ymd` int(11) not null comment '截至日期',
  `contractId` bigint(20) not null default 0 comment '协议号',
  `copartnerId` smallint(4) not null default 0 comment '渠道号',
  `notLicaiHasBalance` int(11) not null default 0 comment '无投资有余额人数',
  `licaiNoBalance` int(11) not null default 0 comment '有投资无余额人数',
  `licaiHasBalance` int(11) not null default 0 comment '有投资有余额人数',
  PRIMARY KEY (`ymd`, `contractId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留存情况';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='52.lilianqi' ON DUPLICATE KEY UPDATE v='52.lilianqi';