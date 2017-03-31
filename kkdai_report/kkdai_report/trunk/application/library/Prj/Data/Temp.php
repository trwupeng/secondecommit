<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */
namespace Prj\Data;

class Temp extends \Prj\Data\BaseFK {

    protected static $_pk = 'id'; //主键

    protected static $_tbname = 'tb_temp'; //表名

    public static function set($key , $value , $exp = ''){
        $tmp = self::getCopy($key);
        $tmp->load();
        $tmp->setField('value',$value);
        $tmp->setField('lastUpdate',date('YmdHis'));
        if($exp)$tmp->setField('exp',$exp);
        $tmp->update();
    }

    public static function get($key){
        $tmp = self::getCopy($key);
        $tmp->load();
        return $tmp->exists() ? $tmp->getField('value') : null;
    }
}