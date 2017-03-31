use db_kkrpt;

ALTER TABLE `fk_touzidangan`
ADD COLUMN `jieqingzhuangkuang`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '结清状况' AFTER `taxiangquanzheng`;

UPDATE `fk_rongzixiangmubiao` SET `fuwufeiyicixingyue` = `fuwufeiyicixingyue` / 100;

ALTER TABLE `fk_rongzixiangmubiao`
MODIFY COLUMN `fuwufeiyicixingyue`  decimal(15,8) NOT NULL DEFAULT 0 COMMENT '服务费_一次性[%/月]' AFTER `fuwufeianyueyingshou`;

UPDATE `fk_rongzixiangmubiao` SET `lixiyingshou` = `lixiyingshou` / 100;

ALTER TABLE `fk_rongzixiangmubiao`
MODIFY COLUMN `lixiyingshou` decimal(15,4) NOT NULL DEFAULT 0 COMMENT '利息_应收[%]' AFTER `lixiyue`;