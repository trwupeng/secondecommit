USE db_kkrpt;

alter table tb_recharges_final ADD COLUMN `clientType` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '端类型';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='34.llq' ON DUPLICATE KEY UPDATE v='34.llq';