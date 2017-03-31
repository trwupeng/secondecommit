<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondAccountInfo&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/26 0026
 * Time: 下午 4:20
 */

class CrondAccountInfo extends \Rpt\Misc\DataCrondGather {
    public function init() {
        parent::init();
//        $this->toBeContinue = false;
        $this->_iissStartAfter = 1800;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected $dbMysql;
    public function free() {
        $this->dbMysql = null;
        parent::free();
    }

    protected function gather()
    {

        $this->printLogOfTimeRang();
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        /**
         * 更新当日创建的用户
         */
        $where = ['update_time]'=>$this->YmdHisFrom, 'update_time['=>$this->YmdHisTo];
        $arr_account = $db_produce->getCol(\Rpt\Tbname::account_info, 'customer_id', $where);
        $total = $arr_account;
        if (!empty($arr_account)) {
            $arr_account = array_chunk($arr_account, 1000);
            foreach($arr_account as $group) {
                // 过滤掉在customer表中不存在的用户 
                $exist_in_customer = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', ['customer_id'=>$group]);
                if (empty($exist_in_customer)) {
                    continue;
                }
                $accounts = $db_produce->getRecords(\Rpt\Tbname::account_info,
                        \Rpt\Fields::$produce_fields_account_info, ['customer_id'=>$exist_in_customer]);

                foreach($accounts as $r) {
                    $tmp = [
                        'userId' => $r['customer_id'],
                        'wallet' => $r['balance'],
                        'total_income' => $r['total_income'],
                        'financial_principal' => $r['financial_principal'],
                        'fbb_principal' => $r['fbb_principal'],
                        'interest' => $r['interest'],
                        'bid_income' => $r['bid_income'],
                        'packet' => $r['packet'],
                    ];
                    try{
                        $copart = \Rpt\User::getCopartnerIdAndContractId($r['customer_id']);
                        $tmp = array_merge($tmp, $copart);
                    }catch(\ErrorException $e) {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }
                    $upd_fields = array_keys($tmp);
                    unset($upd_fields[array_search('userId', $upd_fields)]);
                    $this->dbMysql->ensureRecord(\Rpt\Tbname::tb_user_final, $tmp, $upd_fields);
                }
            }
        }



        /**
         *
         * 更新当日更新的用户
         */

        $where = ['update_time]'=>$this->YmdHisFrom, 'update_time['=>$this->YmdHisTo];
        $arr_account = $db_produce->getCol(\Rpt\Tbname::account_info, 'customer_id', $where);
        $this->ret->total = sizeof($total);
        if (empty($arr_account)) {
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
            return true;
        }


        $arr_account = array_chunk($arr_account, 1000);
        foreach ($arr_account as $group){
            
            // 过滤掉在customer表中不存在的用户
            $exist_in_customer = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', ['customer_id'=>$group]);
            if (empty($exist_in_customer)) {
                continue;
            }
            
            $accounts = $db_produce->getRecords(\Rpt\Tbname::account_info,
                \Rpt\Fields::$produce_fields_account_info, ['customer_id'=>$exist_in_customer]);

            foreach($accounts as $r) {
                $tmp = [
                    'userId' => $r['customer_id'],
                    'wallet' => $r['balance'],
                    'total_income' => $r['total_income'],
                    'financial_principal' => $r['financial_principal'],
                    'fbb_principal' => $r['fbb_principal'],
                    'interest' => $r['interest'],
                    'bid_income' => $r['bid_income'],
                    'packet' => $r['packet'],
                ];
                try{
                    $copart = \Rpt\User::getCopartnerIdAndContractId($r['customer_id']);
                    $tmp = array_merge($tmp, $copart);
                }catch(\ErrorException $e) {
                    error_log($e->getMessage()."\n".$e->getTraceAsString());
                }
                if (!in_array($r['customer_id'], $total)) {
                    $this->ret->total++;
                }

                $upd_fields = array_keys($tmp);
                unset($upd_fields[array_search('userId', $upd_fields)]);
                $this->dbMysql->ensureRecord(\Rpt\Tbname::tb_user_final, $tmp, $upd_fields);
//var_log($tmp, 'tmp>>>>>');
//error_log(\Sooh\DB\Broker::lastCmd());
            }
        }

        $this->toBeContinue = false;
        $this->ret->total = sizeof($total);
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;

    }
}