<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondRecharges&ymdh=20160120"
 *
 * 充值和体现，正值 充值，负值 体现
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/22 0022
 * Time: 上午 9:36
 */

class CrondRecharges extends \Rpt\Misc\DataCrondGather {
    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter = 1200;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    public function free() {
        parent::free();
        $this->dbMysql = null;
    }

    protected function gather () {
//$t1 = microtime(true);
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        /**
         *
         * 抓取充值记录
         */
error_log('时间：'.$this->dt->hour);
        if($this->dt->hour >=5) {
            $this->YmdHisTo = date('Y-m-d H:i:s', $this->dt->timestamp() - 180);
        }
        $this->printLogOfTimeRang();

        $where = ['update_time]'=>$this->YmdHisFrom, 'update_time['=>$this->YmdHisTo, 'status'=>1, 'trade_type'=>3004]; // 1 代表成功
        $arr_id = $db_produce->getCol(\Rpt\Tbname::recharge_enchashment_water, 'id', $where);
        $total = $arr_id;
        $upd=[];
        if(!empty($arr_id)){
            $arr_id = array_chunk($arr_id, 1000);
            foreach($arr_id as $group) {
                $orders = $db_produce->getRecords(\Rpt\Tbname::recharge_enchashment_water,
                    \Rpt\Fields::$produce_fields_recharge_enchashment_water, ['id'=>$group]);
                foreach($orders as $r) {
//$tmp1 = microtime(true);
                    $tmp = $this->trans($r);
                    try{
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_recharges_final, $tmp);
                        $this->ret->newadd++;
                    }catch(\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)){
                            unset($tmp['id']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_recharges_final, $tmp, ['ordersId'=>$r['id']]);
                            $upd[] = $r['id'];
                            $this->ret->newupd++;
                        }else {
                            error_log($e->getMessage()."\n".$e->getTraceAsString());
                        }
                    }

                    // 更新用户表 充值
                    $record = $this->rechargeRecordWhichTime(1, $r['customer_id'], $this->dbMysql);
                    $user_upd_field['amountFirstRecharge'] = $record['amount'];
                    $user_upd_field['ymdFirstRecharge'] = $record['ymd'];


                    $record = $this->rechargeRecordWhichTime(2, $r['customer_id'], $this->dbMysql);
                    if (!empty($record)) {
                        $user_upd_field['amountSecRecharge'] = $record['amount'];
                        $user_upd_field['ymdSecRecharge'] = $record['ymd'];
                    }


                    $record = $this->rechargeRecordWhichTime('last', $r['customer_id'], $this->dbMysql);
                    $user_upd_field['amountLastRecharge'] = $record['amount'];
                    $user_upd_field['ymdLastRecharge'] = $record['ymd'];

                    $record = $this->rechargeRecordWhichTime('max', $r['customer_id'], $this->dbMysql);
                    $user_upd_field['amountMaxRecharge'] = $record['amount'];
                    $user_upd_field['ymdMaxRecharge'] = $record['ymd'];
                    $user_upd_field['userId'] = $r['customer_id'];

                    // 更新用户的渠道号
                    try {
                        $tmp_copart = \Rpt\User::getCopartnerIdAndContractId($r['customer_id']);
                        $user_upd_field = array_merge($user_upd_field, $tmp_copart);
                    }catch(\ErrorException $e) {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }


                    $updFields = array_keys($user_upd_field);
                    unset($updFields[array_search('userId', $updFields)]);
                    $this->dbMysql->ensureRecord(\Rpt\Tbname::tb_user_final, $user_upd_field, $updFields);
//$tmp2 = microtime(true);
//$tmp2 = $tmp2-$tmp1;
//error_log('for循环间隔时间：'.$tmp2);
                }

            }
        }
//$t2 = microtime(true);
//$tmp = $t2 - $t1;
//error_log('抓取充值记录时间长：'.$tmp);
        /**
         *
         * 抓取提现
         */

        $where = ['update_time]'=>$this->YmdHisFrom, 'update_time['=>$this->YmdHisTo, 'trade_type'=>3005];
        $arr_id = $db_produce->getCol(\Rpt\Tbname::recharge_enchashment_water, 'id', $where);

        if (!empty($arr_id)) {
            $arr_id = array_chunk($arr_id, 1000);
            foreach($arr_id as $group) {
                $orders = $db_produce->getRecords(\Rpt\Tbname::recharge_enchashment_water,
                    \Rpt\Fields::$produce_fields_recharge_enchashment_water, ['id'=>$group]);
                foreach($orders as $r) {
                    if (!in_array($r['id'], $total)) {
                        $total[] = $r['id'];
                    }
                    $tmp = $this->trans($r);
                    try{
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->dbMysql->addRecord(\Rpt\Tbname::tb_recharges_final, $tmp);
                        $this->ret->newadd++;
                    }catch(\ErrorException $e) {
                        if (\Sooh\DB\Broker::errorIs($e)){
                            unset($tmp['id']);
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_recharges_final, $tmp, ['ordersId'=>$r['id']]);
                            if (!in_array($r['id'], $upd)){
                                $upd[]= $r['id'];
                                $this->ret->newupd++;
                            }

                        }else {
                            error_log($e->getMessage()."\n".$e->getTraceAsString());
                        }
                    }
                }
            }
        }

//$tmp = microtime(true) - $t2;
//error_log('抓取提现时间长：'.$tmp);

        $where = ['finish_time]'=>$this->YmdHisFrom, 'finish_time['=>$this->YmdHisTo, 'trade_type'=>3005];
        $arr_id = $db_produce->getCol(\Rpt\Tbname::recharge_enchashment_water, 'id', $where);
//var_log(\Sooh\DB\Broker::lastCmd(), 'lastCmd>>>>>>>>>');
        if (empty($arr_id)) {
            $this->ret->total = sizeof($total);
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
            return true;
        }
        $arr_id = array_chunk($arr_id, 1000);
        foreach($arr_id as $group) {
            $orders = $db_produce->getRecords(\Rpt\Tbname::recharge_enchashment_water,
                \Rpt\Fields::$produce_fields_recharge_enchashment_water, ['id'=>$group]);
            foreach($orders as $r) {
                if (!in_array($r['id'], $total)) {
                    $total[] = $r['id'];
                }
                $tmp = $this->trans($r);
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $this->dbMysql->addRecord(\Rpt\Tbname::tb_recharges_final, $tmp);
                    $this->ret->newadd++;
                }catch(\ErrorException $e) {
                    if (\Sooh\DB\Broker::errorIs($e)){
                        unset($tmp['id']);
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_recharges_final, $tmp, ['ordersId'=>$r['id']]);
                        if (!in_array($r['id'], $upd)){
                            $upd[]= $r['id'];
                            $this->ret->newupd++;
                        }

                    }else {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }
                }
            }
        }


        $this->ret->total = sizeof($total);
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }

    protected function trans ($r){
        $tmp = [
            'ordersId'      => $r['id'],
            'userId'        => $r['customer_id'],
            'poundage'      => $r['user_fee'],
            'ymd'           => date('Ymd', strtotime($r['update_time'])),
            'hhiiss'        => date('His', strtotime($r['update_time'])),
            'summary'       => $r['summary'],
            'bankCard'      =>$r['card_id'],
            'balance'       =>$r['balance'],
            'couponId'      =>$r['coupon_id'],
            'payMethod'     =>$r['pay_method'],
            'flag'          =>$r['flag'],
            'orderStatus'  =>$r['status'],
        ];

        if (!empty($r['finish_time'])) {
            $tmp['finishYmd'] = date('Ymd', strtotime($r['finish_time']));
        }elseif(empty($r['finish_time']) && $r['status'] >0 ) {
            $tmp['finishYmd'] = $tmp['ymd'];
        }

        if (!empty($r['channel'])){
            $type = \Rpt\ClientTypeTrans::clientTypeSearch($r['channel']);
        }else {
            $type = $this->dbMysql->getOne(\Rpt\Tbname::tb_user_final, 'clientType', ['userId'=>$r['customer_id']]);
        }
        if (empty($type)) {
            $type = \Prj\Consts\ClientType::www;
        }
        $tmp['clientType'] = $type;

        if (3004 == $r['trade_type']) {
            $tmp['amount'] = $r['amount'];
            $tmp['orderStatus'] = \Prj\Consts\OrderStatus::done;
        }else if (3005 == $r['trade_type']) {
            $tmp['amount'] = 0- $r['amount'];
            if ($r['status'] == 1) {
                $tmp['orderStatus'] = \Prj\Consts\OrderStatus::done;
            }elseif ($r['status'] == 2) {
                $tmp['orderStatus'] = \Prj\Consts\OrderStatus::failed;
            }
        }
        return $tmp;
    }

    private function rechargeRecordWhichTime ($times, $userId, $db) {

        $where = ['userId'=>$userId, 'amount>'=>0];
        if (is_numeric($times)) {
            $orderBy = 'sort ymd sort hhiiss';
            $limitFrom = $times-1;
            $pageSize = 1;
        }elseif ($times == 'last'){
            $orderBy = 'rsort ymd rsort hhiiss';
            $limitFrom = 0;
            $pageSize = 1;
        }elseif ($times == 'max') {
            $orderBy = 'rsort amount rsort ymd';
            $limitFrom = 0;
            $pageSize = 1;
        }


        $orders = $db->getRecords(\Rpt\Tbname::tb_recharges_final, 'ymd,amount', $where,
                $orderBy, $pageSize, $limitFrom);

        if (empty($orders[0])) {
            return null;
        }else {
            $tmp['ymd'] = $orders[0]['ymd'];
            $tmp['amount'] = $orders[0]['amount'];
            return $tmp;
        }
    }
}
