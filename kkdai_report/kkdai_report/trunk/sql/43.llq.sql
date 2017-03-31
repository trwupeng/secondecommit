-- 渠道展示数据 设定规则

use db_kkrpt;
alter table tb_contract_0 add displayRule tinyint(2) not null default 0 comment '设定隐藏的规则， 0 无规则，1注册，2绑卡， 3购买';
alter table tb_contract_0 add displayPercent tinyint(4) not null default 0 comment '设定要显示的比例';
alter table tb_user_final add flagDisplay tinyint(1) not null default 0 comment'是否展示这个用户的数据给渠道 0 不展示，1 展示';

-- 添加flagDisplay这个字段之后，首先把之前的所有注册用户的这个字段填充一个数据.
update tb_user_final set flagDisplay=1;

insert into db_kkrpt.tb_config set k='dbsql.ver',v='43.llq' ON DUPLICATE KEY UPDATE v='43.llq';


