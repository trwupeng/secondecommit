ALTER TABLE `mb_perf_dailylog` ADD COLUMN `iRecordVerID` int(11) NOT NULL DEFAULT 0 AFTER `del`;
ALTER TABLE `mb_perf_dailylog` ADD COLUMN `sLockData` varchar(255) NOT NULL DEFAULT '' AFTER `iRecordVerID`;

ALTER TABLE `mb_perf_dst` ADD COLUMN `iRecordVerID` int(11) NOT NULL DEFAULT 0 AFTER `del`;
ALTER TABLE `mb_perf_dst` ADD COLUMN `sLockData` varchar(255) NOT NULL DEFAULT '' AFTER `iRecordVerID`;

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('76.lyq.sql', '日志相关表');