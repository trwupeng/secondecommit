
use db_kkrpt;
alter table tb_user_final add column `amountExtFirstBuy` int(11) not null default 0 comment '第一次购买使用红包金额' after amountFirstBuy;
alter table tb_user_final add column `amountExtSecBuy` int(11) not null default 0 comment '第二次购买使用红包金额' after amountSecBuy;
alter table tb_user_final add column `amountExtLastBuy` int(11) not null default 0 comment '最后一次购买使用红包金额' after amountLastBuy;
alter table tb_user_final add column `amountExtMaxBuy` int(11) not null default 0 comment '最大次购买使用红包金额' after amountMaxBuy;
insert into db_kkrpt.tb_config set k='dbsql.ver',v='48.llq' ON DUPLICATE KEY UPDATE v='48.llq';
