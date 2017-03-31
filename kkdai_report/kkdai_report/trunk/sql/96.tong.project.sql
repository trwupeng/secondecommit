create table if not exists `tb_project` (`id` bigint not null auto_increment comment '项目id ', `name` varchar(100) not null comment '项目名字 ', `startDate` date not null comment '项目开始时间 ', `endDate` date not null comment '项目结束时间 ', `intro` blob(60000) comment '项目介绍 ', `userlist` varchar(5000) comment '用户列表 ', `managerlist` varchar(5000) comment '管理员列表 ', `iRecordVerID` bigint, `create_time` datetime, `update_time` datetime, `del` tinyint, primary key(`id`) );
