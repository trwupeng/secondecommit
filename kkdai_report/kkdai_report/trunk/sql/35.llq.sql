USE db_kkrpt;

ALTER TABLE tb_recharges_final ADD COLUMN `orderStatus` int(11) NOT NULL DEFAULT '0' COMMENT '单订状态';
ALTER TABLE tb_recharges_final ADD COLUMN `finishYmd` int(11) NOT NULL DEFAULT '0' COMMENT '定单最终完成日期';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='35.llq' ON DUPLICATE KEY UPDATE v='35.llq';