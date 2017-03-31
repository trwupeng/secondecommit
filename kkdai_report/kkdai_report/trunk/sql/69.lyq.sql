use db_kkrpt;
-- ------------------------------
-- 服务费按月% 改为decimal类型
-- ------------------------------
ALTER TABLE `fk_rongzixiangmubiao` MODIFY COLUMN `fuwufeianyue` decimal(15,8) NOT NULL DEFAULT 0 AFTER `lixishishouyuan`;
UPDATE `fk_rongzixiangmubiao` SET `fuwufeianyue` = `fuwufeianyue`/100;

ALTER TABLE `fk_rongzixiangmubiao` ADD COLUMN `fuwufeianyueyingshou` decimal(15,3) NOT NULL DEFAULT 0 AFTER `fuwufeianyue`;

insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('69.lyq.sql', '服务费按月改为decimal类型,增加服务费按月应收');