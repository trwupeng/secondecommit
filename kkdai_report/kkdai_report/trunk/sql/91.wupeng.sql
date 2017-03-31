
ALTER TABLE `mb_perf_dailylog`
MODIFY COLUMN `plan_cost`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 COMMENT '计划用时(小时)' AFTER `type`,
MODIFY COLUMN `real_cost`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 COMMENT '实际用时(小时）' AFTER `plan_cost`;

ALTER TABLE `mb_message`
ADD COLUMN `perf_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '目标自建id或者日志自建的id' AFTER `dstid`;

ALTER TABLE `mb_perf_dst`
ADD COLUMN `settime`  date NOT NULL COMMENT '目标日期' AFTER `id`,
ADD COLUMN `staffname`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '员工名称' AFTER `settime`;

ALTER TABLE `mb_perf_dailylog`
ADD COLUMN `settime`  date NOT NULL COMMENT '日志日期' AFTER `id`,
ADD COLUMN `staffname`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '员工名称' AFTER `settime`;


update db_kkrpt.tb_menu set value='["plan","perfdailylog","index",{"targetTab":"list"},[],"perf"]' where id=1002;