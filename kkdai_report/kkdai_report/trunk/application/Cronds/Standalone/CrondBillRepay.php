<?php
namespace PrjCronds;
/**
 * 还款记录
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondBillRepay&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/21 0021
 * Time: 下午 2:43
 */

class CrondBillRepay extends \Rpt\Misc\DataCrondGather {

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
         *  还款记录
         *
         */
        $where = ['payment_date]' => $this->YmdHisFrom, 'payment_date[' => $this->YmdHisTo];
        $arr_history_id = $db_produce->getCol('phoenix.bill_repay_history', 'history_id', $where);
error_log(\Sooh\DB\Broker::lastCmd());
        $total = sizeof($arr_history_id);
        $arr_history_id = array_chunk($arr_history_id, 1000);
        $fields = [
            'history_id','bill_id','bill_num','shouldpay_date','customer_id','finish','payment_date','interest','money',
            'order_id','penalty_interet','poi_id','principal','serial_id','status','add_interest','poi_type',
        ];
        if (!empty($arr_history_id)){
            foreach ($arr_history_id as $group) {
                $repayRecords = $db_produce->getRecords('phoenix.bill_repay_history', $fields, ['history_id' => $group]);

                foreach ($repayRecords as $r) {
                    $tmp = $this->trans($r);
                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord('db_kkrpt.tb_bill_repay_history', $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['historyId']);
                            $this->dbMysql->updRecords('db_kkrpt.tb_bill_repay_history', $tmp, ['historyId' => $r['history_id']]);
                            $this->ret->newupd++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }
                }

            }
        }

        $this->ret->total = $total;
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;

    }


    private function trans ($r) {
        $tmp = [
            'historyId' => $r['history_id'],
            'billId' => $r['bill_id'],
            'billNum' => $r['bill_num'],
            'userId' => $r['customer_id'],
            'finish' => $r['finish'],
            'amount'=>$r['money'],
            'interest'=>$r['interest'],
            'addInterest'=>$r['add_interest'],
            'penaltyInteret'=>$r['penalty_interet'],
            'ordersId'=>$r['poi_id'],
            'principal'=>$r['principal'],
            'serialId'=>$r['serial_id'],
            'status'=>$r['status'],
            'poi_type'=>$r['poi_type'],
            'order_Id'=>$r['order_id'],
        ];

        if(!empty($r['shouldpay_date'])){
            $tmp['ymdShouldPay'] = date('Ymd', strtotime($r['shouldpay_date']));
        }else {
            $tmp['ymdShouldPay'] = 0;
        }

        if(!empty($r['payment_date'])){
            $tmp['ymdPayment'] = date('Ymd', strtotime($r['payment_date']));
        }else {
            $tmp['ymdPayment'] = 0;
        }
        $poi = $this->dbMysql->getRecord('db_kkrpt.tb_orders_final', 'waresId,amount,amountExt', ['ordersId'=>$r['poi_id']]);
        if(!empty($poi)){
            $tmp['orderAmount'] = $poi['amount']-0;
            $tmp['orderAmountExt'] = $poi['amountExt']-0;
            $tmp['orderAmountSum'] = $poi['amount']+$poi['amountExt'];
            $tmp['waresId'] = $poi['waresId'];
        }
        return $tmp;
    }

}
