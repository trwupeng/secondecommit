alter table mb_message modify column read_time datetime default null comment '消息阅读时间';
insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('81.lilainqi.sql', '修改消息表（mb_message）中read_time的默认值是null' );