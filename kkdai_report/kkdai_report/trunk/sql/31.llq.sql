USE db_kkrpt;

alter table tb_bankcard_final CHANGE `desc` `resultDesc` varchar(128) NOT NULL DEFAULT '' COMMENT '绑卡失败描述';
alter table tb_bankcard_final MODIFY `statusCode` tinyint(8) DEFAULT NULL COMMENT '绑卡失败状态码';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='31.llq' ON DUPLICATE KEY UPDATE v='31.llq';