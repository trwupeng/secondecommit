create table if not exists `tb_oaTicket` (`ticket` char(50) not null, `oaLoginName` varchar(200) not null, `iRecordVerID` int(11), primary key(`ticket`), index index_ticket(`ticket`) );
