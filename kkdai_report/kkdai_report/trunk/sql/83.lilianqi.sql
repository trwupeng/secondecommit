alter table mb_message add column `userid` varchar(100) NOT NULL COMMENT '日志/目标拥有者' after receiverid;

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('83.lilainqi.sql', '增加字段' );
