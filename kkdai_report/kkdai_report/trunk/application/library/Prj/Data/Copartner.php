<?php
namespace Prj\Data;
/**
 * 渠道描述
 */
class Copartner extends \Sooh\DB\Base\KVObj{
    public $cameFrom;
    
    /**
     * 
     * @param string $pkey
     * @return \Prj\Data\Copartner
     */
    public static function getCopy($pkey=null){
        return parent::getCopy($pkey);
    }
    
    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName ($n, $isCache)
    {
        return 'tb_copartner_'.($n % static::numToSplit());
    }
    
    //针对缓存，非缓存情况下具体的表的名字
    protected static function idFor_dbByObj_InConf ($isCache)
    {
        return 'copartner';
    }
    
    public function getAccountNum($where)
    {
        return static::loopGetRecordsCount($where);
    }
    
    public function getAllRecords() {
        return $this->db()->getRecords($this->tbname(), array('copartnerId', 'copartnerAbs', 'copartnerName'));
    }
    
}