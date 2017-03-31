ALTER TABLE `mb_perf_dailylog` MODIFY COLUMN `userid` VARCHAR(40) NOT NULL DEFAULT '' COMMENT '日志所属的用户id' AFTER `del_time`;

ALTER TABLE `mb_perf_dst` MODIFY COLUMN `create_userid` VARCHAR(40) NOT NULL DEFAULT '' COMMENT '创建者的用户id' AFTER `del_time`;
ALTER TABLE `mb_perf_dst` MODIFY COLUMN `userid` VARCHAR(40) NOT NULL DEFAULT '' COMMENT '日志所属的用户id' AFTER `create_userid`;

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('77.lyq.sql', '目标、日志相关表' );