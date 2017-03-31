<?php
namespace Rpt;
/**
 * 查找邀请人
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/12 0012
 * Time: 下午 2:51
 */

class InviteUser {
    public static function findUserInvitedBy ($userId) {
        $db =  \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $r = $db->getOne(\Rpt\Tbname::customer_invite, 'invite_customer_id', ['customer_id'=>$userId]);
        return $r;
    }


    public static function findUserInviteByParent($userId) {
        $r = self::findUserInvitedBy($userId);

        if(!empty($r)) {
            $r = self::findUserInvitedBy($r);
        }
        return $r;
    }

    public static function findUserInviterByRoot ($userId) {
        $db =  \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $r = $db->getOne(\Rpt\Tbname::customer_invite, 'invite_customer_id', ['customer_id'=>$userId]);
        if (empty($r)) {
            return $userId;
        }else {
           return self::findUserInviterByRoot($r);
        }
    }
}