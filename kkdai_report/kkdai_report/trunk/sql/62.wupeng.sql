DROP TABLE IF EXISTS `fk_xianshangbenxifeizhang`;
CREATE TABLE `fk_xianshangbenxifeizhang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `biaodimingcheng` varchar(40) NOT NULL DEFAULT '' COMMENT '标的名称',
  `jiekuanren` varchar(40) NOT NULL DEFAULT '' COMMENT '借款人',
  `qishu` int(11) NOT NULL DEFAULT '0' COMMENT '期数',
  `zhifushijian` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `lixiyuan` int(11) NOT NULL DEFAULT '0' COMMENT '利息[元]',
  `feiyongyuanguanlifei` int(11) NOT NULL DEFAULT '0' COMMENT '费用[元]_管理费',
  `feiyongyuanzhongjiefei` int(11) NOT NULL DEFAULT '0' COMMENT '费用[元]_中介费',
  `feiyongyuanfuwufei` int(11) NOT NULL DEFAULT '0' COMMENT '费用[元]_服务费',
  `feiyongyuanqita` int(11) NOT NULL DEFAULT '0' COMMENT '费用[元]_其他',
  `hejiyuan` int(11) NOT NULL DEFAULT '0' COMMENT '合计[元]',
  `haikuanqingkuang` tinyint(4) NOT NULL DEFAULT '0' COMMENT '还款情况',
  `beizhu` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态位',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='线上本息费账';
DROP TABLE IF EXISTS `fk_dianxiaotijiangbiao`;
CREATE TABLE `fk_dianxiaotijiangbiao` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `yewubianhao` varchar(16) DEFAULT '' COMMENT '业务编号',
  `kehuxingming` varchar(16) DEFAULT '' COMMENT '客户姓名',
  `rongzijinewanyuan` int(11) DEFAULT '0' COMMENT '融资金额[万元]',
  `hezuoyinhang` varchar(16) DEFAULT '' COMMENT '合作银行',
  `yewuleixing` tinyint(4) DEFAULT '0' COMMENT '业务类型',
  `shoufeijinewanyuan` int(11) DEFAULT '0' COMMENT '收费金额[万元]',
  `shoufeiriqi` int(11) DEFAULT '0' COMMENT '收费日期',
  `jiedanren` varchar(16) DEFAULT '' COMMENT '接单人',
  `jibie` varchar(16) DEFAULT '' COMMENT '级别',
  `dangyueheji` int(11) DEFAULT '0' COMMENT '当月合计',
  `tijiangbili` varchar(16) DEFAULT '' COMMENT '提奖比例',
  `tijiangjine` varchar(32) DEFAULT '' COMMENT '提奖金额',
  `tandanren` varchar(16) DEFAULT '' COMMENT '谈单人',
  `tijiangbili284` varchar(16) DEFAULT '' COMMENT '提奖比例284',
  `tijiangjine285` varchar(32) DEFAULT '' COMMENT '提奖金额285',
  `gendanren` varchar(16) DEFAULT '' COMMENT '跟单人',
  `tijiangbili287` varchar(16) DEFAULT '' COMMENT '提奖比例287',
  `tijiangjine288` varchar(32) DEFAULT '' COMMENT '提奖金额288',
  `zuodanren` varchar(16) DEFAULT '' COMMENT '做单人',
  `tijiangbili290` varchar(16) DEFAULT '' COMMENT '提奖比例290',
  `tijiangjine291` varchar(32) DEFAULT '' COMMENT '提奖金额291',
  `bumenjingli` varchar(16) DEFAULT '' COMMENT '部门经理',
  `tijiangbili293` varchar(16) DEFAULT '' COMMENT '提奖比例293',
  `tijiangjine294` varchar(32) DEFAULT '' COMMENT '提奖金额294',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dianxiaotijiangbiao_yewubianhao` (`yewubianhao`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='电销提奖表';
DROP TABLE IF EXISTS `fk_ronghuifangjilu`;
CREATE TABLE `fk_ronghuifangjilu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `kehubianhao` int(11) NOT NULL DEFAULT '0' COMMENT '客户编号',
  `kehu` varchar(40) NOT NULL DEFAULT '' COMMENT '客户',
  `huifangshijian` varchar(36) DEFAULT '' COMMENT '回访时间',
  `huifangfangshi` varchar(40) DEFAULT '' COMMENT '回访方式',
  `huifangrenyuan` varchar(40) DEFAULT '' COMMENT '回访人员',
  `huifangqingkuang` varchar(255) DEFAULT '' COMMENT '回访情况',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='融回访记录';
DROP TABLE IF EXISTS `fk_rongzikehumingce`;
CREATE TABLE `fk_rongzikehumingce` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `bianhao` int(11) NOT NULL DEFAULT '0' COMMENT '编号',
  `xingming` varchar(40) DEFAULT '' COMMENT '姓名',
  `weihuren` varchar(40) DEFAULT '' COMMENT '维护人',
  `guishuren` varchar(40) DEFAULT '' COMMENT '归属人',
  `yuanguishu` varchar(40) DEFAULT '' COMMENT '原归属',
  `jieshaoren` varchar(40) DEFAULT '' COMMENT '介绍人',
  `zaibaoqingkuang` tinyint(4) DEFAULT '0' COMMENT '在保情况',
  `jieqingriqi` varchar(16) DEFAULT '' COMMENT '结清日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='融资客户名册';
CREATE TABLE `fk_touzikehumingce` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `xingming` varchar(16) DEFAULT '' COMMENT '姓名',
  `zhengjianhaoma` varchar(18) DEFAULT '' COMMENT '证件号码',
  `lianxidianhua` varchar(11) DEFAULT '' COMMENT '联系电话',
  `yinhangzhanghao` varchar(32) DEFAULT '' COMMENT '银行账号',
  `kaihuxingxinxi` varchar(32) DEFAULT '' COMMENT '开户行信息',
  `jiatingzhuzhi` varchar(64) DEFAULT '' COMMENT '家庭住址',
  PRIMARY KEY (`id`),
  UNIQUE KEY `touzikehumingce_xingming` (`xingming`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投资客户名册';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='62.wupeng' ON DUPLICATE KEY UPDATE v='62.wupeng';