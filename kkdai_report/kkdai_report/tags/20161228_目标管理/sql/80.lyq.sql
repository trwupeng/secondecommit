UPDATE `tb_menu` SET `value` = '["plan","perfdst","index",{"targetTab":"day"},[],"perf"]' WHERE `name` = '目标管理.工作目标';

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('80.lyq.sql', '修正BUG、工作目标默认打开日标签' );
