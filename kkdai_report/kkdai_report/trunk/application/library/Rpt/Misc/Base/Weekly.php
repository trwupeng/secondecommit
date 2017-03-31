<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/20
 * Time: 18:51
 */
namespace Rpt\Misc\Base;

class Weekly {
    const dbConf = 'dbForRpt';
    const tbWeek = 'db_kkrpt.tb_evtweekly';
    const tbDay = 'db_kkrpt.tb_evtdaily';
    public $name = '';
    public $startDate = 0;
    public $endDate = 0;
    public $weekDay = 6; //从周几开始

    public static function getCopy($name,$startDate){
        $o = new Weekly();
        $o->name = $name;
        $o->startDate = $startDate;
        $o->checkStartDate();
        $o->endDate = date('Ymd',strtotime('+7 days',$o->startDate));
        return $o;
    }

    /**
     * 从日报里添加周报
     * @param string $dailyName
     * @param int $clienttype
     * @param int $copartnerId
     * @param int $flgext01
     * @param int $contractid
     * @throws \ErrorException
     */
    public function addFromDaily($dailyName = '',$clienttype = 0,$copartnerId=0,$flgext01=0,$contractid=0){
        if(empty($dailyName))$dailyName = $this->name;
        $db = \Sooh\DB\Broker::getInstance(self::dbConf);
        $dayRs = $db->getRecord(self::tbDay,'sum(n) as n',['ymd]'=>$this->startDate,'ymd['=>$this->endDate,'act'=>$dailyName]);
        var_log($dayRs,'dayrs>>>>>>>>>>>');
        $dayRs['n']-=0;
        $dayRs['ymd'] = $this->startDate;
        $dayRs['act'] = $this->name;
        $dayRs['clienttype'] = $clienttype;
        $dayRs['copartnerId'] = $copartnerId;
        $dayRs['flgext01'] = $flgext01;
        $dayRs['contractid'] = $contractid;
        $where = ['ymd'=>$this->startDate,'act'=>$this->name];
        if(!$weekRs = $db->getRecord(self::tbWeek,'*',$where)){
            $db->addRecord(self::tbWeek,$dayRs);
        }else{
            $db->updRecords(self::tbWeek,$dayRs,$where);
        }
    }

    protected function checkStartDate(){
        if(date('w',strtotime($this->startDate))!=$this->weekDay)throw new \ErrorException('请输入正确的起始日期');
    }
}