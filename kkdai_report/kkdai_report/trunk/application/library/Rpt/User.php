<?php
namespace Rpt;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/20 0020
 * Time: 上午 10:41
 */
class User {


    public static function sortBuy ($db, $userId){
        $record = [
            'amountFirstBuy'=> 0,
            'amountExtFirstBuy'=> 0,
            'ymdFirstBuy' => 0,
            'shelfIdFirstBuy' => 0,
            'ymdSecBuy' => 0,
            'amountSecBuy' => 0,
            'amountExtSecBuy' => 0,
            'shelfIdSecBuy' => 0,
            'amountLastBuy' => 0,
            'amountExtLastBuy' => 0,
            'ymdLastBuy' => 0,
            'shelfIdLastBuy' => 0,
        ];
        $where = [
            'userId'=>$userId,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::payed,
                \Prj\Consts\OrderStatus::going,
                \Prj\Consts\OrderStatus::igoing,
                \Prj\Consts\OrderStatus::delay,
                \Prj\Consts\OrderStatus::advanced,
                \Prj\Consts\OrderStatus::done,
            ],
            'poi_type'=>0,
        ];
        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        $lb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['statusCode'=>4011]);
        $tyb = array_merge($tyb, $lb);
        if(!empty($tyb)) {
            $where['waresId!'] = $tyb;
        }
        $order = $db->getRecords(\Rpt\Tbname::tb_orders_final, 'shelfId,amount,amountExt,ymd', $where, 'sort ymd sort hhiiss');
        if (!empty($order)) {
            if(isset($order[0])) {
                $record['amountFirstBuy'] = $order[0]['amount'];
                $record['amountExtFirstBuy'] = $order[0]['amountExt'];
                $record['ymdFirstBuy'] = $order[0]['ymd'];
                $record['shelfIdFirstBuy'] = $order[0]['shelfId'];
            }else {
                $record['amountFirstBuy'] = 0;
                $record['ymdFirstBuy'] = 0;
                $record['shelfIdFirstBuy'] = 0;
                $record['amountExtFirstBuy'] = 0;
            }

            if(isset($order[1])) {
                $record['ymdSecBuy'] = $order[1]['ymd'];
                $record['amountSecBuy'] = $order[1]['amount'];
                $record['amountExtSecBuy'] = $order[1]['amountExt'];
                $record['shelfIdSecBuy'] = $order[1]['shelfId'];
            }else {
                $record['ymdSecBuy'] = 0;
                $record['amountSecBuy'] = 0;
                $record['shelfIdSecBuy'] = 0;
                $record['amountExtSecBuy'] = 0;
            }

            $lastOrder = array_pop($order);
            if(!empty($lastOrder)) {
                $record['amountLastBuy'] = $lastOrder['amount'];
                $record['amountExtLastBuy'] = $lastOrder['amountExt'];
                $record['ymdLastBuy'] = $lastOrder['ymd'];
                $record['shelfIdLastBuy'] = $lastOrder['shelfId'];
            }else {
                $record['amountLastBuy'] = 0;
                $record['ymdLastBuy'] = 0;
                $record['shelfIdLastBuy'] = 0;
                $record['amountExtLastBuy'] = 0;
            }
        }
        return $record;
    }

    public static function firstBuy ($db, $userId){
        $record = [
            'amountFirstBuy'=> 0,
            'ymdFirstBuy' => 0,
            'shelfIdFirstBuy' => 0,
        ];
        $where = [
            'userId'=>$userId,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::payed,
                \Prj\Consts\OrderStatus::going,
                \Prj\Consts\OrderStatus::igoing,
                \Prj\Consts\OrderStatus::delay,
                \Prj\Consts\OrderStatus::advanced,
                \Prj\Consts\OrderStatus::done,
            ],
            'poi_type'=>0,
        ];
        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        if(!empty($tyb)) {
            $where['waresId!'] = $tyb;
        }
        $order = $db->getRecord(\Rpt\Tbname::tb_orders_final, 'shelfId,amount,amountExt,ymd', $where, 'sort ymd sort hhiiss');
        if (!empty($order)) {
            $record['amountFirstBuy'] = $order['amount'];
            $record['amountExtFirstBuy'] = $order['amountExt'];
            $record['ymdFirstBuy'] = $order['ymd'];
            $record['shelfIdFirstBuy'] = $order['shelfId'];
        }
        return $record;
    }
    public static function secBuy ($db, $userId) {
        $record = [
            'ymdSecBuy' => 0,
            'amountSecBuy' => 0,
            'shelfIdSecBuy' => 0,
        ];
        $where = [
            'userId'=>$userId,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::payed,
                \Prj\Consts\OrderStatus::going,
                \Prj\Consts\OrderStatus::igoing,
                \Prj\Consts\OrderStatus::delay,
                \Prj\Consts\OrderStatus::advanced,
                \Prj\Consts\OrderStatus::done,
            ],
            'poi_type'=>0,
        ];
        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        if(!empty($tyb)) {
            $where['waresId!'] = $tyb;
        }
        $orders = $db->getRecords(\Rpt\Tbname::tb_orders_final, 'shelfId,amount,amountExt,ymd', $where, 'sort ymd sort hhiiss', 1, 1);
        $orders = $orders[0];
        if (!empty($orders)) {
            $record['ymdSecBuy'] = $orders['ymd'];
            $record['amountSecBuy'] = $orders['amount'];
            $record['amountExtSecBuy'] = $orders['amountExt'];
            $record['shelfIdSecBuy'] = $orders['shelfId'];
        }
        return $record;
    }

    public static function lastBuy ($db, $userId) {
        $record = [
            'amountLastBuy' => 0,
            'ymdLastBuy' => 0,
            'shelfIdLastBuy' => 0,
        ];
        $where = [
            'userId'=>$userId,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::payed,
                \Prj\Consts\OrderStatus::going,
                \Prj\Consts\OrderStatus::igoing,
                \Prj\Consts\OrderStatus::delay,
                \Prj\Consts\OrderStatus::advanced,
                \Prj\Consts\OrderStatus::done,
            ],
            'poi_type'=>0,
        ];
        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        if(!empty($tyb)) {
            $where['waresId!'] = $tyb;
        }
        $order = $db->getRecord(\Rpt\Tbname::tb_orders_final, 'shelfId,amount,amountExt,ymd', $where, 'rsort ymd rsort hhiiss');
        if (!empty($order)) {
            $record['amountLastBuy'] = $order['amount'];
            $record['amountExtLastBuy'] = $order['amountExt'];
            $record['ymdLastBuy'] = $order['ymd'];
            $record['shelfIdLastBuy'] = $order['shelfId'];
        }
        return $record;
    }

    public static function maxBuy ($db, $userId) {
        $record = [
            'ymdMaxBuy' => 0,
            'amountMaxBuy' => 0,
            'amountExtMaxBuy' => 0,
            'shelfIdMaxBuy'=>0,
        ];
        $where = [
            'userId'=>$userId,
            'orderStatus'=>[
                \Prj\Consts\OrderStatus::payed,
                \Prj\Consts\OrderStatus::going,
                \Prj\Consts\OrderStatus::igoing,
                \Prj\Consts\OrderStatus::delay,
                \Prj\Consts\OrderStatus::advanced,
                \Prj\Consts\OrderStatus::done,
            ],
            'poi_type'=>0,
        ];
        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        if(!empty($tyb)) {
            $where['waresId!'] = $tyb;
        }
        $order = $db->getRecord(\Rpt\Tbname::tb_orders_final, 'max(amount) as amount,amountExt,ymd,shelfId', $where, 'rsort ymd rsort hhiiss');
        if (!empty($order['ymd']) && !empty($order['amount'])) {
            $record['ymdMaxBuy'] = $order['ymd'];
            $record['amountMaxBuy'] = $order['amount'];
            $record['amountExtMaxBuy'] = $order['amountExt'];
            $record['shelfIdMaxBuy'] = $order['shelfId'];
        }
        return $record;
    }


    public static function firstOrder ($db, $userId, $shelfId, $orderId) {
        $where = [
            'userId' => $userId,
            'poi_type' => 0,
            'orderStatus'=>[8,10,39],
        ];
        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        if(!empty($tyb)) {
            $where['waresId!'] = $tyb;
        }

        $orderFirstInAll = $db->getOne(\Rpt\Tbname::tb_orders_final, 'ordersId', $where);
        if($orderId == $orderFirstInAll) {
            $firstTimeInAll = 1;
            $ret  = [
                'firstTimeInAll' => $firstTimeInAll,
                'firstTime' => $firstTimeInAll,
            ];

            return $ret;
        }else{
            $firstTimeInAll = 0;
        }

        if($shelfId > 0) {
            $first_dq = $db->getOne(\Rpt\Tbname::tb_orders_final, 'ordersId', array_merge($where, ['shelfId>'=>0]));
            if($orderId == $first_dq) {
                $firstTime = 1;
            }else {
                $firstTime = 0;
            }

        }

        if($shelfId == 0) {
            $first_ttz = $db->getOne(\Rpt\Tbname::tb_orders_final, 'ordersId', array_merge($where, ['shelfId'=>0]));
            if($first_ttz == $orderId){
                $firstTime =1;
            }else {
                $firstTime =0;
            }
        }

        $ret = [
            'firstTimeInAll' => $firstTimeInAll,
            'firstTime' => $firstTime,
        ];

        return $ret;
    }

    public static function isFirstOrderInAll ($db, $userId, $create_time) {

        $where = ['customer_id'=>$userId, 'poi_status'=>[601,603,610],'poi_type'=>0];
        $tyb = $db->getCol(\Rpt\Tbname::bid, 'bid_id', ['bid_type'=>501]);
        if(!empty($tyb)) {
            $where['bid_id!']=$tyb;
        }

        $order_time = $db->getOne(\Rpt\Tbname::bid_poi, 'create_time', $where, 'sort create_time');
//error_log('firstTimeInALl >>>>> lastCmd###'.\Sooh\DB\Broker::lastCmd());
//var_log($order_time, 'firstTimeInAll >>>> 非天天第一成功订单时间：');
        $yuebao_time = $db->getOne(\Rpt\Tbname::yuebao_poi, 'create_date', ['customer_id'=>$userId, 'type'=>1, 'status'=>1], 'sort create_date');
//var_log($yuebao_time, '天天第一成功订单时间：');

        $first_time = min($order_time, $yuebao_time);
        if (empty($first_time)) {
            $first_time = max($order_time, $yuebao_time);
        }

//        error_log($userId.':'.$create_time. '---------'.$first_time);
        if ($create_time == $first_time) {

            return 1;
        }else {
            return 0;
        }
    }

    public static function isFirstOrder ($db, $userId, $shelfId, $ordersId) {
        if ($shelfId == 0) {
            $first_order = $db->getOne(\Rpt\Tbname::yuebao_poi, 'poi_id', ['customer_id'=>$userId, 'type'=>1, 'status'=>1], 'sort create_date');
            if ($first_order == $ordersId) {
                return 1;
            }else {
                return 0;
            }
        }else {
            $where = ['customer_id'=>$userId, 'poi_status'=>[601,603,610],'poi_type'=>0];
            $tyb = $db->getCol(\Rpt\Tbname::bid, 'bid_id', ['bid_type'=>501]);
            if(!empty($tyb)) {
                $where['bid_id!'] = $tyb;
            }
            $first_order = $db->getOne(\Rpt\Tbname::bid_poi, 'poi_id', $where, 'sort create_time');
//error_log('firstTime>>>>>>>>lastCmd###'.\Sooh\DB\Broker::lastCmd());
            if ($ordersId == $first_order) {
                return 1;
            }else {
                return 0;
            }
        }
    }

    public static function getCopartnerIdAndContractId($userId){
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $copartnerId = $db_rpt->getOne(\Rpt\Tbname::tb_user_final, 'copartnerId', ['userId'=>$userId]);
        $user_upd_field = [];
        if (empty($copartnerId)) {
            $customer_info= $db_produce->getRecord(\Rpt\Tbname::customer, 'source,download_source', ['customer_id'=>$userId]);
            if (!empty($customer_info['source'])) {
                $copart = $customer_info['source'];
            }elseif (!empty($customer_info['download_source'])){
                $copart = $customer_info['download_source'];
            }else{
                $copart = '999920150101000000'; // 之前老数据没有source 和download_source的
            }
            $user_upd_field['contractId'] = \Rpt\CopartnerTrans::transContractId($copart);
//var_log($user_upd_field, 'user_upd>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
            if (empty($user_upd_field['contractId'])) {
                throw new \ErrorException('user:'.$userId.' is not found contractId，maybe source source OR download_source is new');
                return;
            }
            $user_upd_field['copartnerId'] = \Rpt\CopartnerTrans::transCopartnerId($copart);

        }

        return $user_upd_field;
    }
}