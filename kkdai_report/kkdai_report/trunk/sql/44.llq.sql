-- 渠道展示数据 设定规则

use db_kkrpt;

alter table tb_user_final add yuebaoInvestmentTotal bigint(20) not null default 0 comment '天天赚购买总金额';
alter table tb_user_final add dingqiInvestmentTotal bigint(20) not null default 0 comment '定期产品购买总金额';
alter table tb_orders_final add poi_type tinyint(1) not null default 0 comment '是否是天天赚资金购买定期产品';



insert into db_kkrpt.tb_config set k='dbsql.ver',v='44.llq' ON DUPLICATE KEY UPDATE v='44.llq';


