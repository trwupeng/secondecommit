alter table tb_products_final add column `isNewbie` int(2) DEFAULT '0' comment '是否新手表 1 是, 0 不是';
alter table tb_products_final add column `serviceFee` bigint(20) not null default 0 comment '服务费';
alter table tb_products_final add column `isJiaXi` tinyint(4) NOT NULL DEFAULT '0' COMMENT '加息标';
alter table tb_products_final add column `xInterest` int(11) DEFAULT '0' COMMENT '第一期利率';
alter table tb_products_final add column `xRate` int(11) DEFAULT '0' COMMENT '第一期服务费率';
alter table tb_products_final add column `yInterest` int(11) DEFAULT '0' COMMENT '第二期利率';
alter table tb_products_final add column `yRate` int(11) DEFAULT '0' COMMENT '第二期服务费率';
alter table tb_products_final add column `periodAdd` int(11) DEFAULT 0 COMMENT '第二期天数';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='56.lilianqi' ON DUPLICATE KEY UPDATE v='56.lilianqi';