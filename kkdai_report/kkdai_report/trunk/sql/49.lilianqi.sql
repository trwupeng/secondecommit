-- ------------------------------
-- tb_baidutongji
-- ------------------------------
DROP TABLE IF EXISTS `tb_baidutongji`;
CREATE TABLE `tb_baidutongji` (
  `ymd` int(11) NOT NULL COMMENT '日期',
  `pv_count` int (11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `visitor_count` int (11) NOT NULL DEFAULT 0 COMMENT '访问量',
  `ip_count`int (11) NOT NULL DEFAULT 0 COMMENT 'ip数',
  PRIMARY KEY (`ymd`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='百度统计';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='49.llq' ON DUPLICATE KEY UPDATE v='49.llq';