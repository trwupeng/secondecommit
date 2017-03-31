<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondAccountBill&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/21 0021
 * Time: 下午 2:43
 */

class CrondAccountBill extends \Rpt\Misc\DataCrondGather {

    protected $dbMysql;
    public function init() {
        parent::init();
        $this->_iissStartAfter =1000;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function free () {
        parent::free();
        $this->dbMysql = null;
    }

    protected function gather() {
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $this->printLogOfTimeRang();
        /**
         *  新创建的账单
         *
         */
        $where = ['bill_date]' => $this->YmdHisFrom, 'bill_date[' => $this->YmdHisTo];
        $arr_bill_id = $db_produce->getCol(\Rpt\Tbname::account_bill, 'bill_id', $where);

        $total = $arr_bill_id;
        $upd = [];
        $arr_bill_id = array_chunk($arr_bill_id, 1000);
        if (!empty($arr_bill_id)){
            foreach ($arr_bill_id as $group) {
                $bills = $db_produce->getRecords(\Rpt\Tbname::account_bill, \Rpt\Fields::$produce_fieldds_account_bill, ['bill_id' => $group]);

                foreach ($bills as $r) {
                    $tmp = $this->trans($r);
                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_account_bill, $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['billId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_account_bill, $tmp, ['billId' => $r['bill_id']]);
                            $upd[] = $r['bill_id'];
                            $this->ret->newupd++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }
                }

            }
        }


        /**
         * 还款的账单
         */

        $where = ['payment_date]' => $this->YmdHisFrom, 'payment_date[' => $this->YmdHisTo];
        $arr_bill_id = $db_produce->getCol(\Rpt\Tbname::account_bill, 'bill_id', $where);

        $arr_bill_id = array_chunk($arr_bill_id, 1000);
        if (!empty($arr_bill_id)){
            foreach ($arr_bill_id as $group) {
                $bills = $db_produce->getRecords(\Rpt\Tbname::account_bill, \Rpt\Fields::$produce_fieldds_account_bill, ['bill_id' => $group]);

                foreach ($bills as $r) {
                    if(!in_array($r['bill_id'], $total)){
                        $total[] = $r['bill_id'];
                    }
                    $tmp = $this->trans($r);
                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_account_bill, $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['billId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_account_bill, $tmp, ['billId' => $r['bill_id']]);
                            $upd[] = $r['bill_id'];
                            $this->ret->newupd++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }
                }

            }
        }

        $this->ret->total = sizeof($total);
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;

    }


    private function trans ($r) {
        $tmp = [
            'billId' => $r['bill_id'],
            'aheadAmount' => $r['ahead_amount'],
            'waresId' => $r['bid_id'],
            'ymdBill' => date('Ymd', strtotime($r['bill_date'])),
            'hisBill' => date('His', strtotime($r['bill_date']))-0,
            'billNum' => $r['bill_num'],
            'billType' => $r['bill_type'],
            'userId' => $r['customer_id'],
            'interest' => $r['interest'],
            'lendingId' => $r['lending_id'],
            'overheadCharges'=>$r['overhead_charges'],
            'paymentMoney'=>$r['payment_money'],
            'paymentStatus'=>$r['payment_status'],
            'penaltyInteret'=>$r['penalty_interet'],
            'principal'=>$r['principal'],
            'serviceCharge'=>$r['service_charge'],
            'ymdShouldPay'=>date('Ymd', strtotime($r['shouldpay_date'])),
            'finish'=>$r['finish'],
            'shelfId'=>$r['bid_type'],
            'custInterest'=>$r['cust_interest'],
            'custPrincipal'=>$r['cust_principal'],
            'custPenaltyInteret'=>$r['cust_penalty_interet'],
            'freezeAmount'=>$r['freeze_amount'],
            'freezeOrdId'=>$r['freeze_ord_id'],
            'unfreezeOrdId'=>$r['unfreeze_ord_id'],
            'freezeStatus'=>$r['freeze_status'],

        ];
        if($tmp['paymentStatus']==2020){
            $tmp['paymentStatus']=202;
        }
        if($r['payment_date'] !==NULL){
            $tmp['ymdPayment']=date('Ymd', strtotime($r['payment_date']));
            $tmp['hisPayment']=date('His', strtotime($r['payment_date']))-0;
        }else {
            $tmp['ymdPayment']=0;
            $tmp['hisPayment']=0;
        }
        return $tmp;
    }

}
