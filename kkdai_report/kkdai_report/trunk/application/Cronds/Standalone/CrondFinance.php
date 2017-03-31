<?php
namespace PrjCronds;
/**
 * php /var/www/miao_php/public/crond.php "__crond/run&task=Standalone.CrondFinance&ymdh=20160115"
 */
class CrondFinance extends \Sooh\Base\Crond\Task
{
    protected $addNum = 0;
    protected $updateNum = 0;
    protected $db = null;

    public function init()
    {
        parent::init();
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 300;//每5分钟启动一次
        $this->_iissStartAfter = 200;//每小时02分后启动
        $this->db = \Sooh\DB\Broker::getInstance('dbForRpt');
        $this->ret = new \Sooh\Base\Crond\Ret();

    }

    public function free()
    {
        parent::free();
    }

    protected function onRunnnn($dt)
    {
        $date = $dt->YmdFull;
        $now = date('YmdHis');
        if ($this->_isManual) {
            $m = 'manual';
        } else {
            $m = 'auto';
        }
        $tbFinance = 'db_kkrpt.tb_finance_0';
        $tbFinanceg = 'db_kkrpt.tb_finance_ground_0';
        $tbFinanceDay = 'db_kkrpt.tb_finance_day';
        $typeArr = \Prj\Consts\Finance::$type_enum;
        $financegAmountArr = ['userAmount', 'borrowerAmount', 'borrowerService', 'borrowerMargin', 'borrowerInterest', 'borrowerAgency', 'incomeOT',
            'payLoan', 'payInterest', 'payAmount', 'payMargin', 'payAgency', 'payOT'];
        $financegAmountArrSqlStr = implode('+', $financegAmountArr);
        foreach ($typeArr as $k => $v) {
            $where = ['date' => $date, 'type' => $k];
            if ($k == \Prj\Consts\Finance::type_xxtb) {
                $rs = $this->db->getRecord($tbFinanceg, 'sum('.$financegAmountArrSqlStr.') as amount', ['date' => $date]);
                $rs['date'] = $date;
                $rs['type'] = $k;
                $this->updateDayTB($rs,$where);
            } else {
                $rs = $this->db->getRecord($tbFinance, 'sum(amount) as amount', $where);
                $rs['date'] = $date;
                $rs['type'] = $k;
                $this->updateDayTB($rs,$where);
            }

        }

        error_log("$tbFinanceDay>>>新增了 $this->addNum 条！");
        error_log("$tbFinanceDay>>>更新了 $this->updateNum 条！");


        if ($this->_counterCalled == 1) {
            error_log("[TRace]" . __CLASS__ . '# first by ' . $m . ' #' . $this->_counterCalled);
        } else {
            error_log("[TRace]" . __CLASS__ . '# continue by ' . $m . ' #' . $this->_counterCalled);
        }
        $this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息

        return true;
    }

    protected function updateDayTB($rs,$where){
        $tbFinanceDay = 'db_kkrpt.tb_finance_day';
        $rs['createTime'] = date('YmdHis');
        $rs['amount']-=0;
        if (!$dayRs = $this->db->getRecord($tbFinanceDay, '*', $where)) {
            $this->db->addRecord($tbFinanceDay, $rs);
            $this->addNum++;
        } else {
            if($rs['amount']!=$dayRs['amount']){
                $rs['iRecordVerID'] = $dayRs['iRecordVerID']+1;
                $this->db->updRecords($tbFinanceDay,$rs,$where);
                $this->updateNum++;
            }
        }
    }
}
