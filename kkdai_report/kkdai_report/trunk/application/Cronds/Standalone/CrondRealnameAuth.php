<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=StandaloneCrondRealnameAuth&ymdh=20150819"
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/17 0017
 * Time: 上午 9:29
 */

class CrondRealnameAuth extends \Sooh\Base\Crond\Task {


    public function init () {
        parent::init();
        $this->toBeContinue = true;
        $this->_iissStartAfter=5500; // 每小时的第55分 启动
        $this->ret = new \Sooh\Base\Crond\Ret();

    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        error_log('[ Trace ] ### '.__CLASS__.' ###');
        $db_rpt = \Sooh\Db\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $db_produce = \Sooh\Db\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $where = ['customer_realname!'=>null];
        $product_user = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', $where);

        $where = ['realname!'=>''];
        $rpt_user = $db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'userId', $where);

        $diff = array_diff($product_user, $rpt_user);
        $this->ret->newupd =  $this->ret->total = sizeof($diff);
        if (!empty($diff)) {
            foreach ($diff as $u) {
                $tmp_real_info = [];
                $realInfo = $db_produce->getRecord(\Rpt\Tbname::customer, 'customer_realname,customer_idno,add_date,phone_ownership,customer_origin,pay_type',
                    ['customer_id'=>$u]);
                if(!empty($realInfo['add_date'])) {
                    $timestamp = strtotime($realInfo['add_date']);
                    $tmp_real_info['ymdReg'] = date('Ymd', $timestamp);
                    $tmp_real_info['hisReg'] = date('His', $timestamp);
                }else {
                    $tmp_real_info['ymdReg'] = 20150701;
                }

                $tmp_real_info['clientType'] =\Rpt\ClientTypeTrans::clientTypeTrans($realInfo['pay_type']);
                $tmp_real_info['realname'] = $realInfo['customer_realname'];
                if (!empty($realInfo['customer_idno'])) {
                    $tmp_real_info['idCard'] = $realInfo['customer_idno'];
                    $tmp_real_info['ymdBirthday'] = substr($tmp_real_info['idCard'], -12, 8);;
                    $tmp_real_info['gender'] = (substr($tmp_real_info['idCard'], -2, 1) % 2) ? 'm' : 'f';
                }

                // 用户的渠道号
                try {
                    $tmp_copart = \Rpt\User::getCopartnerIdAndContractId($u);
                }catch(\ErrorException $e) {
                    error_log($e->getMessage()."\n".$e->getTraceAsString());
                }
                $tmp_real_info = array_merge($tmp_real_info, $tmp_copart);
                $upd_fields = array_keys($tmp_real_info);
                $tmp_real_info['userId'] = $u;
                $db_rpt->ensureRecord(\Rpt\Tbname::tb_user_final, $tmp_real_info, $upd_fields);
            }

        }


        $this->toBeContinue = false;
        $this->lastMsg = $this->ret->toString();
        return true;
    }
}