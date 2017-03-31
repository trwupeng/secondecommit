-- ------------------------------
-- 修改风控系统融人企信息表
-- ------------------------------
use db_kkrpt;
drop table IF EXISTS `fk_rongrenqixinxi`;
CREATE TABLE `fk_rongrenqixinxi` (
  `id` int(11) not null AUTO_INCREMENT COMMENT '自增id',
  `kehubianhao` int(11) NOT NULL  comment '客户编号',
  `kehu` varchar(40) NOT NULL DEFAULT '' COMMENT '客户',
  `leixing` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型',
  `ming` varchar(80) NOT NULL DEFAULT '' COMMENT '名',
  `guanxi` varchar(40) NOT NULL DEFAULT '' COMMENT '关系',
  `zhengjianhaoma` varchar(255) NOT NULL DEFAULT '' COMMENT '证件号码',
  `xingbie` varchar(2) NOT NULL DEFAULT '-' COMMENT '性别',
  `nianling` int(11) NOT NULL DEFAULT '0' COMMENT '年龄',
  `hunyinzhuangkuang` varchar(40) NOT NULL DEFAULT '' COMMENT '婚姻状况',
  `lianxidianhua` varchar(255) NOT NULL DEFAULT '' COMMENT '联系电话',
  `xianzhuzhi` varchar(255) NOT NULL DEFAULT '' COMMENT '现住址',
  `hujidi` varchar(255) NOT NULL DEFAULT '' COMMENT '户籍地',
  `gongzuodanwei` varchar(255) NOT NULL DEFAULT '' COMMENT '工作单位',
  `danweidizhi` varchar(255) NOT NULL DEFAULT '' COMMENT '单位地址',
  `fadingdaibiaoren` varchar(40) NOT NULL DEFAULT '' COMMENT '法定代表人',
  `shijikongzhiren` varchar(40) NOT NULL DEFAULT '' COMMENT '实际控制人',
  `guquanjiegou` varchar(255) NOT NULL DEFAULT '' COMMENT '股权结构',
  `bangongdizhi` varchar(255) NOT NULL DEFAULT '' COMMENT '办公地址',
  `beizhixingchaxunshijian` int(11) NOT NULL DEFAULT '0' COMMENT '被执行查询时间',
  `zhengxinchaxunshijian` int(11) NOT NULL DEFAULT '0' COMMENT '征信查询时间',
  `beizhu` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '记录添加时间',
  `updateTime` int(11) NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `iRecordVerID` int(11) NOT NULL DEFAULT '0',
  `sLockData` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='融人企信息';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='67.lilianqi' ON DUPLICATE KEY UPDATE v='67.lilianqi';