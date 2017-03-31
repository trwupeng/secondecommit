-- 通知渠道

use db_kkrpt;
alter table tb_user_final MODIFY column notifyNewReg varchar(100) not null default '' comment'是否通知合作方标识字段';
update tb_user_final set notifyNewReg = '20150701';

alter table tb_user_final add column cp_id varchar(256) not null default '' comment'渠道传来的参数';

alter table tb_orders_final add column notifyNewOrder varchar(100) not null default '' comment '是否通知合作方标识字段';
update  tb_orders_final set notifyNewOrder = '20150701';


ALTER TABLE `tb_user_final` ADD INDEX index_copartnerId( `copartnerId` );
ALTER TABLE tb_orders_final add index index_notifyneworder (`notifyNewOrder`);
ALTER TABLE tb_user_final add index index_notifynewreg (`notifyNewReg`);

insert into db_kkrpt.tb_config set k='dbsql.ver',v='42.llq' ON DUPLICATE KEY UPDATE v='42.llq';
-- 添加这个字段之后，首先把之前的所有注册用户的这个字段填充一个数据.

