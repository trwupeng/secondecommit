<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/25
 * Time: 16:36
 */
namespace Prj\Data;
class BusinessNum extends \Sooh\DB\Base\KVObj
{
    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($fields,$nickname){
        $time = \Sooh\Base\Time::getInstance();
        $month = $fields['month'];
        $tmp = self::getCopy($month);
        $tmp->load();
        if($tmp->exists())throw new \ErrorException('请勿重复提交！');
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        $tmp->setField('updateUser',$nickname);
        $tmp->setField('updateTime',$time->ymdhis());
        return $tmp;
    }

    public static function getNum($ym){
        $tmp = self::getCopy($ym);
        $tmp->load();
        if(!$tmp->exists())return 0;
        return $tmp->getField('num');
    }

    public static function getNums($ym){
        $tmp = self::getCopy($ym);
        $tmp->load();
        if(!$tmp->exists())return ['num'=>0,'numAfter'=>0,'week'=>0];
        return ['num'=>$tmp->getField('num'),'numAfter'=>$tmp->getField('numAfter'),'week'=>$tmp->getField('week')];
    }

    public static function getCopy($month) {
        return parent::getCopy(['month'=>$month]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_business_num';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'dbForRpt'.($isCache?'Cache':'');
    }
}