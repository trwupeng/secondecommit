<?php
/**
 * 投标统计的任务
 * 数据用户投标统计中的月投标和日投标
 *
 * 凌晨1 到 6点每个小时修复前几天某一天的数据 并且跑一次当天的数据
 *
 * 6点之后每10分钟跑一次 当天的数据
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.Crondbidstatistics&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/19 0002
 * Time: 上午 13:47
 */
namespace PrjCronds;
use Prj\Consts\PayGW;

class Crondbidstatistics extends \Sooh\Base\Crond\Task {
    protected $db_rpt;
    protected $db_produce;
    public function init() {
        parent::init();
        $this->_iissStartAfter = 1655;
        $this->_secondsRunAgain = 900;  // 十五分钟跑一次
        $this->toBeContinue = true;
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected function onRun($dt)
    {
        if (!$this->_isManual && $dt->hour<=6) {
            $dt0 = strtotime($dt->YmdFull);
            switch($dt->hour) {
                case 1: $this->oneday(date('Ymd', $dt0-86400*10));break;
                case 2: $this->oneday(date('Ymd', $dt0-86400*2));break;
                case 3: $this->oneday(date('Ymd', $dt0-86400*1));break;
                case 4: $this->oneday(date('Ymd', $dt0-86400*4));break;
                case 5: $this->oneday(date('Ymd', $dt0-86400*3));break;
                case 6: $this->oneday(date('Ymd', $dt0-86400*7));break;
            }
            $this->oneday($dt->YmdFull);
            $this->toBeContinue = false;
        }
        $this->oneday($dt->YmdFull);
        error_log(__CLASS__.' '.$dt->YmdFull.' finished');
        return true;
    }

    protected function oneday($ymd) {

        $records = [];
        /**
         * 非超级用户投资成功总额, 总次数
         */
        $where = 'where ymd='.$ymd
            .' and orderStatus in (8, 10, 39)'
            .' and userId not in (select userId from tb_user_final where flagUser=1)';
        $rs = $this->dailybid($where);
        if(!empty($rs)) {
            $records['amount_succ_normal'] = $rs['sumAmount'];
            $records['count_succ_normal'] = $rs['n'];
        }

        /**
         * 非超级用户投资失败总额, 总次数 TODO 失败状态
         */
//        $where = 'where ymd='.$ymd
//            .' and orderStatus in (18, 110, 139)'
//            .' and userId not in (select userId from tb_user_final where flagUser=1)';
//        $rs = $this->dailybid($where);
//        if(!empty($rs)) {
//            $records['amount_fail_normal'] = $rs['sumAmount'];
//            $records['count_fail_normal'] = $rs['n'];
//        }
        /**
         * 超级用户投资成功总额, 总次数
         */
        $where = 'where ymd='.$ymd
            .' and orderStatus in (8, 10, 39)'
            .' and userId in (select userId from tb_user_final where flagUser=1)';
        $rs = $this->dailybid($where);
        if(!empty($rs)) {
            $records['amount_succ_super'] = $rs['sumAmount'];
            $records['count_succ_super'] = $rs['n'];
        }

        /**
         * 超级用户投资失败总额, 总次数 TODO 失败状态
         */
//        $where = 'where ymd='.$ymd
//            .' and orderStatus in (18, 110, 139)'
//            .' and userId in (select userId from tb_user_final where flagUser=1)';
//        $rs = $this->dailybid($where);
//        if(!empty($rs)) {
//            $records['amount_fail_super'] = $rs['sumAmount'];
//            $records['count_fail_super'] = $rs['n'];
//        }


//var_log($records, 'records############');
        if(!empty($records)) {
            // 先删除这一天的
            $this->db_rpt->delRecords('db_kkrpt.tb_bidstatistics', ['ymd'=>$ymd]);
            $this->db_rpt->ensureRecord('db_kkrpt.tb_bidstatistics',
                array_merge($records, ['ymd'=>$ymd]), array_keys($records));
        }
        $this->oneMonth($ymd);
    }

    protected function oneMonth ($ymd) {
        $timestamp = strtotime($ymd);
        $ym = date('Ym', strtotime($ymd));
        $ymdFrom = $ym.'01';
        $ymdTo= $ym.'31';
        $records = [];

        // 超级用户月投资金额
        $where = ' where ymd>='.$ymdFrom
              . ' and ymd<='.$ymdTo
              . ' and orderStatus in (8, 10, 39)'
              . ' and userId in ('
              . '  select userId from tb_user_final where flagUser=1'
              . ' )';
        $rs = $this->dailybid($where);
        !empty($rs) && $records['amount_succ_super'] = $rs['sumAmount'];

        // 普通用户的月投资金额
        $where = ' where ymd>='.$ymdFrom
                .' and ymd<='.$ymdTo
                .' and orderStatus in (8, 10, 39)'
                .' and userId not in ('
                .'  select userId from tb_user_final where flagUser=1)';
        $rs = $this->dailybid($where);
        !empty($rs) && $records['amount_succ_normal'] = $rs['sumAmount'];

        if(!empty($records)) {
            $records['total']
                = $records['amount_succ_super']+$records['amount_succ_normal'];
            $this->db_rpt->delRecords('db_kkrpt.tb_bidmonth', ['ym'=>$ym]);
            $this->db_rpt->ensureRecord('db_kkrpt.tb_bidmonth',
                array_merge($records,['ym'=>$ym]),
                array_keys($records));
        }
    }

    private function dailybid ($where) {

        $sql  ='select count(*) as n, sum(amount+amountExt)as sumAmount from tb_orders_final '
            . $where
            .' and waresId not in (select waresId from tb_products_final where statusCode = 4011 or  mainType=501)'
            .' and poi_type=0';
        $rs =  $this->execSql($sql, $this->db_rpt);
        if($rs[0]['n'] >0) {
            return ['n'=>$rs[0]['n'], 'sumAmount'=>$rs[0]['sumAmount']];
        }else {
            return [];
        }
    }

    protected function execSql($sql, $db) {
        $result = $db->execCustom(['sql'=>$sql]);
        return $db->fetchAssocThenFree($result);
    }
}