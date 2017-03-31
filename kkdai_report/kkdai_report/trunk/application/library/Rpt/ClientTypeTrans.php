<?php
namespace Rpt;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/12 0012
 * Time: 上午 11:07
 */
class ClientTypeTrans {

    public static function clientTypeTrans($pay_type = null,$userId = null) {
        if (!empty($pay_type)) {
            $pay_type = strtolower($pay_type);
        }
        if (empty($pay_type)) {
            return \Prj\Consts\ClientType::www;
        }elseif ($pay_type == 'app') {
            $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
            $pay_type = $db->getOne(\Rpt\Tbname::tb_old_user_clienttype, 'pay_type', ['customer_id'=>$userId]);
            return self::clientTypeTrans($pay_type);
        }else {
            return self::$oldClientTYpe[$pay_type];
        }
    }
    public static  $oldClientTYpe = [
        'web' => \Prj\Consts\ClientType::www,
        'h5'    => \Prj\Consts\ClientType::wap,
        'android' => \Prj\Consts\ClientType::android,
        'ios'   => \Prj\Consts\ClientType::appstore,
        'cellphonesite' => \Prj\Consts\ClientType::android
    ];

    public static function clientTypeSearch($channel) {
        $channel = strtolower($channel);
        return self::$oldClientTYpe[$channel];
    }

    public static function getClientTypeFromUser($userId) {
        $clientType = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt)->getOne(\Rpt\Tbname::tb_user_final, 'clientType', ['userId'=>$userId]);
        if (empty($clientType)) {
            $clientType = \Prj\Consts\ClientType::www;
        }
        return $clientType;
    }
}