<?php
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondLicaiDay&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/8/29 0029
 * Time: 下午 4:52
 */
namespace PrjCronds;

class CrondLicaiDay extends \Sooh\Base\Crond\Task {

    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter = 1255;
        $this->toBeContinue = true;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected function onRun ($dt) {
        if (!$this->_isManual && $dt->hour<=6) {  //
            $dt0 = strtotime($dt->YmdFull);
            switch($dt->hour) {
                case 1: $this->oneday(date('Ymd', $dt0-86400*10));break; // case 1
                case 2: $this->oneday(date('Ymd', $dt0-86400*7));break;  // case 2
                case 3: $this->oneday(date('Ymd', $dt0-86400*1));break;  // case 3
                case 4: $this->oneday(date('Ymd', $dt0-86400*4));break;  // case 4
                case 5: $this->oneday(date('Ymd', $dt0-86400*3));break;  // case 5
                case 6: $this->oneday(date('Ymd', $dt0-86400*2));break;  // case 6
            }
        }elseif($this->_isManual) {
            $this->oneday($dt->YmdFull);
        }
        $this->toBeContinue = false;
        return true;
    }

    /**
     * @param $ymd
     */
    protected function oneday ($ymd) {
error_log('CrondLicaiCount:'.$ymd.' '.date('Y-m-d H:i:s'));
        // 当天注册首次投资
        $where = ['ymdReg'=>$ymd, 'ymdFirstBuy'=>$ymd, 'flagUser!'=>1];
        $this->new_licai($where, 0);
        // 前1天至前5天注册首次投资
        $ymdReg5 = date('Ymd', strtotime($ymd)-5*86400);
        $where = ['ymdReg]'=>$ymdReg5, 'ymdReg<'=>$ymd, 'ymdFirstBuy'=>$ymd, 'flagUser!'=>1];
        $this->new_licai($where, 1);

        // 前6天至前30天注册首次投资
        $ymdReg30 = date('Ymd', strtotime($ymd)-30*86400);
        $where = ['ymdReg]'=>$ymdReg30, 'ymdReg<'=>$ymdReg5, 'ymdFirstBuy'=>$ymd, 'flagUser!'=>1];
        $this->new_licai($where, 2);

        // 前31天及更久注册首次投资
        $where = ['ymdReg<'=>$ymdReg30, 'ymdFirstBuy'=>$ymd, 'flagUser!'=>1];
        $this->new_licai($where, 4);

//        $this->licai_count($ymd);
    }

    /**
     * 新增理财人数
     * @param $where
     * @return mixed
     */
    protected function new_licai ($where, $type) {
        $fields = 'contractId,count(*) as n,shelfIdFirstBuy,ymdFirstBuy,sum(amountFirstBuy+amountExtFirstBuy) as sumAmount,avg(amountFirstBuy+amountExtFirstBuy) as avgAmount';
        $rs = $this->dbMysql->getRecords(\Rpt\Tbname::tb_user_final, $fields, $where,
            'groupby contractId groupby shelfIdFirstBuy');

        foreach($rs as $r) {
            $fields = [
                'ymd' => $r['ymdFirstBuy'],
                'contractId' => $r['contractId'],
                'shelfId'=>$r['shelfIdFirstBuy'],
                'copartnerId'=>substr($r['contractId'], 0, 4),
            ];
            $upd = [];
            switch($type) {
                case 0:
                    $upd['countReg0Day'] = $r['n'];
                    $upd['amountReg0Day'] = $r['sumAmount'];
                    $upd['avgAmountReg0Day'] = $r['avgAmount'];
                    break;
                case 1:
                    $upd['countReg1To5'] = $r['n'];
                    $upd['amountReg1To5'] = $r['sumAmount'];
                    $upd['avgAmountReg1To5'] = $r['avgAmount'];
                    break;
                case 2:
                    $upd['countReg6To30'] = $r['n'];
                    $upd['amountReg6To30'] = $r['sumAmount'];
                    $upd['avgAmountReg6To30'] = $r['avgAmount'];
                    break;
                case 4:
                    $upd['countReg31Plus'] = $r['n'];
                    $upd['amountReg31Plus'] = $r['sumAmount'];
                    $upd['avgAmountReg31Plus'] = $r['avgAmount'];
                    break;
            }

            $this->dbMysql->ensureRecord('db_kkrpt.tb_licai_day', array_merge($fields, $upd), array_keys($upd));
        }
    }

//    protected function licai_count ($ymd) {
//      $sql = ' SELECT contractId, tb_user_final.userId, shelfId, COUNT(ordersId) AS n, sum(amount+amountExt)as sumAmount'
//          .' FROM tb_orders_final'
//          .' right join tb_user_final'
//		  .' on tb_user_final.userId = tb_orders_final.userId'
//		  .' WHERE tb_user_final.userId IN ('
//          .' SELECT userId'
//		  .'      FROM tb_orders_final AS tof LEFT JOIN tb_user_final AS tuf USING (userId)'
//		  .'		WHERE ymd='.$ymd
//          .'      AND flagUser != 1'
//          .'      AND poi_type = 0'
//          .'      AND waresId NOT IN ( SELECT waresId FROM tb_products_final WHERE statusCode = 4011 OR mainType = 501  )'
//          .' )'
//		  .' AND ymd<='.$ymd
//		  .' AND poi_type=0'
//          .' AND waresId NOT IN ( SELECT waresId FROM tb_products_final WHERE statusCode = 4011 OR mainType = 501)'
//          .' group by contractId, tb_orders_final.userId, shelfId';
//
//        $result = $this->dbMysql->execCustom(['sql'=>$sql]);
//        $rs = $this->dbMysql->fetchAssocThenFree($result);
//        $tmpRecords = [];
//        foreach($rs as $r) {
//            $tmpRecords[$r['contractId']][$r['shelfId']][1]['n'] = $r['n'];
//            $tmpRecords[$r['contractId']][$r['shelfId']][1]['sumAmount'] = $r['sumAmount'];
//
//            if($r['n'] == 1) {
//                $tmpRecords[$r['contractId']][$r['shelfId']]['1']['n'] += 1;
//                $tmpRecords[$r['contractId']][$r['shelfId']]['1']['sumAmount'] += $r['sumAmount'];
//            }elseif ($r['n'] >= 2 && $r['n'] <= 5) {
//                $tmpRecords[$r['contractId']][$r['shelfId']]['2_5']['n']+= $r['n'];
//                $tmpRecords[$r['contractId']][$r['shelfId']]['2_5']['sumAmount']+= $r['sumAmount'];
//            }else {
//                $tmpRecords[$r['contractId']][$r['shelfId']]['6+']['n']+= $r['n'];
//                $tmpRecords[$r['contractId']][$r['shelfId']]['6+']['sumAmount']+= $r['sumAmount'];
//            }
//        }
//        foreach($tmpRecords as $cid => $value) {
//            foreach($value as $sid => $v) {
//                foreach($v as $num => $detail) {
//                    $fields = [
//                        'ymd'=>$ymd,
//                        'contractId'=>$cid,
//                        'shelfId'=>$sid,
//                        'copartnerId'=>substr($cid, 0, 4),
//                    ];
//
//
//                    if($num == '1') {
//                        $upd['count1Buy'] = $detail['n'];
//                        $upd['amount1Buy'] = $detail['sumAmount'];
//                        $upd['avgAmount1Buy'] = $detail['sumAmount'] / $detail['n'];
//                    }elseif($num == '2_5'){
//                        $upd['count5Buy'] = $detail['n'];
//                        $upd['amount5Buy'] = $detail['sumAmount'];
//                        $upd['avgAmount5Buy'] = $detail['sumAmount'] / $detail['n'];
//                    }elseif ($num=='6+') {
//                        $upd['count6PlusBuy'] = $detail['n'];
//                        $upd['amount6PlusBuy'] = $detail['sumAmount'];
//                        $upd['avgAmount6PlusBuy'] = $detail['sumAmount'] / $detail['n'];
//                    }
//                    $this->dbMysql->ensureRecord('db_kkrpt.tb_licai_count', array_merge($fields, $upd), array_keys($upd));
//                }
//
//            }
//        }
//    }
}