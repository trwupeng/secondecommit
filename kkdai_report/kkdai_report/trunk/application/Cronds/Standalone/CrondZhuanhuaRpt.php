<?php
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondZhuanhuaRpt&ymdh=20160826"
 * Created by PhpStorm.
 * User: tao.man
 * Date: 2016/9/2 0002
 * Time: 下午 03:53
 */

namespace PrjCronds;

class CrondZhuanhuaRpt extends \Sooh\Base\Crond\Task {
    //报表数据源
    private $dbMysql;
    //生产数据源
    private $dbProduce;

    public function init() {
        parent::init();
        //ii 分钟 ss 秒
        //即: 每小时的 16分55秒 执行
        $this->_iissStartAfter = 1655;
        //是否只执行一次
        $this->toBeContinue = true;

        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->ret->newadd = 0;
        $this->ret->newupd = 0;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $this->dbProduce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
    }

    protected function onRun($dt) {
        if (!$this->_isManual && $dt->hour<=6) {  // TODO 上线修改称<=6
            $dt0 = strtotime($dt->YmdFull);
            switch($dt->hour) {
                case 1: $this->oneday(date('Ymd', $dt0-86400*10));break; // case 1
                case 2: $this->oneday(date('Ymd', $dt0-86400*7));break;  // case 2
                case 3: $this->oneday(date('Ymd', $dt0-86400*1));break;  // case 3
                case 4: $this->oneday(date('Ymd', $dt0-86400*4));break;  // case 4
                case 5: $this->oneday(date('Ymd', $dt0-86400*3));break;  // case 5
                case 6: $this->oneday(date('Ymd', $dt0-86400*2));break;  // case 6
            }
        } elseif ($this->_isManual) {
            $this->oneday($dt->YmdFull);
        }
        $this->toBeContinue = false;
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }
    protected function oneday($ymd) {
        $records = [];

        //当天注册数
        $sql = 'select contractId, count(1) as regCount from tb_user_final '.
               'where ymdReg = '.$ymd.' and flagUser != 1 '.
               'group by contractId';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $r) {
            $records[$r['contractId']]['regCount'] += $r['regCount'];
        }

        // 实名人数
        $sql = 'select contractId, count(*) as realnameCount from tb_user_final '
            . 'where ymdReg='.$ymd.' and ymdRealName=ymdReg and flagUser!=1 '
            . 'group by contractId';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $r) {
            $records[$r['contractId']]['realnameCount'] += $r['realnameCount'];
        }

        // 绑卡人数
        $sql = 'select contractId, count(*) as bindcardCount from tb_user_final '
             . 'where ymdReg='.$ymd.' and ymdBindcard=ymdReg and flagUser!=1 '
             . 'group by contractId';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $r) {
            $records[$r['contractId']]['bindcardCount'] += $r['bindcardCount'];
        }

        //当天注册充值数
        $sql = 'select contractId, count(1) as regCount from tb_user_final '.
               'where ymdReg = '.$ymd.' and flagUser !=  1 '.
               'and ymdFirstRecharge = ymdReg '.
               'group by contractId';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $r) {
            $records[$r['contractId']]['rechargeCount'] += $r['regCount'];
        }

        //当天注册投资数
        $sql = 'select contractId, count(1) as regCount from tb_user_final '.
               'where ymdReg = '.$ymd.' and flagUser != 1 '.
               'and ymdFirstBuy = ymdReg '.
               'group by contractId';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $r) {
            $records[$r['contractId']]['buyCount'] += $r['regCount'];
        }
//var_log($records, 'records#####');
        $this->dbMysql->delRecords('db_kkrpt.tb_zhuanhua', ['ymd'=>$ymd]);
        foreach($records as $cid => $r) {
            $fields = [
                'ymd'=>$ymd,
                'contractId'=>$cid,
            ];

            $upd = [
                'copartnerId'=>substr($cid, 0, 4),
                'registerCount'=>(empty($r['regCount'])?0:$r['regCount']),
                'realnameCount'=>(empty($r['realnameCount'])?0:$r['realnameCount']),
                'bindcardCount'=>(empty($r['bindcardCount'])?0:$r['bindcardCount']),
                'newRechargeCount'=>(empty($r['rechargeCount'])?0:$r['rechargeCount']),
                'newBuyCount'=>(empty($r['buyCount'])?0:$r['buyCount']),
            ];

            $this->dbMysql->ensureRecord('db_kkrpt.tb_zhuanhua', array_merge($fields, $upd), array_keys($upd));
            $this->ret->newupd++;
        }
    }

    protected function execSql($sql, $db) {
        $result = $db->execCustom(['sql'=>$sql]);
        $rs = $db->fetchAssocThenFree($result);
        return $rs;
    }
}