<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/25
 * Time: 11:25
 */
namespace Prj\Data;
class BusinessTarget extends \Sooh\DB\Base\KVObj
{
    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($fields,$loginName){
        $time = \Sooh\Base\Time::getInstance();
        $tmp = self::getCopy($fields['month'],$fields['loginName']);
        $tmp->load();
        if($tmp->exists())throw new \ErrorException('请勿重复提交！');
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        $tmp->setField('updateUser',$loginName);
        $tmp->setField('updateTime',$time->ymdhis());
        return $tmp;
    }

    public static function getTarget($ym,$loginName){
        $tmp = self::getCopy($ym,$loginName);
        $tmp->load();
        if(!$tmp->exists())return 0;
        return $tmp->getField('target');
    }

    public static function getCopy($month,$loginName = '') {
        return parent::getCopy(['month'=>$month,'loginName'=>$loginName]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_business_target';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'dbForRpt'.($isCache?'Cache':'');
    }
}