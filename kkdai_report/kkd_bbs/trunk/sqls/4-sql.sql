ALTER TABLE `pre_common_member_nickname`
ADD COLUMN `kkdname`  varchar(30) NOT NULL DEFAULT '' AFTER `token`,
ADD COLUMN `kkdphone`  int NOT NULL DEFAULT 0 AFTER `kkdname`;


