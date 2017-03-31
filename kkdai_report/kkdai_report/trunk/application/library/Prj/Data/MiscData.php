<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/24
 * Time: 14:07
 */

namespace Prj\Data;

class MiscData {

    public static function getActivityDoubleElevenRecords($dateLine){
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $tichuchaojiyonghu = <<<sql
(c.flag <> 1 or c.customer_id in ('e25cb241d31041299d86943d00d55811','7ad2860de77c405ab43f8fc09082d81e')) AND
sql;
        $sql = <<<Sql
select
-- a.poi_id,a.bid_id,a.pay_amount,b.bid_title,b.bid_type,b.bid_status,b.bid_period,a.poi_status,a.create_time,
a.customer_id,SUM((a.amount * b.bid_period / 360 * (case when b.product_type = 1 then 30 else 1 END))) as yearAmount , COUNT(1) as num,
c.customer_name,c.customer_cellphone,c.flag
from bid_poi a
LEFT JOIN bid b on
a.bid_id = b.bid_id
LEFT JOIN customer c ON
a.customer_id = c.customer_id
where
a.create_time BETWEEN '{$dateLine[0]} 00:00:00' and '{$dateLine[1]} 23:59:59' AND
a.poi_status in (601,603,610) AND
(b.bid_type <> 501 or b.bid_type is null) AND
b.bid_status <> 4011 AND
b.bid_status <> 5007 AND
$tichuchaojiyonghu
1=1
GROUP BY a.customer_id
ORDER BY yearAmount desc
limit 10
Sql;
        $db->execCustom(['sql'=>"SET NAMES 'utf8'"]);
        return $db->fetchAssocThenFree($db->execCustom(['sql'=>$sql]));
    }

    public static function getLuodiyeTotal(){
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $sql = <<<Sql
select 'registerTotal' as title ,'截至到实时的总注册人数' as exp ,COUNT(1) as total from customer
union all
select 'newPacketTotal' as title ,'截至到实时的新手红包' as exp , SUM(amount) as total from customer_coupon where source in ('注册奖励','绑卡认证奖励','首充奖励')
union all
select 'highPacketTotal' as title ,'截至到实时的188元红包领取总额' as exp , SUM(amount) as total from customer_coupon where amount = 18800;
Sql;
        $db->execCustom(['sql'=>"SET NAMES 'utf8'"]);
        return $db->fetchAssocThenFree($db->execCustom(['sql'=>$sql]));
    }

    public static function getDingQiBaoAmount($userId , $startDate , $endDate){
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $startDate = date('Y-m-d',strtotime($startDate));
        $endDate = date('Y-m-d',strtotime($endDate));
        $sql = <<<sql
select
a.customer_id,sum(a.amount) as amount ,sum(a.pay_amount) as realAmount , b.product_type,
c.customer_name,c.customer_cellphone,c.flag
from bid_poi a
LEFT JOIN bid b on
a.bid_id = b.bid_id
LEFT JOIN customer c ON
a.customer_id = c.customer_id
where
a.create_time BETWEEN '{$startDate} 00:00:00' and '{$endDate} 23:59:59' AND
a.poi_status in (601,603,610) AND
(b.bid_type <> 501 or b.bid_type is null) AND
b.bid_status <> 4011 AND
b.bid_status <> 5007 AND
b.product_type = 1 AND
a.customer_id = '{$userId}' AND
1 = 1
GROUP BY a.customer_id
sql;
        $sql1 = <<<sql
select 
users.customer_id as userId , 
users.customer_cellphone as phone ,
sum(orders.amount) as amount ,
sum(orders.pay_amount) as realAmount ,
orders.create_time as createTime 
FROM
phoenix.bid_poi as orders
LEFT JOIN phoenix.customer as users
on orders.customer_id = users.customer_id
LEFT JOIN nest.finance as finance
on orders.bid_id = finance.id
WHERE
1=1
AND finance.finance_type = 110
AND orders.poi_status IN ('601','603','610') 
AND orders.poi_type = 0 
AND (orders.bid_type != '501' OR orders.bid_type IS NULL) 
AND users.customer_id = '{$userId}'
AND orders.create_time BETWEEN '{$startDate} 00:00:00' and '{$endDate} 23:59:59'
sql;

        $db->execCustom(['sql'=>"SET NAMES 'utf8'"]);
        $rs = $db->fetchAssocThenFree($db->execCustom(['sql'=>$sql1]))[0];
        return $rs;
    }
}