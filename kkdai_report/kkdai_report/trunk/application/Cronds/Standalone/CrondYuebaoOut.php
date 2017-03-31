<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondYuebaoOut&ymdh=20160201"
 *
 * 包含天天赚的复投和转出
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/2 0002
 * Time: 上午 10:30
 */

class CrondYuebaoOut extends \Rpt\Misc\DataCrondGather{
    protected $dbMysql;
    public function init() {
        parent::init();
        $this->_iissStartAfter=2000; // 每小时的第55分 启动
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    public function free() {
        parent::free();
        $this->dbMysql=null;
    }

    protected function gather() {
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $this->printLogOfTimeRang();
        $where = ['create_date]'=>$this->YmdHisFrom, 'create_date['=>$this->YmdHisTo, 'type'=>[-1, 2], 'status'=>[1,7]];
        $arr_poi_id = $db_produce->getCol(\Rpt\Tbname::yuebao_poi, 'poi_id', $where);
        $this->ret->total = sizeof($arr_poi_id);
        if (empty($arr_poi_id)) {
            $this->lastMsg = $this->ret->toString();
            return true;
        }

        $arr_poi_id = array_chunk($arr_poi_id, 1000);
        foreach($arr_poi_id as $group){
            $orders = $db_produce->getRecords(\Rpt\Tbname::yuebao_poi, \Rpt\Fields::$produce_fields_yuebao_poi,['poi_id'=>$group]);
            foreach($orders as $k => $r) {
                $tmp = [];
                $tmp['ordersId'] = $r['poi_id'];
                $tmp['userId'] = $r['customer_id'];
                $tmp['waresId'] = $r['yuebao_id'];
                $tmp['amount'] = $r['amount'];
                $tmp['ymd'] = date('Ymd',strtotime($r['create_date']));
                $tmp['hhiiss'] = date('His', strtotime($r['create_date']));
                $tmp['type'] = $r['type'];
                $tmp['shelfId'] = 0;

//              购买时候的端类型
                if (!isset($r['channel']) || !isset(\Rpt\ClientTypeTrans::$oldClientTYpe[$r['channel']])) {
                    $tmp['clientType'] = \Prj\Consts\ClientType::www;
                }else {
                    $tmp['clientType'] = \Rpt\ClientTypeTrans::clientTypeSearch($r['channel']);
                }

                if (!empty($r['yuebao_id'])){
                    $tmp['yieldStatic'] = $db_produce->getOne(\Rpt\Tbname::yuebao, 'interest', ['yuebao_id' => $r['yuebao_id']])/100;
                    // 产品缺失，利率强制改成0
                    if (empty($tmp['yieldStatic'])){
                        $tmp['yieldStatic'] = 0;
                    }
                }
                $tmp['orderStatus'] = \Prj\Consts\OrderStatus::done;  // TODO: 状态待确认
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $this->dbMysql->addRecord(\Rpt\Tbname::tb_yuebao_out, $tmp);
                    $this->ret->newadd++;
                }catch(\ErrorException $e) {
                    if (\Sooh\DB\Broker::errorIs($e)) {
                        unset($tmp['ordersId']);
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_yuebao_out, $tmp, ['ordersId'=>$r['poi_id']]);
//                        error_log(\Sooh\DB\Broker::lastCmd());
                        $this->ret->newupd++;
                    }else {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }
                }
            }
        }
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }

}
