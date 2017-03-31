<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondVoucher&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/23 0023
 * Time: 下午 3:19
 */

class CrondVoucher extends \Rpt\Misc\DataCrondGather {
    protected $dbMysql;
    public function init() {
        parent::init();
        $this->_iissStartAfter = 1500;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function free () {
        parent::free();
        $this->dbMysql = null;
    }

    protected function gather() {
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $this->printLogOfTimeRang();
//        先插入或更新 新建的券
        $where = ['create_date]'=>$this->YmdHisFrom, 'create_date['=>$this->YmdHisTo];
        $arr_vouche_id = $db_produce->getCol(\Rpt\Tbname::customer_coupon, 'id', $where);
        $total= $arr_vouche_id;
        $upd = [];
        if (!empty($arr_vouche_id)){
            $arr_vouche_id = array_chunk($arr_vouche_id, 1000);
            foreach($arr_vouche_id as $group){
                $vouchers = $db_produce->getRecords(\Rpt\Tbname::customer_coupon, \Rpt\Fields::$produce_fields_customer_coupon,
                    ['id'=>$group]);
                foreach ($vouchers as $r) {
                    $tmp = $this->trans($r);

                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_vouchers_final, $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['voucherId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_vouchers_final, $tmp, ['voucherId' => $r['id']]);
                            $upd[] = $r['id'];
                            $this->ret->newupd++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }
                }
                error_log('####### CrondVoucher ### before fee memory usage:'.sprintf('%0.2f MB',memory_get_usage()/1024/1024));
                $vouchers = null;
                gc_collect_cycles();
                error_log('####### CrondVoucher ### after fee memory usage:'.sprintf('%0.2f MB',memory_get_usage()/1024/1024));
            }
        }

//        更新未到期的券
        $where = ['endDate]'=>date('Ymd'), 'status!'=>\Prj\Consts\Voucher::status_used];
        $arr_vouche_id = $this->dbMysql->getCol(\Rpt\Tbname::tb_vouchers_final, 'voucherId', $where);
        if (empty($arr_vouche_id)){
            $this->ret->total = sizeof($total);
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
            return true;
        }

        $arr_vouche_id = array_chunk($arr_vouche_id, 1000);
        foreach($arr_vouche_id as $group) {
            $vouchers = $db_produce->getRecords(\Rpt\Tbname::customer_coupon, \Rpt\Fields::$produce_fields_customer_coupon,
                    ['id'=>$group]);
            foreach($vouchers as $r) {
                $tmp  = $this->trans($r);
                unset($tmp['voucherId']);
                $this->dbMysql->updRecords(\Rpt\Tbname::tb_vouchers_final, $tmp, ['voucherId'=>$r['id']]);
                if (!in_array($r['id'], $upd)) {
                    $this->ret->newupd++;
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
            'voucherId'=>$r['id'],
            'userId'=>$r['customer_id'],
            'title'=>$r['title'],
            'voucherType'=>$r['type'], // TODO: 下周与北京确认 2是加息券与喵叽相同
            'amount'=>$r['amount'],
            'ymdCreate'=>date('Ymd', strtotime($r['create_date'])),
            'orderId'=>$r['poi_id'],
            'startDate'=>date('Ymd', strtotime($r['begin_date'])),
            'endDate'=>date('Ymd', strtotime($r['end_date'])),
            'lowestAmount'=>$r['lowest_amount'],
            'source'=>$r['source'],
            'status'=>$r['status'],
            'clientType'=>$r['channel'],
            'maxAmount'=>$r['max_amount'],
        ];
        if (!empty($r['handle_date'])){
            $tmp['ymdUsed']=date('Ymd', strtotime($r['handle_date']));
        }else{
            $tmp['ymdUsed'] = 0;
        }

        if (!empty($r['channel'])) {
            $clientType = explode(',', $r['channel']);
            foreach ($clientType as $k => $client) {
                $clientType[$k] = \Rpt\ClientTypeTrans::clientTypeSearch($client);
            }

            $tmp['clientType'] = implode(',', $clientType);

        }

        return $tmp;
    }
}
