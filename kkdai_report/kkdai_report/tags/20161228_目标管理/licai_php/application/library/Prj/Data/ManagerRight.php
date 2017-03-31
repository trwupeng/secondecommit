<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/19
 * Time: 17:08
 */

namespace Prj\Data;

class ManagerRight extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){

        $tmp = self::getCopy('','');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());

    }

    public static function add($fields){

    }

    public static function getRightsByType($loginName,$rightsType){
        $tmp = self::getCopy($loginName,$rightsType);
        $tmp->load();
        if(!$tmp->exists())return [];
        return explode(',',$tmp->getField('rights'));
    }
    public static function getRptRightsByType($loginName,$rightsType){
        $tmp = self::getCopy($loginName,$rightsType);
        $tmp->load();
        if(!$tmp->exists())return [];
        return explode(',',$tmp->getField('rptRights'));
    }

    public static function updateRightsByType($loginName,$rightsType,$rights){
        $tmp = self::getCopy($loginName,$rightsType);
        $tmp->load();
        if(is_array($rights)){
            if(in_array('*',$rights)){
                $tmp->setField('rights','*');
            }else{
                $tmp->setField('rights',implode(',',$rights));
            }
        }else{
            $tmp->setField('rights',$rights);
        }
        try{
            $tmp->update();
        }catch (\ErrorException $e){
            var_log($e->getMessage(),'error>>>>>>>>>>>>>>>>>>>');
            return false;
        }
        return true;

    }

    public static function updateRptRightsByType($loginName,$rightsType,$rights){
        $tmp = self::getCopy($loginName,$rightsType);
        $tmp->load();
        if(is_array($rights)){
            if(in_array('*',$rights)){
                $tmp->setField('rptRights','*');
            }else{
                $tmp->setField('rptRights',implode(',',$rights));
            }
        }else{
            $tmp->setField('rptRights',$rights);
        }
        try{
            $tmp->update();
        }catch (\ErrorException $e){
            var_log($e->getMessage(),'error>>>>>>>>>>>>>>>>>>>');
            return false;
        }
        return true;
    }

    public static function getCopy($id,$rightsType = 'all') {
        return parent::getCopy(['loginName'=>$id,'rightsType'=>$rightsType]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_managers_rights';
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'asset'.($isCache?'Cache':'');
    }

}