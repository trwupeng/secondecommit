<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Rights  extends \Sooh\DB\Base\KVObj{

    protected static $_pk = 'rightsId';

    public static function paged($pager,$where=[],$order=''){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add(){

    }

	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
    }

    public static function getRightIds(){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        return $db->getRecords($tb,'rightsId,rightsName',['status'=>0],'sort rightsName');
    }

	protected static function idFor_dbByObj_InConf($isCache) {
        unset($isCache);
		return 'manage';
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_rights_' . ($n % static::numToSplit());
	}

	public static function getCopy($financeId) {
		$tmp         = parent::getCopy(array(self::$_pk => $financeId));
		return $tmp;
	}
}
