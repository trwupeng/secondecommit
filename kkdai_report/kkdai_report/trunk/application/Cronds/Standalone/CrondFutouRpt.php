<?php
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondFutouRpt&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/2 0002
 * Time: 上午 9:47
 */
namespace PrjCronds;
class CrondFutouRpt extends \Sooh\Base\Crond\Task {
    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter = 1655;
        $this->toBeContinue = true;
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected function onRun($dt)
    {
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
        }elseif($this->_isManual) {
            $this->oneday($dt->YmdFull);
        }
        $this->toBeContinue = false;
        return true;
    }

    protected function oneday($ymd) {
        // 先删除这一天的
        $this->dbMysql->delRecords('db_kkrpt.tb_futou', ['ymd'=>$ymd]);
        $sql = 'SELECT contractId,'
            . ' COUNT(CASE WHEN N >= 1 THEN userId END) AS n1,'
            . ' COUNT(CASE WHEN N >= 2 THEN userId END) AS n2,'
            . ' COUNT(CASE WHEN N >= 3 THEN userId END) AS n3,'
            . ' COUNT(CASE WHEN N >= 4 THEN userId END) AS n4,'
            . ' COUNT(CASE WHEN N >= 5 THEN userId END) AS n5'
            . ' FROM tb_user_final AS tuf'
            . ' LEFT JOIN ('
            . '     SELECT userId,COUNT(userId) AS N'
            . '     FROM tb_orders_final'
            . '     WHERE ymd <='.$ymd
            . '     AND waresId NOT IN ('
            . '        SELECT waresId FROM tb_products_final WHERE	statusCode = 4011 OR mainType = 501'
            . '     )'
            . '     AND poi_type = 0'
            . '     GROUP BY userId'
            . ' ) AS tof USING (userId)'
            .' WHERE flagUser <> 1'
            .' GROUP BY contractId';
        $rs = $this->execSql($sql, $this->dbMysql);
//var_log($rs);
        foreach($rs as $v) {
            $fields = [
                'ymd'=>$ymd,
                'contractId'=>$v['contractId'],
            ];
            $upd = [
                'copartnerId'=>substr($v['contractId'], 0, 4),
                'n1'=>$v['n1'],
                'n2'=>$v['n2'],
                'n3'=>$v['n3'],
                'n4'=>$v['n4'],
                'n5'=>$v['n5'],
            ];
            $this->dbMysql->ensureRecord('db_kkrpt.tb_futou', array_merge($fields, $upd), array_keys($upd));
        }
    }

    protected function execSql($sql, $db) {
        $result = $db->execCustom(['sql'=>$sql]);
        $rs = $db->fetchAssocThenFree($result);
        return $rs;
    }
}