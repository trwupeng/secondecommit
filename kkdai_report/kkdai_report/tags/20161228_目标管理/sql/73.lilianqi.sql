/*
Navicat MySQL Data Transfer

Source Server         : 报表 - 开发
Source Server Version : 50624
Source Host           : 10.1.1.212:3306
Source Database       : db_kkrpt

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-12-27 09:50:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_menu
-- ----------------------------
DROP TABLE IF EXISTS `tb_menu`;
CREATE TABLE `tb_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mark` varchar(200) DEFAULT NULL COMMENT '标记分类',
  `name` varchar(200) DEFAULT NULL COMMENT '栏目名称',
  `value` text COMMENT '栏目对应值',
  `iRecordVerID` int(11) DEFAULT '0',
  `statusCode` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'index/add/update/delete/import/export',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1007 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='管理后台菜单表';

-- ----------------------------
-- Records of tb_menu
-- ----------------------------
INSERT INTO `tb_menu` VALUES ('1', '客服', '客服.绑卡未购买', '[]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('2', '报表', '报表.访问权限', '[\"report\",\"rptconf\",\"conf\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('3', '报表', '报表.日报（整合版）', '[\"report\",\"rptdailybasic\",\"recent\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('4', '报表', '报表.日常（数字版）', '[\"report\",\"rptdailybasic\",\"recent2\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('5', '渠道管理', '渠道管理.渠道管理', '[\"manage\",\"copartners\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('6', '渠道管理', '渠道管理.协议管理', '[\"manage\",\"contracts\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('7', '渠道管理', '渠道管理.渠道转换', '[\"manage\",\"Copartnerstrans\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('8', '可视化报表', '可视化报表.网页流量', '[\"report\",\"pcsitetraffic\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('9', '可视化报表', '可视化报表.App流量', '[\"report\",\"umengdata\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('10', '可视化报表', '可视化报表.注册至理财人数', '[\"report\",\"regtoinvestmenttrans\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('11', '可视化报表', '可视化报表.注册至理财转化率', '[\"report\",\"regtoinvestmenttransrate\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('12', '可视化报表', '可视化报表.新增理财人数', '[\"report\",\"newfinancial\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('13', '可视化报表', '可视化报表.新增理财金额', '[\"report\",\"newlicaiamount\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('14', '可视化报表', '可视化报表.新增理财人均', '[\"report\",\"newfinancialavg\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('15', '可视化报表', '可视化报表.新老用户理财人数', '[\"report\",\"oldandnewfinancial\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('16', '可视化报表', '可视化报表.新老用户理财金额', '[\"report\",\"oldandnewfinancialamount\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('17', '可视化报表', '可视化报表.新老用户理财人均', '[\"report\",\"oldandnewfinancialavg\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('18', '可视化报表', '可视化报表.资金情况', '[\"report\",\"fundsdata\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('19', '可视化报表', '可视化报表.留存情况', '[\"report\",\"retaineddata\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('20', '可视化报表', '可视化报表.复投人数', '[\"report\",\"compounddata\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('21', '可视化报表', '可视化报表.复投率', '[\"report\",\"compoundrate\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('22', '财务统计(实时)', '财务统计(实时).投标统计(实时)', '[\"report\",\"bidstatisticsrealtime\",\"monthbid\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('23', '财务统计(实时)', '财务统计(实时).流标统计(实时)', '[\"report\",\"liubiaostatisticsrealtime\",\"day\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('24', '财务统计(实时)', '财务统计(实时).优惠券发放(实时)', '[\"report\",\"vouchergrantrealtime\",\"month\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('25', '财务统计(实时)', '财务统计(实时).优惠券使用(实时)', '[\"report\",\"voucheruserealtime\",\"month\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('26', '财务统计(实时)', '财务统计(实时).用户充值/提现统计(实时)', '[\"report\",\"rechdrawmonth\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('27', '财务统计', '财务统计.投标统计', '[\"report\",\"bidstatistics\",\"monthbid\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('28', '财务统计', '财务统计.流标统计', '[\"report\",\"liubiaostatistics\",\"summary \",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('29', '财务统计', '财务统计.用户充值/提现统计', '[\"report\",\"rechdraw\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('30', '财务统计', '财务统计.还款统计-投资人', '[\"report\",\"paymentofinvestor\",\"summary\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('31', '财务统计', '财务统计.还款统计-借款人', '[\"report\",\"paymentofborrower\",\"summary\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('32', '财务统计', '财务统计.用户理财明细(新浪)', '[\"report\",\"userfinancialdetails\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('33', '财务统计', '财务统计.管理费', '[\"report\",\"servicecharge\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('34', '财务统计', '财务统计.优惠券发放', '[\"report\",\"vouchergrant\",\"summary\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('35', '财务统计', '财务统计.优惠券使用', '[\"report\",\"voucheruse\",\"summary\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('36', '财务统计', '财务统计.好友返现', '[\"report\",\"cashbackmonth\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('37', '财务统计', '财务统计.标的放款明细', '[\"report\",\"fangkuan\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('38', '财务统计', '财务统计.服务费', '[\"report\",\"servicefee\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('39', '风控系统', '风控系统.流程单', '[\"risk\",\"liuchengdan\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('40', '风控系统', '风控系统.融资项目表', '[\"risk\",\"rongzixiangmubiao\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('41', '风控系统', '风控系统.线下本息费账', '[\"risk\",\"xianxiabenxifeizhang\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('42', '风控系统', '风控系统.线下日记账', '[\"risk\",\"xianxiarijizhang\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('43', '风控系统', '风控系统.融资档案', '[\"risk\",\"rongzidangan\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('44', '风控系统', '风控系统.融资客户名册', '[\"risk\",\"rongzikehumingce\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('45', '风控系统', '风控系统.融人企信息', '[\"risk\",\"rongrenqixinxi\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('46', '风控系统', '风控系统.融房产信息', '[\"risk\",\"rongfangchanxinxi\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('47', '风控系统', '风控系统.融回访记录', '[\"risk\",\"ronghuifangjilu\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('48', '风控系统', '风控系统.投资项目表', '[\"risk\",\"touzixiangmubiao\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('49', '风控系统', '风控系统.投资档案', '[\"risk\",\"touzidangan\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('50', '风控系统', '风控系统.投资客户名册', '[\"risk\",\"touzikehumingce\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('51', '风控系统', '风控系统.放款提奖表', '[\"risk\",\"fangkuantijiangbiao\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('52', '风控系统', '风控系统.预留返还账', '[\"risk\",\"yuliufanhuanzhang\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('53', '风控系统', '风控系统.电销提奖表', '[\"risk\",\"dianxiaotijiangbiao\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('54', '风控系统', '风控系统.客户经理名册.查看', '[\"risk\",\"kehujinglimingce\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('55', '风控系统', '风控系统.风控经理名册', '[\"risk\",\"fengkongjinglimingce\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('56', '风控系统', '风控系统.放款人名册', '[\"risk\",\"fangkuanrenmingce\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('57', '风控系统', '风控系统.线上项目表', '[\"risk\",\"xianshangxiangmubiao\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('58', '风控系统', '风控系统.线上本息费账', '[\"risk\",\"xianshangbenxifeizhang\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('59', '风控系统', '风控系统.线上日记账', '[\"risk\",\"xianshangrijizhang\",\"index\",[],[],\"fkTabname\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('60', '系统', '系统.管理员一览', '[\"manage\",\"managers\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('61', '系统', '系统.图片', '[\"manage\",\"discuz\",\"index\",[],[]]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('62', '系统', '系统.用户管理', '[\"manage\",\"managerights\",\"update\",{\"form\":1},[],\"rights\"]', '1', '-1', 'update');
INSERT INTO `tb_menu` VALUES ('63', '系统', '系统.用户组.查看', '[\"manage\",\"rightsrole\",\"index\",[],[],\"rights\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('64', '系统', '系统.权限列表', '[\"manage\",\"rights\",\"index\",[],[],\"rights\"]', '1', '-1', 'index');
INSERT INTO `tb_menu` VALUES ('65', '风控系统', '风控系统.客户经理名册.编辑', '[\"risk\",\"kehujinglimingce\",\"update\",[],[],\"fkTabname\"]', '1', '-1', 'update');
INSERT INTO `tb_menu` VALUES ('66', '系统', '系统.用户组.编辑', '[\"manage\",\"rightsrole\",\"update\",{\"form\":1},[],\"rights\"]', '1', '-1', 'index');
INSERT INTO `tb_menu` VALUES ('67', 'OA', 'OA.OA登录', '[\"manage\",\"oa\",\"ssooa\",[],[]]', '1', '-1', 'index');
INSERT INTO `tb_menu` VALUES ('68', '目标管理', '目标管理.工作目标', '[\"plan\",\"perfdst\",\"index\",[],[],\"perf\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('1003', '目标管理', '目标管理.消息中心', '[\"plan\", \"message\",\"index\",[],[]]', '0', '0', 'index');
INSERT INTO `tb_menu` VALUES ('1004', '目标管理', '目标管理.工作日志', '[\"plan\",\"perfdailylog\",\"index\",[],[],\"perf\"]', '1', '0', 'index');
INSERT INTO `tb_menu` VALUES ('1005', '目标管理', '目标管理.EC统计', '[\"plan\",\"ecstat\",\"index\",[],[]]', '1', '0', 'index');
insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('73.lilianqi.sql', '菜单表');