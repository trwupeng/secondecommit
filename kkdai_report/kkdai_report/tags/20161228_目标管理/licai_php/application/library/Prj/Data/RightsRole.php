<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class RightsRole  extends \Sooh\DB\Base\KVObj{


    public static function paged($pager = null,$where=[],$order=''){
        $fin = self::getCopy('');
        $db = $fin->db();
        $tb = $fin->tbname();
        if($pager){
            $pager->init($db->getRecordCount($tb, $where), -1);
            return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
        }else{
            return $db->getRecords($tb,'*',$where,$order);
        }
    }

    public static function add(){

    }

    public static function getRightsNames($arr){
        $rs = self::paged(null,['roleId' => $arr]);
        $rights = [];
        $rightsName = [];
        foreach ($rs as $v){
            $tmp = $v['rightsIds'] ? explode(',',$v['rightsIds']) : [];
            $rights  = array_merge($rights , $tmp);
        }
        $rights = array_unique($rights);
        //var_log($rights);
        foreach ($rights as $v){
            $name = \Prj\Data\Menu::getName($v);
            if($name)$rightsName[$v] = \Prj\Data\Menu::getName($v);
        }
        return $rightsName;
    }

	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
        unset($isCache);
		return 'manage';
	}


	protected static function splitedTbName($n, $isCache) {
        unset($n);
        unset($isCache);
		return 'tb_rights_role';
	}

	public static function getCopy($financeId) {
		$tmp         = parent::getCopy(array('roleId' => $financeId));
		return $tmp;
	}
}
