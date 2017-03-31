<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Finance  extends \Sooh\DB\Base\KVObj{


    public static function paged($pager,$where=[],$order=''){
        $fin = self::getCopy('');
        $db = $fin->db();
        $tb = $fin->tbname();

        $pager->init($db->getRecordCount($tb, $where), -1);

        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($explain,$amount,$userName,$type,$remain,$date){
        if(empty($explain)||empty($amount)||empty($userName)||empty($type))return null;
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $fin = self::getCopy($id);
            $fin->load();
        }while($fin->exists());
        $fin->setField('exp',$explain);
        $fin->setField('type',$type);
        $amount>=0?$fin->setField('income',$amount):$fin->setField('payment',$amount);
        $fin->setField('remain',$remain);
        $fin->setField('createUser',$userName);
        $fin->setField('updateUser',$userName);
        $fin->setField('date',$date);
        $fin->setField('createTime',$time->ymdhis());
        $fin->setField('updateTime',$time->ymdhis());
        return $fin;
    }

    /**
     * @param $type
     * @param $date
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function refreshRemain($type,$date){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $oldRs = $db->getRecord($tb,'*',['type'=>$type,'date<'=>$date],'rsort date rsort createTime rsort financeId');

        $remain = $oldRs['remain'];
        $newRs = $db->getRecords($tb,'*',['type'=>$type,'date]'=>$date],'sort date sort createTime sort financeId');
        if(empty($newRs))return;
        foreach($newRs as $v){
            $tmp = self::getCopy($v['financeId']);
            $tmp->load();
            if($v['income']){
                $remain+=$v['income'];
            }else{
                $remain+=$v['payment'];
            }
            //$remain+=($v['income']+$v['payment']);
            $tmp->setField('remain',$remain);
            $tmp->update();
        }
    }

    public static function getRemainBefore($ymd,$type){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecord($tb,'*',['type'=>$type,'date<'=>$ymd],'rsort date rsort createTime rsort financeId');
        if(empty($rs))return 0;
        return $rs['remain'];
    }

    public static function getRemainAfter($ymd,$type){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecord($tb,'*',['type'=>$type,'date<='=>$ymd],'rsort date rsort createTime rsort financeId');
        if(empty($rs))return 0;
        return $rs['remain'];
    }




	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'finance' . ($isCache ? 'Cache' : '');
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_finance_' . ($n % static::numToSplit());
	}

	public static function getCopy($financeId) {
		$tmp         = parent::getCopy(array('financeId' => $financeId));
		return $tmp;
	}
}
