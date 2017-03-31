USE db_kkrpt;
alter table tb_user_final add column ymdRealName int(11) not null default 0 comment '实名时间' after dtLast;