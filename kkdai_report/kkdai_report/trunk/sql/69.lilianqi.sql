use db_kkrpt;

alter table `fk_touzixiangmubiao`
  modify `fuxiri` varchar(15) not null default '' comment '付息日';

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
  values ('69.lilianqi.sql', '投资项目表中付息日字段改为字符串类型');