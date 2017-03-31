<?php
namespace Prj\Data;
/**
 * 渠道描述
 */
class Contract extends \Sooh\DB\Base\KVObj{
    public $cameFrom;
    /**
     * 
     * @param string $pkey
     * @return \Prj\Data\Contract
     */
    public static function getCopy($pkey=null){
        return parent::getCopy($pkey);
    }
    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName ($n, $isCache)
    {
        return 'tb_contract_'.($n % static::numToSplit());
    }
    
    //针对缓存，非缓存情况下具体的表的名字
    protected static function idFor_dbByObj_InConf ($isCache)
    {
        return 'contract';
    }
    
    public function getAccountNum($where)
    {
        return static::loopGetRecordsCount($where);
    }
    
    public static function getDefaultFor($copartnerAbs)
	{
		$tmp = self::getCopy(['contractId'=>0]);
		$ret = $tmp->db()->getOne($tmp->tbname(), 
				'contractId',['copartnerAbs'=>$copartnerAbs]);
		if($ret===null){
			return '0';
		}else{
			return $ret;
		}
	}

}