<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondProducts&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/21 0021
 * Time: 下午 2:43
 */

class CrondProducts extends \Rpt\Misc\DataCrondGather {

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
         *
         * 非天天赚产品
         */
        $where = ['bid_create_date]' => $this->YmdHisFrom, 'bid_create_date[' => $this->YmdHisTo];
        $arr_product_id = $db_produce->getCol(\Rpt\Tbname::bid, 'bid_id', $where);

        $total = $arr_product_id;
        $upd = [];
        $arr_product_id = array_chunk($arr_product_id, 1000);
        if (!empty($arr_product_id)){
            foreach ($arr_product_id as $group) {
                $products = $db_produce->getRecords(\Rpt\Tbname::bid, \Rpt\Fields::$produce_fields_bid, ['bid_id' => $group]);

                foreach ($products as $r) {
                    $tmp = $this->trans($r);
                    try {
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_products_final, $tmp);
                        $this->ret->newadd++;
                    } catch (\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)) {
                            unset($tmp['waresId']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_products_final, $tmp, ['waresId' => $r['bid_id']]);
                            $upd[] = $r['bid_id'];
                            $this->ret->newupd++;
                        } else {
                            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
                        }
                    }
                }

            }
        }
        // 更新非天天赚产品
        $where = ['statusCode!'=>[\Prj\Consts\Wares::status_close, \Prj\Consts\Wares::status_failure], 'shelfId>'=>0];
        $arr_product_record = $this->dbMysql->getPair(\Rpt\Tbname::tb_products_final, 'waresId', 'statusCode', $where);
        $arr_product_id = array_keys($arr_product_record);

        $arr_product_id = array_chunk($arr_product_id, 1000);
        foreach($arr_product_id as $group) {
            $prdts = $db_produce->getRecords(\Rpt\Tbname::bid, \Rpt\Fields::$produce_fields_bid, ['bid_id' => $group]);
            foreach($prdts as $r) {
                if (!in_array($r['bid_id'],$total)) {
                    $total[] = $r['bid_id'];
                }
                $tmp = $this->trans($r);
                unset($tmp['waresId']);
                $this->dbMysql->updRecords(\Rpt\Tbname::tb_products_final, $tmp, ['waresId' => $r['bid_id']]);
                if (!in_array($r['bid_id'],$upd)) {
                    $this->ret->newupd++;
                }
            }


        }

        /**
         *
         * 天天赚产品
         */

        $where = ['create_date]'=>$this->YmdHisFrom, 'create_date['=>$this->YmdHisTo];
        $products = $db_produce->getRecords(\Rpt\Tbname::yuebao, \Rpt\Fields::$produce_fields_yuebao, $where);


        if(!empty($products)) {
            foreach ($products as $r) {
                $total[] = $r['yuebao_id'];
                $tmp = $this->yuebao_trans($r);
                try {
                    \Sooh\DB\Broker::errorMarkSkip();
                    $this->dbMysql->addRecord(\Rpt\Tbname::tb_products_final, $tmp);
                    $this->ret->newadd++;
                }catch(\ErrorException $e) {
                    if (\Sooh\DB\Broker::errorIs($e)) {
                        unset($tmp['waresId']);
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_products_final, $tmp, ['waresId'=>$r['yuebao_id']]);
                        $upd[] = $r['yuebao_id'];
                        $this->ret->newupd++;
                    }else {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }
                }
            }
        }
        $where = ['ymdEndReal'=>0, 'shelfId'=>0];
        $arr_ttz_product = $this->dbMysql->getCol(\Rpt\Tbname::tb_products_final, 'waresId', $where);
        if(!empty($arr_ttz_product)) {
            $arr_ttz_product = array_chunk($arr_ttz_product, 1000);
            foreach($arr_ttz_product as $group) {
                foreach($group as $prdtId) {
                    $record = $db_produce->getRecord(\Rpt\Tbname::yuebao, \Rpt\Fields::$produce_fields_yuebao, ['yuebao_id'=>$prdtId]);
                    $tmp = $this->yuebao_trans($record);
                    $create_time = $db_produce->getOne(\Rpt\Tbname::yuebao, 'valid_date', ['yuebao_id'=>$prdtId]);
                    $n = $db_produce->getRecordCount(\Rpt\Tbname::yuebao, ['valid_date>'=>$create_time]);
                    if($n >0) {
                        $lastOrderTime = $db_produce->getOne(\Rpt\Tbname::yuebao_poi, 'create_date',
                            ['status'=>1, 'type'=>1, 'yuebao_id'=>$prdtId], 'rsort create_date');
                        $tmp['ymdEndReal'] = date('Ymd', strtotime($lastOrderTime));
                        $tmp['statusCode'] = \Prj\Consts\Wares::status_go;

                    }else {
                        $tmp['statusCode'] = \Prj\Consts\Wares::status_open;
                    }
                    unset($tmp['waresId']);
                    $this->dbMysql->updRecords(\Rpt\Tbname::tb_products_final, $tmp, ['waresId' => $prdtId]);
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
            'waresId' => $r['bid_id'],
            'waresName' => $r['bid_title'],
            'deadLine' => $r['bid_period'],
            'priceStart' => $r['lowest_amount'],
            'priceStep' => $r['amount_multiple'],
            'amount' => $r['bid_amount'],
            'realRaise' => $r['bid_amount'] - $r['bid_free_amount'],
            'yieldStatic' => $r['bid_interest']/100,
            'shelfId' => $r['product_type'],
            'mainType' => $r['bid_type'],
            'isNewbie'=>$r['is_newbie'],
            'serviceFee'=>$r['bid_serviceFee'],
            'isJiaXi'=>$r['is_jia_xi'],
            'xInterest'=>$r['x_interest']-0,
            'xRate'=>$r['x_rate']/100-0,
            'yInterest'=>$r['y_interest']-0,
            'yRate'=>$r['y_rate']/100-0,
            'periodAdd'=>$r['bid_period_add']-0,
        ];

        if (!empty($r['bid_publish_startdate'])) {
            $tmp['ymdStartReal'] = date('Ymd', strtotime($r['bid_publish_startdate']));

        }
        if($r['product_type'] == 1 || $r['product_type'] == 3) {
            $tmp['dlUnit'] = '月';
        }elseif($r['product_type'] == 2|| $r['product_type'] == 5) {
            $tmp['dlUnit'] = '天';
        }

        $type = $r['bid_status'];
        if ($type == 5001) {
            $type = \Prj\Consts\Wares::status_new;
        } elseif ($type == 5002) { // 募集中
            $type = \Prj\Consts\Wares::status_open;
        } elseif ($type == 5003){ // 满标
            $type = \Prj\Consts\Wares::status_go;
        }elseif( $type == 5004) { // 还款中
            $type = \Prj\Consts\Wares::status_return;
        } elseif ($type == 5005) {
            $type = \Prj\Consts\Wares::status_close;
        } elseif ($type == 5006) {
            $type = \Prj\Consts\Wares::status_ready;
        }
        $tmp['statusCode'] = $type;

        if ($r['bid_interest_type'] == 303) {
            $tmp['returnType'] = \Prj\Consts\ReturnType::byMonth;
        } elseif ($r['bid_interest_type'] == 302) {
            $tmp['returnType'] = \Prj\Consts\ReturnType::single;
        } else {
            $tmp['returnType'] = \Prj\Consts\ReturnType::unknow;
        }

        if($type == \Prj\Consts\Wares::status_go || $type = \Prj\Consts\Wares::status_close) {
            $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
            $lastOrderTime = $db_produce->getOne(\Rpt\Tbname::bid_poi, 'create_time', ['bid_id'=>$r['bid_id'], 'poi_status'=>[601, 603, 610]], 'rsort create_time');
            if(!empty($lastOrderTime)) {
                $tmp['ymdEndReal'] = date('Ymd', strtotime($lastOrderTime));
            }
        }
        return $tmp;
    }


    private function yuebao_trans($r) {
        $tmp = [
            'waresId'       => $r['yuebao_id'],
            'waresName'         => $r['title'],
            'ymdStartReal'      => date('Ymd', strtotime($r['valid_date'])),
            'yieldStatic'      => $r['interest'] /100,
            'dlUnit'           => '天',
            'amount'        => $r['amount'],
            'per_day_deposit_amount'            => $r['per_day_deposit_amount'],
            'per_day_withdraw_amount'           => $r['per_day_withdraw_amount'],
            'per_day_people_deposit_amount'     => $r['per_day_people_deposit_amount'],
            'per_day_people_withdraw_amount'    => $r['per_day_people_withdraw_amount'],
            'shelfId'     =>0,
            'realRaise'    => $r['amount'] - $r['free_amount'],
            'priceStart'  => $r['lowest_amount'],
            'orderIndex'    => $r['order_index'],
        ];
        if ($r['free_amount'] == 0){
            $tmp['statusCode'] = \Prj\Consts\Wares::status_go;
        }else{
            $tmp['statusCode'] == \Prj\Consts\Wares::status_open;
        }
        return $tmp;
    }

}
