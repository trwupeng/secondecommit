USE db_kkrpt;
ALTER TABLE tb_bankcard_final CHANGE `channel` `clientType` smallint(4) NOT NULL DEFAULT '0' COMMENT '端类型';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='36.llq' ON DUPLICATE KEY UPDATE v='36.llq';