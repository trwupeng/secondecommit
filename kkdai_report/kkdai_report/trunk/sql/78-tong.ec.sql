create table if not exists `tb_ecRecord` (`id` varchar(50) not null, `contactTime` varchar(50), `customerId` varchar(50), `customerName` varchar(100), `customerCompany` varchar(500), `userId` varchar(50), `content` varchar(20000), primary key(`id`), index index_id(`id`), index index_customerId(`customerId`), index index_userId(`userId`) );

ALTER TABLE `tb_ecRecord`
ADD COLUMN `iRecordVerID`  int NULL DEFAULT 0 AFTER `content`;

