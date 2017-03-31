<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */

namespace Prj\Data;

class BaseFK  extends \Sooh\DB\Base\KVObj{

    protected static $_pk; //主键

    protected static $_tbname; //表名

    protected static $_host = 'manage'; //配置名

    public static function paged($pager,$where=[],$order=''){
        $tmp = static::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function getRecords($where=[],$order='',$start = 0 , $limit = null){
        $tmp = static::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        if(!$limit){
            $limit = $start;
            $start = 0;
        }
        return $db->getRecords($tb,'*',$where,$order,$limit, $start);
    }

    /**
     * 获取一个实例
     * @param $id
     * @return null|\Sooh\DB\Base\KVObj
     */
    public static function getOne($id){
        $tmp = self::getCopy($id);
        $tmp->load();
        return $tmp->exists() ? $tmp : null;
    }

    public static function query($sql){
        $tmp = static::getCopy('');
        $db = $tmp->db();
        //$db->execCustom(['sql'=>"SET NAMES 'utf8'"]);
        $rs = $db->fetchAssocThenFree($db->execCustom(['sql'=>$sql]));
        return $rs;
    }


    protected static function idFor_dbByObj_InConf($isCache) {
        isset($isCache);
		return static::$_host;
	}

	protected static function splitedTbName($n, $isCache) {
        isset($n);
        isset($isCache);
        if(empty(static::$_tbname))throw new \ErrorException('no tbname');
		return static::$_tbname;
	}

	public static function getCopy($id) {
        if(empty(static::$_pk))throw new \ErrorException('no pk');
		$tmp = parent::getCopy(array(static::$_pk => $id));
		return $tmp;
	}
}
