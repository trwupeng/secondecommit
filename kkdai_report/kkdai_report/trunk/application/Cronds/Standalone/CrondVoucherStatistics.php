<?php
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondVoucherStatistics&ymdh=20160826"
 * Created by PhpStorm.
 * User: tao.man
 * Date: 2016/9/26
 * Time: 下午 03:53
 */

namespace PrjCronds;

class CrondVoucherStatistics extends \Sooh\Base\Crond\Task {
    //报表数据源
    private $dbMysql;
    //生产数据源
    private $dbProduce;

    public function init() {
        parent::init();
        //ii 分钟 ss 秒
        //即: 每小时的 16分55秒 执行
        $this->_iissStartAfter = 1655;
        //是否只执行一次
        $this->toBeContinue = true;

        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->ret->newadd = 0;
        $this->ret->newupd = 0;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $this->dbProduce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
    }

    protected function onRun($dt) {
        if (!$this->_isManual && $dt->hour<=6) {  // TODO 上线修改称<=6
            $dt0 = strtotime($dt->YmdFull);
            switch($dt->hour) {
                case 1: $this->oneday(date('Ymd', $dt0-86400*10));break; // case 1
                case 2: $this->oneday(date('Ymd', $dt0-86400*7));break;  // case 2
                case 3: $this->oneday(date('Ymd', $dt0-86400*1));break;  // case 3
                case 4: $this->oneday(date('Ymd', $dt0-86400*4));break;  // case 4
                case 5: $this->oneday(date('Ymd', $dt0-86400*3));break;  // case 5
                case 6: $this->oneday(date('Ymd', $dt0-86400*2));break;  // case 6
            }
        } elseif ($this->_isManual) {
            $this->oneday($dt->YmdFull);
        }
        $this->toBeContinue = false;
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }
    protected function oneday($ymd) {
        $record = [];

        //当天抵现券, 提现券, 抵现券的发放金额
        $rs = $this->dbMysql->getPair('db_kkrpt.tb_vouchers_final','voucherType', 'sum(amount) as sumAmount',
            ['ymdCreate'=>$ymd, 'voucherType'=>[1,3,4]], 'groupby voucherType');

        foreach($rs as $voucherType => $sumAmount) {
            switch($voucherType){
                case 1: $record['dixian_grant_amount'] = $sumAmount;break;
                case 3: $record['fanxian_grant_amount'] = $sumAmount;break;
                case 4: $record['tixian_grant_amount'] = $sumAmount;break;
            }
        }
        // 当天加息券发放数量
        $rs = $this->dbMysql->getOne('db_kkrpt.tb_vouchers_final', 'count(*)',
            ['ymdCreate'=>$ymd, 'voucherType'=>2]);
        $record['jiaxi_grant_num']=$rs;

        // 当天抵现券, 提现券, 抵现券的使用金额
        $rs = $this->dbMysql->getPair('db_kkrpt.tb_vouchers_final','voucherType', 'sum(amount) as sumAmount',
            ['ymdUsed'=>$ymd, 'status'=>1, 'voucherType'=>[1,3,4]], 'groupby voucherType');

        foreach($rs as $voucherType => $sumAmount) {
            switch($voucherType){
                case 1: $record['dixian_use_amount'] = $sumAmount;break;
                case 3: $record['fanxian_use_amount'] = $sumAmount;break;
                case 4: $record['tixian_use_amount'] = $sumAmount;break;
            }
        }

        // 当天加息券使用数量
        $rs = $this->dbMysql->getOne('db_kkrpt.tb_vouchers_final', 'count(*)',
            ['ymdUsed'=>$ymd,'status'=>1, 'voucherType'=>2]);
        $record['jiaxi_use_num']=$rs;

        if(!array_sum($record)){
            return;
        }
        $this->dbMysql->delRecords('db_kkrpt.tb_voucher_statistics', ['ymd'=>$ymd]);
        $this->dbMysql->ensureRecord('db_kkrpt.tb_voucher_statistics', array_merge(['ymd'=>$ymd], $record), array_keys($record));
        $this->ret->newupd++;
    }

//    protected function execSql($sql, $db) {
//        $result = $db->execCustom(['sql'=>$sql]);
//        $rs = $db->fetchAssocThenFree($result);
//        return $rs;
//    }
}