USE db_kkrpt;

alter table tb_user_final CHANGE `yuebao_totao_amount` `yuebao_total_amount`  bigint(32) DEFAULT '0' COMMENT '天天赚总投资额';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='32.llq' ON DUPLICATE KEY UPDATE v='32.llq';