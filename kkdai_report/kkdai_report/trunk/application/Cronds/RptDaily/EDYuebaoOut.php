<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDYuebaoOut&ymdh=20160126"
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/26 0026
 * Time: 下午 5:26
 */

class EDYuebaoOut extends \Sooh\Base\Crond\Task{

    protected $ymd;
    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 600;
        $this->_iissStartAfter = 55;

        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db = \Sooh\DB\Broker::getInstance();
    }

    public function free() {
        $this->db = null;
        parent::free();
    }

    protected function onRun($dt)
    {
        $this->oneday($dt->YmdFull);
        if (!$this->_isManual && $dt->hour <= 6) {
            $dt0 = strtotime($dt->YmdFull);
            switch($dt->hour) {
                case 1: $this->oneday(date('Ymd',$dt0-86400*10));break;
                case 2: $this->oneday(date('Ymd',$dt0-86400*7));break;
                case 3: $this->oneday(date('Ymd',$dt0-86400*4));break;
                case 4: $this->oneday(date('Ymd',$dt0-86400*3));break;
                case 5: $this->oneday(date('Ymd',$dt0-86400*2));break;
                case 6: $this->oneday(date('Ymd',$dt0-86400*1));break;
            }
        }
        return true;
    }

    protected function oneday($ymd) {
        $this->ymd = $ymd;
error_log('################################'.$ymd.' '.__CLASS__);
        $where = 'type = -1 and orderStatus = 39 and ymd='.$ymd;
        $sql = 'SELECT tb_user_final.copartnerId, tb_user_final.flagUser as uType, COUNT(DISTINCT(tb_yuebao_out.userId)) as u, -sum(tb_yuebao_out.amount)/100 as n'
            .' FROM `tb_yuebao_out`'
            .' left join tb_user_final on tb_yuebao_out.userId = tb_user_final.userId'
            .' where '.$where
            .' group by tb_user_final.copartnerId, tb_user_final.flagUser';


        $yebOutAmount = \Rpt\EvtDaily\YebOutAmountAll::getCopy('YebOutAmountAll');


        $yebOutUserNum = \Rpt\EvtDaily\YebOutUserNumAll::getCopy('YebOutUserNumAll');

        $this->gowith($yebOutUserNum, $yebOutAmount, $sql);
    }

    protected function getResult($sql){
        $result = $this->db->execCustom(['sql'=>$sql]);
        $rs = $this->db->fetchAssocThenFree($result);
        return $rs;
    }

    protected function gowith ($user, $amount, $sql) {
//        error_log($sql);
        $result = $this->db->execCustom(['sql'=>$sql]);
        $rs = $this->db->fetchAssocThenFree($result);
        $user->reset();
        $amount->reset();
        foreach ($rs as $r){
            $user->add($r['u'], 0, $r['copartnerId'], 0, $r['uType']-0);
            $amount->add($r['n'], 0, $r['copartnerId'], 0, $r['uType']-0);
        }
//		var_log($rs,  \Sooh\DB\Broker::lastCmd());
        $user->save($this->db, $this->ymd);
        $amount->save($this->db, $this->ymd);
    }
}