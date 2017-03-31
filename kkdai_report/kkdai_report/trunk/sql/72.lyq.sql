use db_kkrpt;

CREATE TABLE `tb_sync_user` (
`userId`  varchar(40) NOT NULL COMMENT '用户ID' ,
`userType`  varchar(40) NOT NULL COMMENT '用户所属平台类型' ,
`userName`  varchar(40) NOT NULL DEFAULT '' COMMENT '用户姓名' ,
`userCode`  varchar(40) NOT NULL DEFAULT '' COMMENT '用户姓名' ,
`loginName`  varchar(40) NOT NULL DEFAULT '' COMMENT '用户登录名' ,
`orgAccountId`  varchar(40) NOT NULL DEFAULT '' COMMENT '所属单位ID' ,
`orgAccountName`  varchar(80) NOT NULL DEFAULT '' COMMENT '所属单位名' ,
`orgShortName`  varchar(40) NOT NULL DEFAULT '' COMMENT '所属单位短名字' ,
`createdAt`  int(11) NOT NULL DEFAULT 0 COMMENT '创建时间' ,
`updatedAt`  int(11) NOT NULL DEFAULT 0 COMMENT '更新时间' ,
PRIMARY KEY (`userId`, `userType`)
)
ENGINE=InnoDB
COMMENT='其他平台的账号信息';

ALTER TABLE `tb_managers_0`
ADD COLUMN `oa`  VARCHAR(40) NULL DEFAULT '' AFTER `dept`;

ALTER TABLE `tb_managers_0`
ADD COLUMN `ec` VARCHAR(40) DEFAULT '' AFTER `oa`;

INSERT INTO `db_kkrpt`.`tb_menu` (`mark`, `name`, `value`, `iRecordVerID`, `statusCode`, `alias`) VALUES ('OA', 'OA.OA登录', '[\"manage\",\"oa\",\"ssooa\",[],[]]', '1', '0', 'index');

insert into `tb_rpt_database_ver` (`ver_id`, `intro`) values ('72.lyq.sql', '建立OA本地库，添加OA登录菜单');