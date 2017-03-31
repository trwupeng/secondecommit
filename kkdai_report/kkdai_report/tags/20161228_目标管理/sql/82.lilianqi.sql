
alter table mb_message add column `iRecordVerID` int(11) NOT NULL DEFAULT '0' COMMENT '记录的版本';

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('82.lilainqi.sql', '修复 消息中心中标记已读点击无效果的bug 。 （缺少一个字段）' );
