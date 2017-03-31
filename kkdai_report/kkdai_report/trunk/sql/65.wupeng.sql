-- ------------------------------
-- 修改风控系统所有表中是金额的字段数据类型为bigint(20)
-- ------------------------------

ALTER TABLE `fk_dianxiaotijiangbiao`
MODIFY COLUMN `rongzijinewanyuan`  bigint(20) NULL DEFAULT 0 COMMENT '融资金额[万元]' AFTER `kehuxingming`,
MODIFY COLUMN `shoufeijinewanyuan`  bigint(20) NULL DEFAULT 0 COMMENT '收费金额[万元]' AFTER `yewuleixing`,
MODIFY COLUMN `dangyueheji`  bigint(20) NULL DEFAULT 0 COMMENT '当月合计' AFTER `jibie`,
MODIFY COLUMN `tijiangjine`  bigint(20) NULL DEFAULT 0 COMMENT '提奖金额' AFTER `tijiangbili`,
MODIFY COLUMN `tijiangjine285`  bigint(20) NULL DEFAULT 0 COMMENT '提奖金额285' AFTER `tijiangbili284`,
MODIFY COLUMN `tijiangjine288`  bigint(20) NULL DEFAULT 0 COMMENT '提奖金额288' AFTER `tijiangbili287`,
MODIFY COLUMN `tijiangjine291`  bigint(20) NULL DEFAULT 0 COMMENT '提奖金额291' AFTER `tijiangbili290`,
MODIFY COLUMN `tijiangjine294`  bigint(20) NULL DEFAULT 0 COMMENT '提奖金额294' AFTER `tijiangbili293`;


ALTER TABLE `fk_xianxiabenxifeizhang`
MODIFY COLUMN `yingfujineyuan`  bigint(20) NOT NULL DEFAULT 0 COMMENT '应付金额[元]' AFTER `zhonglei`,
MODIFY COLUMN `yifujineyuan`  bigint(20) NOT NULL DEFAULT 0 COMMENT '已付金额[元]' AFTER `yingfushijian`,
MODIFY COLUMN `qianfuyincang`  bigint(20) NOT NULL DEFAULT 0 COMMENT '欠付[隐藏]' AFTER `yifujineyuan`,
MODIFY COLUMN `yuqifeiyuan`  bigint(20) NOT NULL DEFAULT 0 COMMENT '逾期费[元]' AFTER `yuqililv`,
MODIFY COLUMN `qianfujineyuan`  bigint(20) NOT NULL DEFAULT 0 COMMENT '欠付金额[元]' AFTER `yuqifeiyuan`;

ALTER TABLE `fk_rongzixiangmubiao`
MODIFY COLUMN `lixiyingshouyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '利息_应收[元]',
MODIFY COLUMN `lixishishouyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '利息_应收[元]',
MODIFY COLUMN `fuwufeiyingshouyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '利息_实收[元]',
MODIFY COLUMN `fuwufeishishouyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '服务费_应收[元]',
MODIFY COLUMN `dianzifeijineyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '垫资费_金额[元]',
MODIFY COLUMN `zhongjiefeiyingshouyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '中介费_应收[元]',
MODIFY COLUMN `zhongjiefeishishouyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '中介费_实收[元]',
MODIFY COLUMN `baozhengjinyuan` bigint(20) NOT NULL DEFAULT 0 COMMENT '保证金[元]';

ALTER TABLE `fk_rongzidangan`
MODIFY COLUMN `jiekuanjinewanyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '借款金额[分]';

ALTER TABLE `fk_rongfangchanxinxi`
MODIFY COLUMN `yinhangdiyae` bigint(20) DEFAULT 0 NOT NULL COMMENT '银行抵押额',
MODIFY COLUMN `yinhangshengyue` bigint(20) DEFAULT 0 NOT NULL COMMENT '银行剩余额',
MODIFY COLUMN `jiekuane` bigint(20) DEFAULT 0 NOT NULL COMMENT '借款额';

ALTER TABLE `fk_touzidangan`
MODIFY COLUMN `touziewanyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '投资额[万元]';

ALTER TABLE `fk_yuliufanhuanzhang`
MODIFY COLUMN `gerenyuliu` bigint(20) DEFAULT 0 NOT NULL COMMENT '个人预留',
MODIFY COLUMN `meiqifafang` bigint(20) DEFAULT 0 NOT NULL COMMENT '每期发放',
MODIFY COLUMN `jingliyuliu` bigint(20) DEFAULT 0 NOT NULL COMMENT '经理预留',
MODIFY COLUMN `meiqifafang258` bigint(20) DEFAULT 0 NOT NULL COMMENT '每期发放258',
MODIFY COLUMN `zongjianyuliu` bigint(20) DEFAULT 0 NOT NULL COMMENT '总监预留',
MODIFY COLUMN `meiqifafang261` bigint(20) DEFAULT 0 NOT NULL COMMENT '每期发放261',
MODIFY COLUMN `cunqianguanru` bigint(20) DEFAULT 0 NOT NULL COMMENT '存钱罐[入]';

ALTER TABLE `fk_xianshangxiangmubiao`
MODIFY COLUMN `biaodijineyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '标的金额[分]',
MODIFY COLUMN `fuwufeiyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '服务费[分]',
MODIFY COLUMN `shijidaozhangyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '实际到账[分]',
MODIFY COLUMN `toubiaojineyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '投标金额[分]',
MODIFY COLUMN `kehutoubiaojineyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '客户投标金额[分]';

ALTER TABLE `fk_xianshangbenxifeizhang`
MODIFY COLUMN `lixiyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '利息[分]',
MODIFY COLUMN `feiyongyuanguanlifei` bigint(20) DEFAULT 0 NOT NULL COMMENT '费用[分]_管理费',
MODIFY COLUMN `feiyongyuanzhongjiefei` bigint(20) DEFAULT 0 NOT NULL COMMENT '费用[分]_中介费',
MODIFY COLUMN `feiyongyuanfuwufei` bigint(20) DEFAULT 0 NOT NULL COMMENT '费用[分]_服务费',
MODIFY COLUMN `feiyongyuanqita` bigint(20) DEFAULT 0 NOT NULL COMMENT '费用[分]_其他',
MODIFY COLUMN `hejiyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '合计[元]';

ALTER TABLE `fk_xianshangrijizhang`
MODIFY COLUMN `qichuyueyuan` bigint(20)  DEFAULT 0 NOT NULL COMMENT '期初余额[分]',
MODIFY COLUMN `cunqianguanru` bigint(20) DEFAULT 0 NOT NULL COMMENT '存钱罐[入]',
MODIFY COLUMN `xianxiachongzhiru` bigint(20) DEFAULT 0 NOT NULL COMMENT '线下充值[入]',
MODIFY COLUMN `qiyehuchongzhiru` bigint(20) DEFAULT 0 NOT NULL COMMENT '企业户充值[入]',
MODIFY COLUMN `haoyoufanxianru` bigint(20) DEFAULT 0 NOT NULL COMMENT '好友返现[入]',
MODIFY COLUMN `shoudaofangkuaneru` bigint(20) DEFAULT 0 NOT NULL COMMENT '收到放款额[入]',
MODIFY COLUMN `daoqibenjinru` bigint(20) DEFAULT 0 NOT NULL COMMENT '到期本金[入]',
MODIFY COLUMN `daoqilixiru` bigint(20) DEFAULT 0 NOT NULL COMMENT '到期利息[入]',
MODIFY COLUMN `diaopeizijinru` bigint(20) DEFAULT 0 NOT NULL COMMENT '调配资金[入]',
MODIFY COLUMN `jiedongzijinru` bigint(20) DEFAULT 0 NOT NULL COMMENT '解冻资金[入]',
MODIFY COLUMN `tixianchu` bigint(20) DEFAULT 0 NOT NULL COMMENT '提现[出]',
MODIFY COLUMN `shouxufeichu` bigint(20) DEFAULT 0 NOT NULL COMMENT '手续费[出]',
MODIFY COLUMN `zhuanzhangzijinchu` bigint(20) DEFAULT 0 NOT NULL COMMENT '转账资金[出]',
MODIFY COLUMN `dongjiezijinchu` bigint(20) DEFAULT 0 NOT NULL COMMENT '冻结资金[出]',
MODIFY COLUMN `zhifubenxichu` bigint(20) DEFAULT 0 NOT NULL COMMENT '支付本息[出]',
MODIFY COLUMN `zhifutoubiaochu` bigint(20) DEFAULT 0 NOT NULL COMMENT '支付投标[出]',
MODIFY COLUMN `qimoyueyuan` bigint(20) DEFAULT 0 NOT NULL COMMENT '期末余额[分]';

insert into db_kkrpt.tb_config set k='dbsql.ver',v='65.wupeng' ON DUPLICATE KEY UPDATE v='65.wupeng';