use db_kkrpt;
drop table IF EXISTS `tb_rpt_database_ver`;
CREATE TABLE `tb_rpt_database_ver` (
  `auto_id` int(11) UNSIGNED not null AUTO_INCREMENT comment '自增id',
  `ver_id` varchar(50) NOT NULL COMMENT '是版本号也是主键',
  `intro` varchar(300) NOT NULL DEFAULT '此版本的介绍',
  PRIMARY KEY (`auto_id`),
  UNIQUE (ver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='报表系统的数据库版本记录';
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('30.reset.sql', '将之前的sql全部整合到这个sql文件中');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('31.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('32.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('33.tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('34.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('34.tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('35.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('36.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('36.wn.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('37.tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('38.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('38.tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('39.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('40.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('41.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('42.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('43.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('44.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('45.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('46.llq.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('47.wupeng.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('48.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('49.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('50.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('50.wupeng.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('51.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('52.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('53.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('54.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('55.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('56.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('57.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('58-tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('58.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('59-tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('60-tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('61-tgh.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('62.wupeng.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('63-tgh-rights.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('64-tgh-tmp.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('65.wupeng.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('66-tgh-rights.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('67.lilianqi.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('fengkong.sql', '');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('68.lilianqi.sql', '摒弃sql版本记录方式,使用这种方式:记录每次sql执行的记录.(并将之前的sql文件执行记录补充进来)');