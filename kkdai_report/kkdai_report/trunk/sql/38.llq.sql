USE db_kkrpt;
alter table tb_recharges_final add index index_userId (userId);
alter table tb_recharges_final add index index_ymd (ymd);
alter table tb_recharges_final add index index_hhiiss (hhiiss);
insert into db_kkrpt.tb_config set k='dbsql.ver',v='38.llq' ON DUPLICATE KEY UPDATE v='38.llq';