
-- --------------------------------------------------
-- mb_perf_dst 工作目标表
-- --------------------------------------------------
drop table if exists `mb_perf_dst`;
create table `mb_perf_dst` (
 `id` bigint not null primary key auto_increment comment '唯一id',
 `name` varchar(100) not null default '' comment '项目名字',
 `content` text comment '工作内容',
 `level` tinyint not null default 1 comment '优先级 1 重要并紧急 2 紧急 3 重要 4 普通 5 不重要',
 `type` tinyint not null default 1 comment '目标类型 1 日目标 2 周目标 3 月目标 4 季度目标',
 `dst_date` date not null comment '目标所属的日期',
 `create_time` timestamp not null default 0 comment '创建的时间',
 `update_time` timestamp not null default 0 comment '修改的时间',
 `del_time` timestamp default 0 comment '删除的时间',
 `create_userid` bigint not null default 0 comment '创建者的用户id',
 `userid` bigint not null default 0 comment '目标所属的用户id',
 `del` tinyint default 0 comment '删除标志 0或空 未删除 其他值 删除'
)engine=InnoDB default charset=utf8 comment='工作目标表';

-- --------------------------------------------------
-- mb_perf_dailylog 工作日志表
-- --------------------------------------------------
drop table if exists `mb_perf_dailylog`;
create table `mb_perf_dailylog` (
 `id` bigint not null primary key auto_increment comment '唯一id',
 `name` varchar(100) not null default '' comment '项目名字',
 `content` text comment '工作内容',
 `level` tinyint not null default 0 comment '优先级',
 `type` tinyint not null default 1 comment '工作类型 1 原始计划 2 临时任务',
 `plan_cost` int default 0 comment '计划用时(小时)',
 `real_cost` int default 0 comment '实际用时(小时）',
 `finish` tinyint default 0 comment '完成情况 0或空 未完成 1 完成 2 其它(需要填原因)',
 `finish_reason` varchar(1000) default '' comment '完成情况的说明',
 `log_date` date not null comment '日志所属的日期',
 `create_time` timestamp not null default 0 comment '创建的时间',
 `update_time` timestamp not null default 0 comment '修改的时间',
 `del_time` timestamp not null default 0 comment '删除的时间',
 `userid` bigint not null default 0 comment '日志所属的用户id',
 `del` tinyint default 0 comment '删除标志 0或空 未删除 其他值 删除'
)engine=InnoDB default charset=utf8 comment='工作日志表';

-- --------------------------------------------------
-- mb_message 消息中心
-- --------------------------------------------------
drop table if exists `mb_message`;
create table `mb_message` (
 `id` bigint not null primary key auto_increment comment '唯一id',
 `title` varchar(100) not null default '' comment '标题',
 `content` text comment '消息内容',
 `sendid` varchar(30) not null default '' comment '发送者id',
 `receiverid` varchar(30) not null default '' comment '目标/日志的拥有者',
 `type` smallint not null default 1 comment '消息类型 1点评 2跟踪 3@',
 `batch_type` TINYINT(1) UNSIGNED not null default 1  comment '目标还是日志 1目标 2日志',
 `batchid` bigint not null default 0 comment '目标或日志的批次id',
 `dstid` varchar(1000) not null default '' comment '发送目标id',
 `flag` tinyint(1) UNSIGNED not null default 0 comment '0消息未读 1消息已读',
 `create_time` datetime not null comment '创建时间',
 `read_time` datetime not null comment '标记已读时间',
 `update_time` datetime not null comment '更新事件',
 `arg_smallint` smallint default 0 comment '自定义参数',
 `arg_int` int default 0 comment '自定义参数',
 `arg_bigint` bigint default 0 comment '自定义参数',
 `arg_100` varchar(100) default '' comment '自定义参数',
 `arg_1000` varchar(1000) default '' comment '自定义参数',
 `arg_10000` text comment '自定义参数',
 `sLockData` varchar(255) NOT NULL DEFAULT ''
)engine=InnoDB default charset=utf8 comment='消息中心';

-- --------------------------------------------------
-- mb_perf_reply 目标/日志回复表
-- --------------------------------------------------
drop table if exists `mb_perf_reply`;
create table `mb_perf_reply` (
 `id` bigint not null primary key auto_increment comment '唯一id',
 `content` varchar(1000) default '' comment '内容',
 `sendid` varchar(30) not null default '' comment '发送者id',
 `dstid` varchar(1000) default '' comment '@的id列表',
 `parentid` bigint default 0 comment '父消息id',
 `type` tinyint not null default 1 comment '类型 1点评  2跟踪  3目标',
 `batch_type` TINYINT(1) UNSIGNED not null default 1  comment '目标还是日志 1目标 2日志',
 `batchid` bigint not null default 0 comment '目标或日志的批次id',
 `receiverid` varchar(30) not null default '' comment '目标/日志的拥有者',
 `create_time` datetime DEFAULT NULL comment '创建时间'
)engine=InnoDB default charset=utf8 comment='目标/日志回复表';

insert into `tb_rpt_database_ver` (`ver_id`, `intro`)
values ('74.lilianqi.sql', '目标管理相关表');