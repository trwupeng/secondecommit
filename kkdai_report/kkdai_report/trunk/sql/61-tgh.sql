use db_kkrpt;
ALTER TABLE `tb_rights_role`
MODIFY COLUMN `rightsIds`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限集' AFTER `roleName`;

ALTER TABLE `tb_managers_rights`
MODIFY COLUMN `rights`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '理管权限' AFTER `roles`;



