use db_kkrpt;
CREATE TABLE `tb_umeng_data` (
  `ymd` varchar(36) NOT NULL DEFAULT '' COMMENT '日期',
  `channels` varchar(36) NOT NULL DEFAULT '' COMMENT '渠道id',
  `ids` varchar(36) NOT NULL DEFAULT '' COMMENT 'ids',
  `new_user` varchar(36) NOT NULL DEFAULT '' COMMENT '新增用户',
  `active_user` varchar(36) NOT NULL DEFAULT '' COMMENT '活跃用户',
  `launches_user` varchar(36) NOT NULL DEFAULT '' COMMENT '启动次数',
  `clientType` varchar(36) NOT NULL DEFAULT '' COMMENT '901:ios,902:安卓'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='有盟数据表';
insert into db_kkrpt.tb_config set k='dbsql.ver',v='50.wupeng' ON DUPLICATE KEY UPDATE v='50.wupeng';
