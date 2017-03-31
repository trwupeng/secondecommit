<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Group  extends \Sooh\DB\Base\KVObj{


    public static function paged($pager,$where=[],$order=''){
        $fin = self::getCopy('');
        $db = $fin->db();
        $tb = $fin->tbname();

        $pager->init($db->getRecordCount($tb, $where), -1);

        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($explain,$amount,$userName,$type){

    }

    public static function getRights($groupId){
        $group = \Prj\Data\Group::getCopy($groupId);
        $group->load();
        if(!$group->exists())return [];
        if($rightstr = $group->getField('rights')){
            return explode(',',$rightstr);
        }else{
            return [];
        }
    }


	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'manage';
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_group_' . ($n % static::numToSplit());
	}

	public static function getCopy($financeId) {
		$tmp         = parent::getCopy(array('groupId' => $financeId));
		return $tmp;
	}

}
