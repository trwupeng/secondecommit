<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/15
 * Time: 15:02
 */

namespace Prj\Data;

class ManageLog extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function addLogs($fields,$nickname){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        $tmp->setField('createUser',$nickname);
        //$tmp->setField('updateUser',$nickname);
        $tmp->setField('createTime',$time->ymdhis());
        //$tmp->setField('updateTime',$time->ymdhis());
        try{
            $tmp->update();
        }catch (\ErrorException $e){
            var_log($e->getMessage(),'记录操作日志>>>>>>>>>>>>>>');
        }

    }

    public static function getCopy($id) {
        return parent::getCopy(['logsId'=>$id]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_logs_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'manage';
    }

}