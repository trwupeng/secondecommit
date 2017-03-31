<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondPacket&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/23 0023
 * Time: 下午 9:12
 */

class CrondPacket extends \Rpt\Misc\DataCrondGather {
    protected $dbMysql;
    public function init() {
        parent::init();
        $this->_iissStartAfter = 800;
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
        $where = ['start_time]'=>$this->YmdHisFrom, 'start_time['=>$this->YmdHisTo];
        $arr_packet_id = $db_produce->getCol(\Rpt\Tbname::account_packet, 'id', $where);
//var_log(\Sooh\DB\Broker::lastCmd());
        $total = $arr_packet_id;
        $upd = [];
        if (!empty($total)) {
            $arr_packet_id = array_chunk($arr_packet_id, 1000);
            foreach ($arr_packet_id as $group) {
                $packets = $db_produce->getRecords(\Rpt\Tbname::account_packet, \Rpt\Fields::$produce_fields_account_packet,
                    ['id' => $group]);
                foreach ($packets as $r) {
                    $tmp = $this->trans($r);

                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_packet_water, $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['packetId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_packet_water, $tmp, ['packetId' => $r['id']]);
                            $upd[] = $r['id'];
                            $this->ret->newupd++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }

                }
            }
        }


        // 更新红包

        $where = ['endDate'=>0];
        $arr_packet_id = $this->dbMysql->getCol(\Rpt\Tbname::tb_packet_water, 'packetId', $where);
        if(empty($arr_packet_id)) {
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
            return true;
        }

        $arr_packet_id = array_chunk($arr_packet_id, 1000);
        foreach ($arr_packet_id as $group) {
            $packets = $db_produce->getRecords(\Rpt\Tbname::account_packet, 'id,end_time',
                ['id'=>$group]);
            foreach($packets as $r) {
                if (empty($r['end_time'])){
                    continue;
                }else {
                    $tmp['endDate'] = date('Ymd', strtotime($r['end_time']));
                    $this->dbMysql->updRecords(\Rpt\Tbname::tb_packet_water, $tmp, ['packetId'=>$r['id']]);
                    if (!in_array($r['id'],$total)){
                        $total[] = $r['id'];
                    }
                    if (!in_array($r['id'], $upd)){
                        $this->ret->newupd++;
                    }
                }
            }
        }

        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }


    private function trans ($r) {
        $tmp = [
            'packetId'=>$r['id'],
            'userId'=>$r['customer_id'],
            'packetDesc'=>$r['packet_desc'],
            'packetType'=>$r['type'],
            'amount'=>$r['amount'],
            'startDate'=>date('Ymd', strtotime($r['start_time'])),

        ];

        if (!empty($r['end_time'])) {
            $tmp['endDate']=date('Ymd', strtotime($r['end_time']));
        }else {
            $tmp['endDate']=0;
        }
        return $tmp;
    }
}
