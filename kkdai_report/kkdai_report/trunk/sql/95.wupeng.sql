ALTER TABLE `mb_perf_dst`
ADD COLUMN `target_ji`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日目标关联的季度目标' AFTER `dst_date`,
ADD COLUMN `target_yue`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日目标关联的月目标' AFTER `target_ji`,
ADD COLUMN `target_week`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日目标关联的周目标' AFTER `target_yue`,
ADD COLUMN `process`  varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '工作进度' AFTER `target_week`;

ALTER TABLE `mb_perf_dailylog`
MODIFY COLUMN `finish`  varchar(16) NULL DEFAULT 0 COMMENT '完成情况 百分比(需要填原因)' AFTER `real_cost`;