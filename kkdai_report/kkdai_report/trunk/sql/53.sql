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
insert into db_kkrpt.tb_config set k='dbsql.ver',v='53.lilianqi' ON DUPLICATE KEY UPDATE v='53.lilianqi';