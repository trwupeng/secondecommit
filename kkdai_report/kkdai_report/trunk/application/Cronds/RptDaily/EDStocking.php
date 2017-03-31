<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDStocking&ymdh=20160126"
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/26 0026
 * Time: 下午 5:26
 */

class EDStocking extends \Sooh\Base\Crond\Task{
    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 600;
        $this->_iissStartAfter = 55;

        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db = \Sooh\DB\Broker::getInstance();
    }

    public function free() {
        $this->db = null;
        parent::free();
    }

    protected function onRun($dt)
    {
        $this->oneday($dt->YmdFull);
        if (!$this->_isManual && $dt->hour <= 6) {
            $dt0 = strtotime($dt->YmdFull);
            switch($dt->hour) {
                case 1: $this->oneday(date('Ymd',$dt0-86400*10));break;
                case 2: $this->oneday(date('Ymd',$dt0-86400*7));break;
                case 3: $this->oneday(date('Ymd',$dt0-86400*4));break;
                case 4: $this->oneday(date('Ymd',$dt0-86400*3));break;
                case 5: $this->oneday(date('Ymd',$dt0-86400*2));break;
                case 6: $this->oneday(date('Ymd',$dt0-86400*1));break;
            }
        }
        return true;
    }

    protected function oneday($ymd) {
error_log('################################'.$ymd.' '.__CLASS__);
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);

        $rechargeDay = \Rpt\EvtDaily\ChargeAmount::getCopy('ChargeAmount');
        $where = 'ymd='.$ymd.' and amount>0 and orderStatus=39 ';
        $result = $this->getResult($this->sqlBase($where));
        if (!empty($result)) {
            foreach($result as $r) {
                $rechargeDay->add($r['n'], 0, $r['copartnerId'], 0);
            }
            $rechargeDay->save($db, $ymd);
error_log('recharege total add:'.$rechargeDay->totalAdd);
        }
        $rechargeDay->reset();

        $withdrawDay = \Rpt\EvtDaily\WithdrawAmount::getCopy('WithdrawAmount');
        $where = ' amount<0 and finishYmd='.$ymd.' and orderStatus=39 ';
        $result = $this->getResult($this->sqlBase($where));
        if (!empty($result)) {
            foreach($result as $r){
                $withdrawDay->add(-$r['n'], 0, $r['copartnerId'], 0);
            }
            $withdrawDay->save($db, $ymd);
error_log('withdraw total add:'.$withdrawDay->totalAdd);
        }
        $withdrawDay->reset();

        $applyWithdrawDay = \Rpt\EvtDaily\ApplyWithdrawAmount::getCopy('ApplyWithdrawAmount');
        $where = 'ymd='.$ymd.' and amount<0 ';
        $result = $this->getResult($this->sqlBase($where));
        if (!empty($result)) {
            foreach($result as $r) {
                $applyWithdrawDay->add(-$r['n'], 0, $r['copartnerId'], 0);
            }
            $applyWithdrawDay->save($db, $ymd);
error_log('apply withdraw add:'.$applyWithdrawDay->totalAdd);
        }
        $applyWithdrawDay->reset();

    }

    protected function sqlBase ($where) {
        return 'select sum(tb_recharges_final.amount)/100 as n,tb_user_final.copartnerId,tb_user_final.flagUser '
            .'from tb_recharges_final '
            .'left join tb_user_final on tb_recharges_final.userId=tb_user_final.userId '
            .'where '.$where
            .'and tb_user_final.flagUser != 1 '
            .'group by tb_user_final.copartnerId';

    }
    protected function getResult($sql){
        $result = $this->db->execCustom(['sql'=>$sql]);
        $rs = $this->db->fetchAssocThenFree($result);
        return $rs;
    }
}