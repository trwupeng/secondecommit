<?php
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondLiuCunRpt&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/2 0002
 * Time: 下午 1:46
 */
namespace PrjCronds;
class CrondLiuCunRpt extends \Sooh\Base\Crond\Task {

    protected $dbMysql;
    public function init () {
        parent::init();
        $this->toBeContinue = true;
        $this->_iissStartAfter=500; // 每小时的5分 启动
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function free() {
        $this->dbMysql = null;
        parent::free();
    }

    protected function onRun($dt) {


        if(!$this->_isManual && $dt->hour == 0) {
            $ymd = $dt->YmdFull;
            $this->oneday($ymd);

        }else {
            $ymd = date('Ymd');
            $this->oneday($ymd);
        }

        $this->toBeContinue = false;
        return true;
    }

    protected function oneday($ymd){
        // 无投资有余额用户
        $where = 'ymdFirstBuy=0 and wallet>0';
        $this->getData($ymd, $where, $this->dbMysql, 'notLicaiHasBalance');

        // 有投资无余额用户
        $where = 'wallet=0 and ymdFirstBuy>0';
        $this->getData($ymd, $where, $this->dbMysql, 'licaiNoBalance');

        // 有投资有余额
        $where = 'wallet>0 and ymdFirstBuy>0';
        $this->getData($ymd, $where, $this->dbMysql, 'licaiHasBalance');
    }

    protected function getData($ymd, $where, $db, $column) {
        $sql = 'select contractId, count(*) as n from tb_user_final'
            .' where '.$where
            .' and flagUser!=1'
            .' group by contractId';
        $rs = $this->execSql($sql, $db);

        foreach($rs as $v) {
            $fields = [
                'ymd'=>$ymd,
                'contractId'=>$v['contractId'],
            ];
            $upd = [
                $column=>$v['n'],
            ];
            $this->dbMysql->ensureRecord('db_kkrpt.tb_liucun', array_merge($fields, $upd), array_keys($upd));
        }
    }

    protected function execSql($sql, $db) {
        $result = $db->execCustom(['sql'=>$sql]);
        $rs = $db->fetchAssocThenFree($result);
        return $rs;
    }
}