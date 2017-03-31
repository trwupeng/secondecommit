drop table IF EXISTS `tb_voucher_statistics`;
create table `tb_voucher_statistics` (
  `ymd` int(11) not null comment '发放/使用日期',
  `dixian_grant_amount` bigint(20) not null default 0 comment '抵现券发放金额',
  `tixian_grant_amount` bigint(20) not null default 0 comment '提现券发放金额',
  `jiaxi_grant_num` bigint(20) not null default 0 comment '加息券发放数量',
  `fanxian_grant_amount` bigint(20) not null default 0 comment '返现券发放金额',

  `dixian_use_amount` bigint(20) not null default 0 comment '抵现券使用金额',
  `tixian_use_amount` bigint(20) not null default 0 comment '提现券使用金额',
  `jiaxi_use_num` bigint(20) not null default 0 comment '加息券使用数量',
  `fanxian_use_amount` bigint(20) not null default 0 comment '返现券使用金额',
  PRIMARY KEY (`ymd`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券发放/使用';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='58.lilianqi' ON DUPLICATE KEY UPDATE v='58.lilianqi';