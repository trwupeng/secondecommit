use db_kkrpt;

drop table if exists `tb_bidmonth`;
create table `tb_bidmonth` (
  `ym` int unsigned not null comment '年月',
  `amount_succ_super` bigint unsigned not null default 0 comment '超级用户月投资金额',
  `amount_succ_normal` bigint unsigned not null default 0 comment '非超级用户月投资金额',
  `total` bigint unsigned not null default 0 comment '上面两个数字的合计金额',
  primary key (`ym`)
)ENGINE = MyISAM DEFAULT CHARSET = utf8 comment '月投标统计';

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('70.lilianqi.sql', '财务统计投标统计中增加月投标统计的数据');