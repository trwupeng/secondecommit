<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondYuebaoUser&ymdh=20160120"
 *
 * 更新天天赚用户的资金信息到tb_user_final
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/20 0020
 * Time: 下午 3:12
 */

class CrondYuebaoUser extends \Rpt\Misc\DataCrondGather {
    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter = 2300;
        $this->toBeContinue = true;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    public function free() {
        parent::free();
        $this->dbMysql = null;
    }

    protected function gather() {
        if(!$this->_isManual) {
            if($this->dt->hour != 11){
                $this->toBeContinue = false;
                return true;
            }

        }
        $this->YmdHisFrom = $this->ymd.' 00:00:00';
        $this->YmdHisTo = $this->ymd.' 23:59:59';
        $db_produce = \Sooh\DB\Broker::getInstance (\Rpt\Tbname::db_p2p);
        $this->printLogOfTimeRang();
        $where = ['update_date]'=>$this->YmdHisFrom, 'update_date['=>$this->YmdHisTo];
        $arr_user = $db_produce->getCol(\Rpt\Tbname::yuebao_customer, 'customer_id', $where);
        $this->ret->total = sizeof($arr_user);
        $this->timeFromTo = "[".$this->YmdHisFrom."-".$this->YmdHisTo."]";
        if (empty($arr_user)) {
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
            return true;
        }

        $arr_user = array_chunk($arr_user, 1000);
        foreach ($arr_user  as $group) {
            $users = $db_produce->getRecords(\Rpt\Tbname::yuebao_customer, \Rpt\Fields::$produce_fields_yuebao_customer, ['customer_id'=>$group]);
            foreach ($users as $r) {
                $tmp = [
                    'userId'        => $r['customer_id'],
                    'yuebao_total_amount'   => $r['total_amount'],
                    'yuebao_valid_amout'   => $r['valid_amount'],
                ];
                // 更新用户的渠道号
                try {
                    $tmp_copart = \Rpt\User::getCopartnerIdAndContractId($r['customer_id']);
                    $tmp = array_merge($tmp, $tmp_copart);
                }catch(\ErrorException $e) {
                    error_log($e->getMessage()."\n".$e->getTraceAsString());
                }
                $this->dbMysql->ensureRecord(\Rpt\TBname::tb_user_final, $tmp, ['yuebao_total_amount', 'yuebao_valid_amout']);
            }
        }
        $this->toBeContinue = false;
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }
}
