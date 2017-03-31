<?php
namespace PrjCronds;

/**
 *
 *  php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondPushNewOrder&ymdh=20150819"
 * 快快贷中 推送给合作商的定时任务
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/24 0024
 * Time: 23:12
 */

class CrondPushNewOrder extends \Sooh\Base\Crond\Task {

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
        $orderFound = [];
        $arr_order = $this->dbMysql->getRecords(\Rpt\Tbname::tb_orders_final, 'userId,ordersId', ['notifyNewOrder'=>'']);
//var_log($arr_order, 'arra_order>>>>>>>>>>');
        if(!empty($arr_order)) {
            foreach($arr_order as $r) {
                $copartnerInfo = $this->dbMysql->getRecord(\Rpt\Tbname::tb_user_final, 'copartnerId,contractId', ['userId'=>$r['userId']]);
                if (empty($copartnerInfo)) {
                    continue;
                }
                $copartner = \Lib\Services\CopartnerApiBase::getCopyById($copartnerInfo['copartnerId']);
                if (!empty($copartner)) {
                    $ret = $copartner->notifyNewOrder($r['ordersId']);
                    error_log('[Trace_newOrder contractID='.$copartnerInfo['contractId'].']:'.$r['ordersId']. '##'.  json_encode($ret));
                    if ($ret!=null) {
                        if ($ret->code == \Sooh\Base\RetSimple::ok) {
                            $orderFound[$r['ordersId']] = true;
                            $record = ['notifyNewOrder'=>$ret->code.'|'.$copartner->abs()];
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, $record, ['ordersId'=>$r['ordersId']]);
                            $this->ret->newadd++;
                        }elseif ($ret->code == -2){
                            error_log('[Error on Trace_newOrder]push failed with notifyNewOrder() ordersId:'.$r['ordersId'].' contractId:'.$copartnerInfo['contractId'].' msg:'.$ret->msg);
                        }elseif($ret->code == -3) {
                            $record = ['notifyNewOrder'=>$ret->msg];
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, $record, ['ordersId'=>$r['ordersId']]);
                            error_log('[Error on Trace_newOrder]push failed with notifyNewOrder() ordersId:'.$r['ordersId'].' contractId:'.$copartnerInfo['contractId'].' msg:'.$ret->msg);
                        }
                        else {
                            $record = ['notifyNewOrder'=>$ret->code.'|'.$copartner->abs().'|'.$ret->msg];
                            $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, $record, ['ordersId'=>$r['ordersId']]);
                            error_log('[Error on Trace_newOrder]push failed with notifyNewOrder() ordersId:'.$r['ordersId'].' contractId:'.$copartnerInfo['contractId'].' msg:'.$ret->msg);
                        }
                    }else {
                        $record = ['notifyNewOrder'=>\Sooh\Base\RetSimple::errDefault.'|'.$copartner->abs().'|null returned with notifyNewOrder'];
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, $record, ['ordersId'=>$r['ordersId']]);
                        error_log('[Error on Trace_newOrder]null returned with notifyNewOrder() ordersId:'.$r['ordersId'].' contractId:'.$copartnerInfo['contractId']);
                    }
                }else {
                    $this->dbMysql->updRecords(\Rpt\Tbname::tb_orders_final, ['notifyNewOrder'=>'skip'], ['ordersId'=>$r['ordersId']]);
                    error_log('skip ordersId:'.$r['ordersId'].' contractId:'.$copartnerInfo['contractId']);
                }
            }
        }

        $this->ret->total = sizeof($orderFound);
        $this->lastMsg = $this->ret->toString();
        return true;
    }
}
