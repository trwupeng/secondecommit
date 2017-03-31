<?php

namespace Prj\Data;

use Sooh\DB\Base\KVObj;

class OaTicket extends KVObj
{
    public static function save( $ticket, $oaLoginName ) {
        $model = self::getCopy($ticket);
        $model->load();

        $model->setField('ticket', $ticket );
        $model->setField('oaLoginName', $oaLoginName );
        $model->update();
        return $model;
    }

    public static function getCopy($ticket)
    {
        return parent::getCopy(['ticket' => $ticket]);
    }

    protected static function splitedTbname($n, $isCache)
    {
        return 'tb_oaTicket';
    }
}
