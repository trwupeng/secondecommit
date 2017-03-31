<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondOrder&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/14 0014
 * Time: 下午 5:02
 *
 *
 */



class CrondOrder extends \Rpt\Misc\DataCrondGather{
    protected $dbMysql;
    public function init() {
        parent::init();
        $this->_iissStartAfter = 600;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function free() {
        parent::free();
        $this->dbMysql = null;
    }
    protected function gather()
    {
        $db_produce = \Sooh\DB\Broker::getInstance('produce');
        $this->printLogOfTimeRang();
        /**
         *
         * 非天天赚产品订单
         *
         */

        $where = ['create_time]'=>$this->YmdHisFrom, 'create_time['=>$this->YmdHisTo, 'poi_status'=>[601,603,610,607,609]];
// 查询最新购买的订单号
        $arr_poi_id = $db_produce->getCol(\Rpt\Tbname::bid_poi, 'poi_id', $where);
        $noNeedToUpdateOrders = $arr_poi_id; // 下面会继续更新没有到订单最终状态(还款还清或者是流标)的订单
        $this->timeFromTo = "[".$this->YmdHisFrom."-".$this->YmdHisTo."]";
        $totalSize = sizeof($arr_poi_id);
        $users_to_upd = [];

        if(!empty($arr_poi_id)) {
            //      将订单分开查询
            $arr_poi_id = array_chunk($arr_poi_id, 500);

            foreach ($arr_poi_id as $group) {
                $orders = $db_produce->getRecords(\Rpt\Tbname::bid_poi, \Rpt\Fields::$produce_fields_bid_poid, ['poi_id'=>$group]);

                foreach ($orders as $k => $r) {
                    $tmp = [];
                    $tmp['ordersId'] = $r['poi_id'];
                    $tmp['waresId'] = $r['bid_id'];
                    $tmp['shelfId'] = $db_produce->getOne(\Rpt\Tbname::bid, 'product_type', ['bid_id'=>$r['bid_id']]);
                    $tmp['userId']  = $r['customer_id'];
                    $tmp['poi_type'] = $r['poi_type'];
                    // 老版本中， 没有pay_amount, pay_amount 是0
                    if (empty($r['pay_amount'])) {
                        $tmp['amount'] = $r['amount'];
                    }else {
                        $tmp['amount'] = $r['pay_amount']; // 这里是实际投资的金额 amount + amountExt 是用户此订单购买的总额
                        $tmp['amountExt'] = $r['amount'] - $r['pay_amount']; // 赠送的金额
                    }

                    switch($r['poi_status']) {
                        case 601:
                            $tmp['orderStatus'] = \Prj\Consts\OrderStatus::payed;
                            break;
                        case 603:
                            $tmp['orderStatus'] = \Prj\Consts\OrderStatus::going;
                            break;
                        case 610:
                            $tmp['orderStatus'] = \Prj\Consts\OrderStatus::done;
                            break;
                        case 609:
                            $tmp['orderStatus'] = \Prj\Consts\OrderStatus::flow;
                            break;
                        default: $tmp['orderStatus'] = $r['poi_status'];
                            break;
                    }

                    $tmp['ymd'] = date('Ymd', strtotime($r['create_time']));
                    $tmp['hhiiss'] = date('His', strtotime($r['create_time']));
//                借款利率
                    $tmp['yieldStatic'] = $db_produce->getOne(\Rpt\Tbname::bid, 'bid_interest', ['bid_id'=>$r['bid_id']]) / 100;
                    if ($r['expect_amount'] >= $r['amount'] ) {
                        $tmp['interest'] = $r['expect_amount'] - $tmp['amount']; // 预计的利润
                    }

                    $firstOrder = \Rpt\User::firstOrder($this->dbMysql, $r['customer_id'], $tmp['shelfId'], $r['poi_id']);
                    $tmp['firstTimeInAll'] = $firstOrder['firstTimeInAll'];
                    $tmp['firstTime'] = $firstOrder['firstTime'];

                    $client_type = \Rpt\ClientTypeTrans::clientTypeSearch($r['channel']);
                    if(!empty($client_type)){
                        $tmp['clientType'] = $client_type;
                    }else{
                        $tmp['clientType'] = \Prj\Consts\ClientType::www;
                    }
                    if($this->dbMysql->getOne(\Rpt\Tbname::tb_orders_final, 'ordersId', ['ordersId'=>$tmp['ordersId']])){
                        $orderId = $tmp['ordersId'];
                        unset($tmp['ordersId']);
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, $tmp, ['ordersId'=>$orderId]);
                        $this->ret->newupd++;
                    }else {
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_orders_final, $tmp);
                        $this->ret->newadd++;
                    }

                    if(!in_array($r['customer_id'], $users_to_upd)) {
                        $users_to_upd [] = $r['customer_id'];
                    }

                }
            }

        }

        /**
         * 更新定期产品中不是订单最终状态的订单信息
         */
        $where = [
            'orderStatus!' => \Prj\Consts\OrderStatus::$finalOrderStatus,
            'shelfId>' => 0,
            'poi_type' => 0,
        ];
        !empty($noNeedToUpdateOrders)
                && $where['ordersId!'] = $noNeedToUpdateOrders;
        $arr_upd_orders = $this->dbMysql->getPair(\Rpt\Tbname::tb_orders_final,
                'ordersId', 'orderStatus', $where);
        !empty($noNeedToUpdateOrders) && $noNeedToUpdateOrders = null;
        if(!empty($arr_upd_orders)) {
            $arr_upd_orders = array_chunk($arr_upd_orders, 1000, true);
            foreach($arr_upd_orders as $group) {
                $orders = $db_produce->getAssoc(\Rpt\Tbname::bid_poi,
                        'poi_id', 'poi_status, customer_id', ['poi_id'=>array_keys($group)]);
                foreach($orders as $ordersId => $value) {
                    $orderStatus = $value['poi_status'];
                    switch($orderStatus) {
                        case 601:
                            $orderStatus = \Prj\Consts\OrderStatus::payed;
                            break;
                        case 603:
                            $orderStatus = \Prj\Consts\OrderStatus::going;
                            break;
                        case 609:
                            $orderStatus = \Prj\Consts\OrderStatus::flow;
                            break;
                        case 610:
                            $orderStatus = \Prj\Consts\OrderStatus::done;
                            break;
                        default: break;
                    }

                    if($orderStatus == $group[$ordersId]){
                        continue;
                    }
                    $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final,
                        ['orderStatus'=>$orderStatus], ['ordersId'=>$ordersId]);
                    if(!in_array($value['customer_id'], $users_to_upd)) {
                        $users_to_upd[] = $value['customer_id'];
                    }
                    $this->ret->newupd++;
                }
            }
        }


        /**
         *
         * 天天赚产品订单
         */
        $where = ['create_date]'=>$this->YmdHisFrom, 'create_date['=>$this->YmdHisTo, 'type'=>1, 'status'=>1];
        $arr_poi_id = $db_produce->getCol(\Rpt\Tbname::yuebao_poi, 'poi_id', $where);
        $this->ret->total = $totalSize + sizeof($arr_poi_id);
        if (empty($arr_poi_id)) {
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        }else {

//        将订单分组
            $arr_poi_id = array_chunk($arr_poi_id, 500);
            foreach ($arr_poi_id as $group) {
                $orders = $db_produce->getRecords(\Rpt\Tbname::yuebao_poi, \Rpt\Fields::$produce_fields_yuebao_poi,['poi_id'=>$group]);
                foreach ($orders as $k => $r) {
                    $tmp = [];
                    $tmp['ordersId'] = $r['poi_id'];
                    $tmp['userId'] = $r['customer_id'];
                    $tmp['waresId'] = $r['yuebao_id'];
                    $tmp['amount'] = $r['amount'];
                    $tmp['ymd'] = date('Ymd',strtotime($r['create_date']));
                    $tmp['hhiiss'] = date('His', strtotime($r['create_date']));
                    $tmp['type'] = $r['type'];
                    $tmp['shelfId'] = 0;
                    $firstOrder = \Rpt\User::firstOrder($this->dbMysql, $r['customer_id'], $tmp['shelfId'], $r['poi_id']);

                    $tmp['firstTime'] = $firstOrder['firstTime'];
                    $tmp['firstTimeInAll'] = $firstOrder['firstTimeInAll'];

//$log= [
//    'firstTimeInAll' => $tmp['firstTimeInAll'],
//    'firstTime' => $tmp['firstTime'],
//];
//var_log($log, $r['customer_id'].' log>>>>>>>>');

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
                    $tmp['orderStatus'] = \Prj\Consts\OrderStatus::payed;  // TODO: 状态待确认
                    try{
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_orders_final, $tmp);
                        $this->ret->newadd++;
                    }catch(\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['ordersId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, $tmp, ['ordersId'=>$r['poi_id']]);
//                        error_log(\Sooh\DB\Broker::lastCmd());
                            $this->ret->newupd++;
                        }else {
                            error_log($e->getMessage()."\n".$e->getTraceAsString());
                        }
                    }

                    if(!in_array($r['customer_id'], $users_to_upd)) {
                        $users_to_upd[] = $r['customer_id'];
                    }

                }
            }
        }



        // 更新用户信息

        if(!empty($users_to_upd)) {

            foreach($users_to_upd as $uid) {
                $upd_user_record = [];
                $record = \Rpt\User::sortBuy($this->dbMysql, $uid);

                if (!empty($record)) {
                    $upd_user_record['ymdFirstBuy'] = $record['ymdFirstBuy'];
                    $upd_user_record['amountFirstBuy'] = $record['amountFirstBuy'];
                    $upd_user_record['amountExtFirstBuy'] = $record['amountExtFirstBuy'];
                    $upd_user_record['shelfIdFirstBuy'] = $record['shelfIdFirstBuy'];

                    $upd_user_record['ymdSecBuy'] = $record['ymdSecBuy'];
                    $upd_user_record['shelfIdSecBuy'] = $record['shelfIdSecBuy'];
                    $upd_user_record['amountSecBuy'] = $record['amountSecBuy'];
                    $upd_user_record['amountExtSecBuy'] = $record['amountExtSecBuy'];

                    $upd_user_record['ymdLastBuy'] = $record['ymdLastBuy'];
                    $upd_user_record['shelfIdLastBuy'] = $record['shelfIdLastBuy'];
                    $upd_user_record['amountLastBuy'] = $record['amountLastBuy'];
                    $upd_user_record['amountExtLastBuy'] = $record['amountExtLastBuy'];
                }

                $record = \Rpt\User::maxBuy($this->dbMysql, $uid);
                if (!empty($record)) {
                    $upd_user_record['ymdMaxBuy'] = $record['ymdMaxBuy'];
                    $upd_user_record['shelfIdMaxBuy'] = $record['shelfIdMaxBuy'];
                    $upd_user_record['amountMaxBuy'] = $record['amountMaxBuy'];
                    $upd_user_record['amountExtMaxBuy'] = $record['amountExtMaxBuy'];
                }


                $tyb = $this->dbMysql->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
                $lb = $this->dbMysql->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['statusCode'=>4011]);
                $tyb = array_merge($tyb, $lb);
                if(!empty($tyb)) {
                    $total_dingqi = $this->dbMysql->getOne(\Rpt\Tbname::tb_orders_final, 'sum(amount)', ['userId'=>$uid, 'waresId!'=>$tyb, 'poi_type'=>0, 'shelfId!'=>0]);
                }else {
                    $total_dingqi = $this->dbMysql->getOne(\Rpt\Tbname::tb_orders_final, 'sum(amount)', ['userId'=>$uid, 'poi_type'=>0, 'shelfId!'=>0]);
                }
                if(empty($total_dingqi)) {
                    $total_dingqi = 0;
                }
                $upd_user_record['dingqiInvestmentTotal'] = $total_dingqi;

                $yuebao_total = $this->dbMysql->getOne(\Rpt\Tbname::tb_orders_final, 'sum(amount)', ['userId'=>$uid, 'shelfId'=>0]);
                $upd_user_record['yuebaoInvestmentTotal'] = empty($yuebao_total) ? 0: $yuebao_total;

                if (!empty($upd_user_record)) {

                    // 更新用户的渠道号
                    try {
                        $tmp_copart = \Rpt\User::getCopartnerIdAndContractId($uid);
                        $upd_user_record = array_merge($upd_user_record, $tmp_copart);
                    }catch(\ErrorException $e) {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }

                    $upd_user_record['userId'] = $uid;
//var_log($upd_user_record, 'upd record>>>>..');
                    $upd_fields = array_keys($upd_user_record);
                    unset($upd_fields[array_search('userId', $upd_fields)]);

                    $this->dbMysql->ensureRecord(\Rpt\Tbname::tb_user_final, $upd_user_record, $upd_fields);
                }
            }

        }


        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;

    }

}