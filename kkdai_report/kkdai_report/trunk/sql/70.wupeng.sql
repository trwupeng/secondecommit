use db_kkrpt;
ALTER TABLE `fk_touzikehumingce`
MODIFY COLUMN `yinhangzhanghao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '银行账号' AFTER `lianxidianhua`,
MODIFY COLUMN `kaihuxingxinxi`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '开户行信息' AFTER `yinhangzhanghao`;

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
  values ('70.wupeng.sql', '投资客户名册扩大银行账户字段，开户行信息字段长度');