<?php

namespace Prj\Data;

class FKTest extends \Sooh\DB\Base\KVObj
{
    public static function getCopy($k)
    {
        return parent::getCopy(['id' => $k]);
    }


    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_test';
    }
}
