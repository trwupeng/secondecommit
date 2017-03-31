<?php
namespace PrjCronds;

/**
 *
 *  php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondPushNewReg&ymdh=20150819"
 * 快快贷中 推送给合作商的定时任务
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/24 0024
 * Time: 23:12
 */

class CrondPushNewReg extends \Sooh\Base\Crond\Task {

    public function init() {
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 180; //  5分钟推送一次数据。
        $this->_iissStartAfter = 1000; // 每个正点的第10分钟开始跑
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    protected $dbMysql;

    public function fre() {
        $this->dbMysql = null;
        parent::free();
    }

    public function onRun($dt)
    {
        error_log('#################################'.__CLASS__);
        $userFound = [];
        $arr_uid = $this->dbMysql->getRecords(\Rpt\Tbname::tb_user_final, 'userId,copartnerId,contractId'
            , ['notifyNewReg'=>'']);
//var_log($arr_uid, 'arr_uid>>>>>>>>>>>>');
        if (!empty($arr_uid)) {
            foreach ($arr_uid as $r) {
                $copartner = \Lib\Services\CopartnerApiBase::getCopyById($r['copartnerId']);
//var_log($copartner, 'copartner>>>>>>>');
                if (!empty($copartner)) {
                    $ret = $copartner->notifyNewReg($r['userId']);
                    if ($ret != null){
                        error_log('[Trace_newReg contractID='.$r['contractId'].']:'.$r['userId']. '##'.  json_encode($ret));

                        // 结果处理
                        if($ret->code == \Sooh\Base\RetSimple::ok) {
                            $userFound[$r['userId']] = true;
                            $record = ['notifyNewReg'=>$ret->code.'|'.$copartner->abs()];
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, $record, ['userId'=>$r['userId']]);
                            error_log('success notify ok');
                            $this->ret->newadd++;
                        }elseif($ret->code == -2) {
                            error_log('[Error on Trace_newReg]push failed with notifyNewReg() userId:'.$r['userId'].' contractId:'.$r['contractId'].' msg:'.$ret->msg);
                        }
                        elseif ($ret->code == -3) {
                            $record = ['notifyNewReg'=>$ret->msg];
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, $record, ['userId'=>$r['userId']]);
                            error_log('[Error on Trace_newReg]push failed with notifyNewReg() userId:'.$r['userId'].' contractId:'.$r['contractId'].' msg:'.$ret->msg);
                        }
                        else {
                            $record = ['notifyNewReg'=>$ret->code.'|'.$copartner->abs().'|'.$ret->msg];
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, $record, ['userId'=>$r['userId']]);
                            error_log('[Error on Trace_newReg]push failed with notifyNewReg() userId:'.$r['userId'].' contractId:'.$r['contractId'].' msg:'.$ret->msg);
                        }

                    }else {
                        $record = ['notifyNewReg'=>\Sooh\Base\RetSimple::errDefault.'|'.$copartner->abs().'|null returned with notifyNewReg'];
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, $record, ['userId'=>$r['userId']]);
                        error_log('[Error on Trace_newReg]null returned with notifyNewReg() userId:'.$r['userId'].' contractId:'.$r['contractId']);
                    }
                }else {
                    $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, ['notifyNewReg'=>'skip'], ['userId'=>$r['userId']]);
                    error_log('skip userId:'.$r['userId'].' contractId:'.$r['contractId']);
                }
            }
        }


        $this->ret->total = sizeof($userFound);
        $this->lastMsg = $this->ret->toString();
        return true;
    }
}
