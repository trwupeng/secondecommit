<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FinanceGround  extends \Sooh\DB\Base\KVObj{

    public static $mapArr = [
        'uAmountRemain'=>'userAmount',
        'bAmountRemain'=>'borrowerAmount',
        'bServiceRemain'=>'borrowerService',
        'bInterestRemain'=>'borrowerInterest',
        'bMarginRemain'=>'borrowerMargin',
        'bAgencyRemain'=>'borrowerAgency',
        'bOTRemain'=>'incomeOT',

        'pLoanRemain'=>'payLoan',
        'pInterestRemain'=>'payInterest',
        'pAmountRemain'=>'payAmount',
        'pMarginRemain'=>'payMargin',
        'pAgencyRemain'=>'payAgency',
        'pOTRemain'=>'payOT',
    ];

    public static function paged($pager,$where=[],$order=''){
        $fin = self::getCopy('');
        $db = $fin->db();
        $tb = $fin->tbname();

        $pager->init($db->getRecordCount($tb, $where), -1);

        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($fields){
        if(empty($fields))return null;
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $fing = self::getCopy($id);
            $fing->load();
            var_log($fing,'fing>>>>>>>>>');
        }while($fing->exists());
        foreach($fields as $k=>$v){
            $fing->setField($k,$v);
        }
        $fing->setField('createTime',$time->ymdhis());
        $fing->setField('updateTime',$time->ymdhis());
        return $fing;
    }

    /**
     * @param $type
     * @param $date
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function refreshRemain($date){
        $mapArr = self::$mapArr;
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $oldRs = $db->getRecord($tb,'*',['date<'=>$date],'rsort date rsort createTime rsort groundId');

        foreach($mapArr as $k=>$v){
            $remain[$k] = $oldRs[$k];
        }

        $newRs = $db->getRecords($tb,'*',['date]'=>$date],'sort date sort createTime sort groundId');
        if(empty($newRs))return;
        foreach($newRs as $v){
            $tmp = self::getCopy($v['groundId']);
            $tmp->load();
            /*
            $arr = ['userAmount','borrowerAmount','borrowerService','borrowerMargin','borrowerInterest','borrowerAgency','incomeOT',
                'payLoan','payInterest','payAmount','payMargin','payAgency','payOT'];
            */
            foreach($mapArr as $kk=>$vv){
                $remain[$kk] += $v[$vv];
            }

            foreach($remain as $k=>$v){
                $tmp->setField($k,$v);
            }

            $tmp->update();
        }
    }

    /**
     * @param $where
     * @return mixed
     */
    public static function getRemainsAfter($date = ''){
        if(empty($date))$date = date('Ymd');
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecord($tb,'*',['date['=>$date],'rsort date rsort createTime rsort groundId');
        $remain = [];
        foreach(self::$mapArr as $k=>$v){
            $remain[$k] = $rs[$k];
        }
        return $remain;
    }

    public static function getRemainsBefore($date = ''){
        if(empty($date))$date = date('Ymd');
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecord($tb,'*',['date<'=>$date],'rsort date rsort createTime rsort groundId');
        $remain = [];
        foreach(self::$mapArr as $k=>$v){
            $remain[$k] = $rs[$k];
        }
        return $remain;
    }


	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'finance_ground' . ($isCache ? 'Cache' : '');
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_finance_ground_' . ($n % static::numToSplit());
	}

	public static function getCopy($financeId) {
		$tmp         = parent::getCopy(array('groundId' => $financeId));
		return $tmp;
	}
}
