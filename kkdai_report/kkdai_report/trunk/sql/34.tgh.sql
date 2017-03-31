ALTER TABLE `tb_business_0`
ADD COLUMN `status`  smallint NOT NULL DEFAULT 0 COMMENT '4：已审核' AFTER `createTime`;

ALTER TABLE `tb_business_num`
ADD COLUMN `week`  smallint NOT NULL DEFAULT 0 COMMENT '确认至几周' AFTER `updateTime`;


insert into db_kkrpt.tb_config set k='dbsql.ver',v='34.tgh' ON DUPLICATE KEY UPDATE v='34.tgh';