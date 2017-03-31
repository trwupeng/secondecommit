/*
Navicat MySQL Data Transfer

Source Server         : 风控测试服@10.1.1.212
Source Server Version : 50624
Source Host           : 10.1.1.212:3306
Source Database       : db_kkrpt

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-10-26 10:50:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_rights_0
-- ----------------------------
DROP TABLE IF EXISTS `tb_rights_0`;
CREATE TABLE `tb_rights_0` (
  `rightsId` varchar(80) NOT NULL,
  `rightsType` varchar(20) NOT NULL DEFAULT '',
  `rightsName` varchar(80) NOT NULL DEFAULT '',
  `exp` varchar(300) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `iRecordVerID` int(20) NOT NULL DEFAULT '0',
  `sLockData` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '-4 禁用',
  PRIMARY KEY (`rightsId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='子系统权限义定';

-- ----------------------------
-- Records of tb_rights_0
-- ----------------------------
INSERT INTO `tb_rights_0` VALUES ('risk_test2_index', '', '测试-查看', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_test2_update', '', '测试-编辑', '', '4', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongzixiangmubiao_index', '', '融资项目表-查看', '', '4', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_liuchengdan_index', '', '流程单-查看', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianxiabenxifeizhang_index', '', '线下本息费账-查看', '', '4', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongzidangan_index', '', '融资档案-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongzikehumingce_index', '', '融资客户名册-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_kehujinglimingce_index', '', '客户经理名册-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_ronghuifangjilu_index', '', '融回访记录-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongfangchanxinxi_index', '', '融房产信息-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzikehumingce_index', '', '投资客户名册-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fangkuanrenmingce_index', '', '放款人名册-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianxiarijizhang_index', '', '线下日记账-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzidangan_index', '', '投资档案-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_yuliufanhuanzhang_index', '', '预留返还账-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_dianxiaotijiangbiao_index', '', '电销提奖表-查看', '', '4', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangxiangmubiao_index', '', '线上项目表-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangbenxifeizhang_index', '', '线上本息费账-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongzixiangmubiao_update', '', '融资项目表-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzikehumingce_update', '', '投资客户名册-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzikehumingce_del', '', '投资客户名册-删除', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fangkuanrenmingce_update', '', '放款人名册-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianxiabenxifeizhang_update', '', '线下本息费账-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_liuchengdan_update', '', '流程单-编辑', '', '4', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_kehujinglimingce_update', '', '客户经理名册-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzidangan_update', '', '投资档案-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangrijizhang_index', '', '线上日记账-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangrijizhang_delete', '', '线上日记账-删除', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongrenqixinxi_index', '', '融人企信息-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzixiangmubiao_index', '', '投资项目表-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianxiarijizhang_update', '', '线下日记账-更新', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzixiangmubiao_update', '', '投资项目表-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangbenxifeizhang_update', '', '线上本息费账-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangrijizhang_update', '', '线上日记账-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_touzixiangmubiao_del', '', '投资项目表-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangxiangmubiao_update', '', '线上项目表-更新', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_xianshangxiangmubiao_delete', '', '线上项目表-删除', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongzikehumingce_update', '', '融资客户名册-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_ronghuifangjilu_update', '', '融回访记录-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_dianxiaotijiangbiao_update', '', '电销提奖表-编辑', '', '5', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_yuliufanhuanzhang_update', '', '预留返还账-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fengkongjinglimingce_index', '', '风控经理名册-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongrenqixinxi_update', '', '融人企信息-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fangkuantijiangbiao_index', '', '放款提奖表-查看', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fengkongjinglimingce_del', '', '风控经理名册-删除', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fangkuantijiangbiao_update', '', '放款提奖表-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongfangchanxinxi_update', '', '融房产信息-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_rongzidangan_update', '', '融资档案-编辑', '', '2', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fangkuantijiangbiao_del', '', '放款提奖表-删除', '', '4', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fengkongjinglimingce_update', '', '风控经理名册-编辑', '', '3', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_fangkuantijiangbiao_onhetongbianhao', '', 'risk_fangkuantijiangbiao_onhetongbianhao', '', '1', '', '0');
INSERT INTO `tb_rights_0` VALUES ('risk_ronghuifangjilu_del', '', '融回访记录-删除', '', '2', '', '0');
