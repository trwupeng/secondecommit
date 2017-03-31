ALTER TABLE `tb_managers_rights`
ADD COLUMN `roles`  varchar(500) NOT NULL DEFAULT '' COMMENT '角色集' AFTER `rightsType`;