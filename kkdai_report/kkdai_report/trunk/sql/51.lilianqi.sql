-- ------------------------------
-- tb_licai_day
-- ------------------------------
DROP TABLE IF EXISTS `tb_licai_day`;
CREATE TABLE `tb_licai_day` (
  `ymd` int(11) not null comment '日期',
  `contractId` bigint(20) not null default 0 comment '协议号',
  `shelfId` smallint(4) not null default 0 comment '产品类型0 天天赚, 1 定期宝, 2 房宝宝, 5 精英宝',
  `copartnerId` smallint(4) not null default 0 comment '渠道号',

  `countReg0Day` int(11) not null default 0 comment '首投此shelfId产品并且是当日注册的人数',
  `countReg1To5` int(11) not null default 0 comment '首投此shelfId产品并且是前1-5天内注册的人数',
  `countReg6To30` int(11) not null default 0 comment '首投此shelfId产品并且是前6-30天内注册人数',
  `countReg31Plus` int(11) not null default 0 comment '首投此shelfId产品并且是31天-31天以前注册的人数	',

  `amountReg0Day` bigint(20) not null default 0 comment '首投此shelfId产品并且是当日注册投资金额',
  `amountReg1To5` bigint(20) not null default 0 comment '首投此shelfId产品并且是前1-5天内注册的金额	',
  `amountReg6To30` bigint(20) not null default 0 comment '首投此shelfId产品并且是前6-30天内注册的金额',
  `amountReg31Plus` bigint(20) not null default 0 comment '首投此shelfId产品并且是前31天-31天以上注册的金额',

  `avgAmountReg0Day` int(11) not null default 0 comment '首投此shelfId产品并且是当日注册投资平均金额',
  `avgAmountReg1To5` int(11) not null default 0 comment '首投此shelfId产品并且是前1-5天内注册的平均金额	',
  `avgAmountReg6To30` int(11) not null default 0 comment '首投此shelfId产品并且是前6-30天内注册的平均金额',
  `avgAmountReg31Plus` int(11) not null default 0 comment '首投此shelfId产品并且是前31天-31天以上注册的平均金额',

  PRIMARY KEY (`ymd`, `contractId`, `shelfId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='理财情况';

-- ------------------------------
-- tb_licai_count
-- ------------------------------
DROP TABLE IF EXISTS `tb_licai_count`;
CREATE TABLE `tb_licai_count` (
  `ymdStart` int(11) not null comment '日期',
  `ymdEnd` int(11) not null comment '日期',
  `contractId` bigint(20) not null default 0 comment '协议号',
  `shelfId` smallint(4) not null default 0 comment '产品类型0 天天赚, 1 定期宝, 2 房宝宝, 5 精英宝',
  `copartnerId` smallint(4) not null default 0 comment '渠道号',

  `count1Buy` int(11) not null default 0 comment '这天之前投资一次的人数',
  `count5Buy` int(11) not null default 0 comment '这天之前投资2-5次人数',
  `count6PlusBuy` int(11) not null default 0 comment '这天之前投资6+次人数',

  `amount1Buy` bigint(20) not null default 0 comment '这天之前投资一次的总金额',
  `amount5Buy` bigint(20) not null default 0 comment '这天之前投资2-5次的总金额',
  `amount6PlusBuy` bigint(20) not null default 0 comment '这天之前投资6+次的总金额',

  `avgAmount1Buy` int(11) not null default 0 comment '这天之前投资一次的平均金额',
  `avgAmount5Buy` int(11) not null default 0 comment '这天之前投2-5次的平均金额',
  `avgAmount6PlusBuy` int(11) not null default 0 comment '这天之前投资6+的平均金额',

  PRIMARY KEY (`ymdStart`, `ymdEnd`, `contractId`, `shelfId`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='理财情况';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='51.lilianqi' ON DUPLICATE KEY UPDATE v='51.lilianqi';