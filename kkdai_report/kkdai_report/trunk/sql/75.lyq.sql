ALTER TABLE `mb_perf_dst` ADD COLUMN `type_num` int(11) NOT NULL DEFAULT 0 COMMENT '批次数' AFTER `type`;
ALTER TABLE `mb_perf_dst` ADD COLUMN `type_id` bigint(18) NOT NULL DEFAULT 0 COMMENT '计算后的批次ID' AFTER `type_num`;

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('75.lyq.sql', '目标相关表');