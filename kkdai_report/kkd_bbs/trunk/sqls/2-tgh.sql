ALTER TABLE `pre_common_member_nickname`
ADD COLUMN `bbsId`  int NOT NULL DEFAULT 0 COMMENT '论坛ID' AFTER `pwd`,
ADD COLUMN `kkdpwd`  varchar(20) NOT NULL DEFAULT '' AFTER `bbsId`,
ADD COLUMN `token`  varchar(32) NOT NULL DEFAULT '' AFTER `kkdpwd`;
