<?php
/**
 *
 * 手动的时候, 输入某个月1号, 或者某个周的周一, 抓取的是指定日期上个月或上周的数据
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondLicaiCount&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/8/29 0029
 * Time: 下午 4:52
 */
namespace PrjCronds;

class CrondLicaiCount extends \Sooh\Base\Crond\Task {

    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter = 1255;
        $this->toBeContinue = true;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected function onRun ($dt) {
        $dt0 = strtotime($dt->YmdFull);
        if(!$this->_isManual && $dt->hour<=6) {
            /** 抓取上个月的 */
            if(date('j', $dt0) == 1) {
                switch($dt->hour) {
//                    case 1: $this->licai_count();break; // case 1
//                    case 2: $this->licai_count();break;  // case 2
                    case 3:
                        $ymdTo = date('Ymd', $dt0-86400);
                        $ymdFrom = date('Ym01', $dt0-86400);
                        $this->licai_count($ymdFrom, $ymdTo);
                        break;  // case 3
//                    case 4: $this->licai_count();break;  // case 4
//                    case 5: $this->licai_count();break;  // case 5
//                    case 6: $this->licai_count();break;  // case 6
                }
            }

            /** 抓取上周的 */
            if(date('N',$dt0) == 1) {
                switch($dt->hour) {
//                    case 1: $this->licai_count(date('Ymd', $dt0-86400*10));break; // case 1
//                    case 2: $this->licai_count(date('Ymd', $dt0-86400*7));break;  // case 2
                    case 3:
                        $ymdFrom = date('Ymd', $dt0-7*86400);
                        $ymdTo = date('Ymd', $dt0-86400);
                        $this->licai_count($ymdFrom , $ymdTo);
                        break;  // case 3
//                    case 4: $this->licai_count(date('Ymd', $dt0-86400*4));break;  // case 4
//                    case 5: $this->licai_count(date('Ymd', $dt0-86400*3));break;  // case 5
//                    case 6: $this->licai_count(date('Ymd', $dt0-86400*2));break;  // case 6
                }
            }
        }else {

            if(date('j', $dt0) == 1) {
                $ymdTo = date('Ymd', $dt0-86400);
                $ymdFrom = date('Ym01', $dt0-86400);
                $this->licai_count($ymdFrom, $ymdTo);
            }

            if(date('N',$dt0) == 1) {
                $ymdFrom = date('Ymd', $dt0-7*86400);
                $ymdTo = date('Ymd', $dt0-86400);
                $this->licai_count($ymdFrom, $ymdTo);
            }
        }
        $this->toBeContinue = false;
        return true;
    }

    protected function licai_count ($ymdFrom, $ymdTo)
    {
        error_log(__CLASS__.'###'.$ymdFrom.' '.$ymdTo);
        $super = $this->dbMysql->getCol(\Rpt\Tbname::tb_user_final, 'userId', ['flagUser' => 1]);

        /** 找出时间段内购买的有效产品类型*/
        $where = ['ymd]' => $ymdFrom, 'ymd[' => $ymdTo, 'poi_type' => 0, 'orderStatus' => [8, 10, 39], 'userId!' => $super];
        $waresId = $this->dbMysql->getCol(\Rpt\Tbname::tb_orders_final, 'distinct(waresId)', $where);
        $arr_shelfs = $this->dbMysql->getCol(\Rpt\Tbname::tb_products_final, 'distinct(shelfId)', ['waresId' => $waresId, 'statusCode!' => 4011]);
        $super = null;
        $waresId = null;

//        var_log($arr_shelfs, 'arr_shelfs####');
        if (!empty($arr_shelfs)) {
            $this->dbMysql->delRecords('db_kkrpt.tb_licai_count', ['ymdStart' => $ymdFrom, 'ymdEnd' => $ymdTo]);
        }
        foreach ($arr_shelfs as $shelfId) {
            $sql = 'select contractId,shelfId,'
                . ' count(case when n=1 then userId end) as n1,'
                . ' count(case when n>=2 and n<=5 then userId end) as n5,'
                . ' count(case when n>=6 then userId end) as n6,'
                . ' sum(case when n=1 then sumAmount end) as sum1,'
                . ' sum(case when n>=2 and n<=5 then sumAmount end) as sum5,'
                . ' sum(case when n>=6 then sumAmount end) as sum6'
                . ' from'
                . '  ('
                . '     select contractId, tb_user_final.userId, shelfId, count(*) as n, sum(tb_orders_final.amount+tb_orders_final.amountExt) sumAmount from tb_user_final'
                . '     left join tb_orders_final'
                . '     on tb_user_final.userId = tb_orders_final.userId'
                . '     where tb_user_final.userId in ('
                . '        select DISTINCT(userId) from tb_orders_final'
                . '        where orderStatus in (8, 10, 39)'
                . '        and ymd >=' . $ymdFrom
                . '        and ymd <=' . $ymdTo
                . '        and shelfId='. $shelfId
                . '        and waresId not in (select waresId from tb_products_final where statusCode=4011 or mainType=501)'
                . '        and poi_type = 0'
                . '        and tb_orders_final.userId not in (select tb_user_final.userId from tb_user_final where flagUser = 1)'
                . '      )'
                . '      and waresId not in (select waresId from tb_products_final where statusCode=4011 or mainType=501)'
                . '      and ymd <=' . $ymdTo
                . '      and poi_type = 0'
                . '      and shelfId=' . $shelfId
                . '      group by tb_user_final.contractId, tb_user_final.userId, shelfId'
                . '  )as tmp'
                . '  group by contractId, shelfId';

            $result = $this->dbMysql->execCustom(['sql' => $sql]);
            $rs = $this->dbMysql->fetchAssocThenFree($result);
            foreach ($rs as $r) {
                $fields = [
                    'ymdStart' => $ymdFrom,
                    'ymdEnd' => $ymdTo,
                    'contractId' => $r['contractId'],
                    'shelfId' => $r['shelfId'],
                ];
                $upd = [
                    'copartnerId' => substr($r['contractId'], 0, 4),
                    'count1Buy' => $r['n1'],
                    'count5Buy' => $r['n5'],
                    'count6PlusBuy' => $r['n6'],
                ];
                if ($r['n1'] == 0) {
                    $upd['amount1Buy'] = 0;
                    $upd['avgAmount1Buy'] = 0;
                } else {
                    $upd['amount1Buy'] = $r['sum1'];
                    $upd['avgAmount1Buy'] = $r['sum1'] / $r['n1'];
                }

                if ($r['n5'] == 0) {
                    $upd['amount5Buy'] = 0;
                    $upd['avgAmount5Buy'] = 0;
                } else {
                    $upd['amount5Buy'] = $r['sum5'];
                    $upd['avgAmount5Buy'] = $r['sum5'] / $r['n5'];
                }

                if ($r['n6'] == 0) {
                    $upd['amount6PlusBuy'] = 0;
                    $upd['avgAmount6PlusBuy'] = 0;
                } else {
                    $upd['amount6PlusBuy'] = $r['sum6'];
                    $upd['avgAmount6PlusBuy'] = $r['sum6'] / $r['n6'];
                }

                $this->dbMysql->ensureRecord('db_kkrpt.tb_licai_count', array_merge($fields, $upd), array_keys($upd));
            }
        }
    }
}