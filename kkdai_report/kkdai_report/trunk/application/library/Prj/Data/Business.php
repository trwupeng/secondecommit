<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/14
 * Time: 9:58
 */

namespace Prj\Data;

class Business extends \Sooh\DB\Base\KVObj{

    public static function paged($pager,$where=[],$order=''){
        $fin = self::getCopy('');
        $db = $fin->db();
        $tb = $fin->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb,'*',$where,$order,$pager->page_size, $pager->rsFrom());
    }

    public static function add($fields,$nickname){
        $time = \Sooh\Base\Time::getInstance();
        do{
            $id = $time->ymdhis().rand(1000,9999);
            $tmp = self::getCopy($id);
            $tmp->load();
        }while($tmp->exists());
        foreach($fields as $k=>$v){
            $tmp->setField($k,$v);
        }
        $count = self::loopGetRecordsCount([/*'createUser'=>$nickname,*/'date'=>$fields['date'],'week'=>$fields['week']]);
        if($count>0)throw new \ErrorException('请勿重复添加！');
        $tmp->setField('createUser',$nickname);
        $tmp->setField('updateUser',$nickname);
        $tmp->setField('createTime',$time->ymdhis());
        $tmp->setField('updateTime',$time->ymdhis());
        return $tmp;
    }

    /**
     * @param $type
     * @param $date
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function refreshRemain($date,$week){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $oldRs = $db->getRecord($tb,'*',['date+week/10<'=>$date+$week/10],'rsort date rsort week');

        $remainAmount = $oldRs['remainAmount'];
        $remainNum = $oldRs['remainNum'];
        $newRs = $db->getRecords($tb,'*',['date+week/10]'=>$date+$week/10],'sort date sort week');
        if(empty($newRs))return;
        foreach($newRs as $v){
            $tmp = self::getCopy($v['businessId']);
            $tmp->load();
            $ammountTotal = 0;
            $amountArr = ['surveyAmount','loanAmount','businessAmount','settleAmount'];
            $ammountTotal += ($v['loanAmount'] + $v['businessAmount'] - $v['settleAmount']);
            $remainAmount+=$ammountTotal;
            $numTotal = 0;
            $numArr = ['surveyNum','loanNum','businessNum','settleNum'];
            $numTotal += ($v['loanNum'] + $v['businessNum'] - $v['settleNum']);
            $remainNum+=$numTotal;
            $tmp->setField('remainNum',$remainNum);
            $tmp->setField('remainAmount',$remainAmount);
            $tmp->update();
        }
    }

    public static function getRemainAfter($ym){
        $tmp = self::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecord($tb,'*',['date'=>$ym],'rsort week');
        if(empty($rs))return ['remainNum'=>0,'remainAmount'=>0];
        return ['remainNum'=>$rs['remainNum'],'remainAmount'=>$rs['remainAmount']];
    }

    public static function getCopy($ordersId) {
        return parent::getCopy(['businessId'=>$ordersId]);
    }


    //针对缓存，非缓存情况下具体的表的名字
    protected static function splitedTbName($n,$isCache)
    {
        return 'tb_business_'.($n % static::numToSplit());
    }
    //指定使用什么id串定位数据库配置
    protected static function idFor_dbByObj_InConf($isCache) {
        return 'business'.($isCache?'Cache':'');
    }
}
