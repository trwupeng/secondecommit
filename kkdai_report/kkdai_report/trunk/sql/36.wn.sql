USE db_kkrpt;

ALTER TABLE tb_copartner_0 ADD authCode varchar(36) NOT NULL DEFAULT '' COMMENT '授权码' after copartnerAbs;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='36' ON DUPLICATE KEY UPDATE v='36';