DROP TABLE  IF EXISTS `tb_bidstatistics`;

CREATE TABLE `tb_bidstatistics`(
  `ymd` int(11) not null comment '日期',
  `amount_succ_normal` bigint(20) not null default 0 comment '非超级用户投资成功总金额',
  `count_succ_normal` int(11) not null default 0 comment '非超级用户投资成功总次数',
  `amount_fail_normal` int(11) not null default 0 comment '非超级用户投资失败总金额',
  `count_fail_normal` int(11) not null default 0 comment '非超级用户投资失败总次数',
  `amount_succ_super` bigint(20) not null default 0 comment '超级用户投资成功总金额',
  `count_succ_super` int(11) not null default 0 comment '超级用户投资成功总次数',
  `amount_fail_super` int(11) not null default 0 comment '超级用户投资失败总金额',
  `count_fail_super` int(11) not null default 0 comment '超级用户投资失败总次数',
  PRIMARY KEY (`ymd`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='投资统计';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='54.lilianqi' ON DUPLICATE KEY UPDATE v='54.lilianqi';