ALTER TABLE `tb_rights_role`
MODIFY COLUMN `rightsIds`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '权限集' AFTER `roleName`;

