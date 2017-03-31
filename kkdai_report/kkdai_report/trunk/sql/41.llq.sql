USE db_kkrpt;
alter table tb_contract_0 add flgDisplay TINYINT(1) not null default 0  comment '是否显示此协议的数据给渠道看  0 否，1  是'  after promotionWay;
insert into db_kkrpt.tb_config set k='dbsql.ver',v='41.llq' ON DUPLICATE KEY UPDATE v='41.llq';