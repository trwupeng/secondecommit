ALTER TABLE `tb_finance_ground_0`
MODIFY COLUMN `pAmountRemain`  bigint(20) NOT NULL DEFAULT 0 AFTER `payAmount`;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='37' ON DUPLICATE KEY UPDATE v='37';