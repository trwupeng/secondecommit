<?php
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondCapitalForRpt&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/1 0001
 * Time: 下午 1:38
 */
namespace PrjCronds;
class CrondCapitalForRpt extends \Sooh\Base\Crond\Task {
    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter = 1255;
        $this->toBeContinue = true;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected function onRun($dt)
    {
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
        }elseif($this->_isManual) {
            $this->oneday($dt->YmdFull);
        }
        $this->toBeContinue = false;
        return true;
    }

    protected function oneday($ymd) {
        // 先删除这一天的
        $this->dbMysql->delRecords('db_kkrpt.tb_financial_situation', ['ymd'=>$ymd]);

        // 充值
        $sql= 'SELECT contractId, flagUser,sum(amount)as sumAmount FROM `tb_recharges_final`'
            .' right join tb_user_final'
            .' on tb_recharges_final.userId = tb_user_final.userId'
            .' where ymd='.$ymd
            .' and amount >0'
            .' and flagUser!=1'
            .' and orderStatus=39'
            .' group by contractId, flagUser';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $v) {
            $fields = [
                'ymd'=>$ymd,
                'contractId'=>$v['contractId'],
                'flagUser'=>$v['flagUser'],
            ];
            $upd = [
                'copartnerId'=>substr($v['contractId'], 0, 4),
                'rechargeAmount'=>$v['sumAmount']
            ];
            $this->dbMysql->ensureRecord('db_kkrpt.tb_financial_situation',array_merge($fields, $upd), array_keys($upd));
        }

        // 提现
        $sql= 'SELECT contractId, flagUser,sum(amount)as sumAmount FROM `tb_recharges_final`'
            .' right join tb_user_final'
            .' on tb_recharges_final.userId = tb_user_final.userId'
            .' where finishYmd='.$ymd
            .' and amount<0'
            .' and flagUser!=1'
            .' and orderStatus=39'
            .' group by contractId, flagUser';
        $rs = $this->execSql($sql, $this->dbMysql);
        foreach($rs as $v) {
            $fields = [
                'ymd'=>$ymd,
                'contractId'=>$v['contractId'],
                'flagUser'=>$v['flagUser'],
            ];
            $upd = [
                'copartnerId'=>substr($v['contractId'], 0, 4),
                'withdrawAmount'=>$v['sumAmount']
            ];
            $this->dbMysql->ensureRecord('db_kkrpt.tb_financial_situation',array_merge($fields, $upd), array_keys($upd));
        }

        // 在投金额
        // 1. 取出 开始募集日期<=指定日期 的所有标的(排除流标体验金)
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $sql = 'select bid_id from bid'
            . ' where DATE_FORMAT(bid_publish_startdate,\'%Y%m%d\')<='.$ymd
            . ' and bid_type != 501'
            . ' and bid_status != 4011';
        $rs = $this->execSql($sql, $db_produce);
        $bids=[];
        foreach($rs as $v) {
            $bids[] = $v['bid_id'];
        }
        if(empty($bids)) {
            return;
        }
        // 2. 从以上标的中筛选出最后还款时间>=指定日期的标的或者至今仍然没有还的标的.
        $bid_huankuanzhong = $db_produce->getCol('phoenix.bid', 'bid_id', ['bid_id'=>$bids,'bid_status'=>[5002, 5003, 5004]]);
        $bid_huanqing = $db_produce->getCol('phoenix.account_bill', 'distinct(bid_id)', ['bid_id'=>$bids, 'date_format(payment_date, \'%Y%m%d\')]'=>$ymd]);
        $bids = array_unique(array_merge($bid_huankuanzhong, $bid_huanqing));
        if(empty($bids)) {
            return;
        }
        // 3. 计算在投金额
        $bids = '(\''.implode('\',\'', $bids).'\')';
        $sql = 'select contractId,flagUser, sum(amount+amountExt) as sumAmount from tb_user_final'
            . ' LEFT JOIN tb_orders_final'
            . ' on tb_user_final.userId = tb_orders_final.userId'
            . ' where ymd <='.$ymd
            . ' and waresId in'.$bids
            . ' and flagUser!=1'
            . ' and poi_type=0'
            . ' group by contractId, flagUser';

        $zaitou = $this->execSql($sql, $this->dbMysql);
//var_log($zaitou, 'zaitou####');
        foreach($zaitou as $v) {
            $fields = [
                'ymd'=>$ymd,
                'contractId'=>$v['contractId'],
                'flagUser'=>$v['flagUser'],
            ];
            $upd = [
                'investmentingAmount'=>$v['sumAmount']
            ];
            $this->dbMysql->ensureRecord('db_kkrpt.tb_financial_situation',array_merge($fields, $upd), array_keys($upd));
        }


    }

    protected function execSql($sql, $db) {
        $result = $db->execCustom(['sql'=>$sql]);
        $rs = $db->fetchAssocThenFree($result);
        return $rs;
    }
}