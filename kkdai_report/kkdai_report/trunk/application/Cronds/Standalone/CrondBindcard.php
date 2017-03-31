<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondBindcard&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/22 0022
 * Time: 上午 11:13
 */
class CrondBindcard extends \Rpt\Misc\DataCrondGather {
    protected $db_mysql;
    public function init() {
        parent::init();
        $this->_iissStartAfter=300; // 每小时的第55分 启动
        $this->db_mysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function free() {
        parent::free();
        $this->db_mysql = null;
    }

    protected function gather(){
        $db_produce = \Sooh\DB\Broker::getInstance(\rpt\Tbname::db_p2p);

        /**
         * 绑卡成功的记录
         */
        $this->printLogOfTimeRang();
        $where = ['binding_date]'=>$this->YmdHisFrom, 'binding_date['=>$this->YmdHisTo];
        $arr_bind_id = $db_produce->getCol(\Rpt\Tbname::account_card_binding, 'binding_id', $where);
        $this->ret->total = sizeof($arr_bind_id);

        if ($this->ret->total >0) {
            $arr_bind_id = array_chunk($arr_bind_id, 1000);
            foreach ($arr_bind_id as $group) {
                $bindings = $db_produce->getRecords(\Rpt\Tbname::account_card_binding,
                    \Rpt\Fields::$produce_fields_account_card_binding, ['binding_id'=>$group]);
                foreach ($bindings as $r){
                    $tmp = [
                        'orderId' => $r['binding_id'],
                        'userId'=>$r['customer_id'],
                        'bankId'=>$r['bank'],
                        'bankCard'=>$r['card_no'],
                        'isDefault'=>1,
                        'createYmd'=>date('Ymd', strtotime($r['add_date'])),
                        'createHis'=>date('His', strtotime($r['add_date'])),
                        'resultYmd'=>date('Ymd', strtotime($r['binding_date'])),
                        'resultHis'=>date('His', strtotime($r['binding_date'])),
                        'cellphone'=>$r['cellphone'],
                        'statusCode'=>\Prj\Consts\BankCard::enabled,
                    ];

                    if (!empty($r['channel'])){
                        $clientType = \Rpt\ClientTypeTrans::clientTypeSearch($r['channel']);
                    }else {
                        $clientType = \Rpt\ClientTypeTrans::getClientTypeFromUser($r['customer_id']);
                        if (empty($clientType)) {
                            $clientType = \Prj\Consts\ClientType::www;
                        }
                    }
                    $tmp['clientType'] = $clientType;
                    try{
                        \Sooh\DB\Broker::errorMarkSkip();
                        $this->db_mysql->addRecord(\Rpt\Tbname::tb_bankcard_final, $tmp);
                        $this->ret->newadd++;
                    }catch(\ErrorException $e) {
                        if(\Sooh\DB\Broker::errorIs($e)){
                            unset($tmp['orderId']);
                            $this->db_mysql->updRecords(\Rpt\Tbname::tb_bankcard_final, $tmp, ['orderId'=>$r['binding_id']]);
                            $this->ret->newupd++;
                        }
                    }

                    // 修改该用户其他银行卡为禁用的
                    $where = ['userId'=>$r['customer_id'], 'statusCode'=>\Prj\Consts\BankCard::enabled, 'orderId!'=>$r['binding_id']];
                    $arr_binding_id = $this->db_mysql->getCol(\Rpt\Tbname::tb_bankcard_final, 'orderId', $where);
                    if (!empty($arr_bind_id)) {
                        $tmp = [
                            'statusCode'=>\Prj\Consts\BankCard::disabled,
                            'isDefault'=>0,
                        ];
                        foreach ($arr_binding_id as $orderId) {
                            $this->db_mysql->updRecords(\Rpt\Tbname::tb_bankcard_final, $tmp, ['orderId'=>$orderId]);
                        }
                    }

                    /**
                     *
                     * 更新实名到用户表
                     */
                    // 用户实名
                    $tmp_real_info = [];
                    $realInfo = $db_produce->getRecord(\Rpt\Tbname::customer, 'customer_realname,customer_idno,add_date,phone_ownership,customer_origin,pay_type',
                        ['customer_id'=>$r['customer_id']]);
                    if(!empty($realInfo['add_date'])) {
                        $timestamp = strtotime($realInfo['add_date']);
                        $tmp_real_info['ymdReg'] = date('Ymd', $timestamp);
                        $tmp_real_info['hisReg'] = date('His', $timestamp);
                    }else {
                        $tmp_real_info['ymdReg'] = 20150701;
                    }

                    // 9月2号线上从库增加了实名时间. 现在将9月2号之后注册的用户的绑卡时间填上
                    if($tmp_real_info['ymdReg']>= 20160902) {
                        $ymdFirstBind = $this->db_mysql->getOne (\Rpt\Tbname::tb_bankcard_final, 'createYmd', ['statusCode'=>[4, 16], 'userId'=>$r['customer_id']], 'sort CreateYmd');
                        if($ymdFirstBind>0){
                            $tmp_real_info['ymdBindcard'] = $ymdFirstBind;
                        }
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
                        $tmp_copart = \Rpt\User::getCopartnerIdAndContractId($r['customer_id']);
                    }catch(\ErrorException $e) {
                        $tmp_copart=[];
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }
//var_log($tmp_copart, 'tmp_copart#########');
                    $tmp_real_info = array_merge($tmp_real_info, $tmp_copart);

                    $upd_fields = array_keys($tmp_real_info);
                    $tmp_real_info['userId'] = $r['customer_id'];
                    $this->db_mysql->ensureRecord(\Rpt\Tbname::tb_user_final, $tmp_real_info, $upd_fields);

                }
            }
        }


        /**
         * 绑卡失败记录
         */

        $where = ['create_time]'=>$this->YmdHisFrom, 'create_time['=>$this->YmdHisTo,'opt_type'=>1003];
        $arr_bind_id = $db_produce->getCol(\Rpt\Tbname::user_behavior, 'id', $where);
        $this->ret->total += sizeof($arr_bind_id);
        if (empty($arr_bind_id)) {
            $this->lastMsg = $this->ret->toString();
            error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
            return true;
        }

        $arr_bind_id = array_chunk($arr_bind_id, 1000);
        foreach($arr_bind_id as $group) {
            $bindRecords = $db_produce->getRecords(\Rpt\Tbname::user_behavior, \Rpt\Fields::$produce_fields_user_behavior, ['id'=>$group]);
            foreach ($bindRecords as $r) {
                $tmp = [
                    'orderId' => $r['id'],
                    'userId' => $r['customer_id'],

                    'bankCard' => 0,
                    'createYmd'=>date('Ymd', strtotime($r['create_time'])),
                    'createHis'=>date('His', strtotime($r['create_time'])),
                    'resultYmd'=>date('Ymd', strtotime($r['create_time'])),
                    'resultHis'=>date('His', strtotime($r['create_time'])),
                ];

                if (!empty($r['bank'])){
                    $tmp['bankId'] = $r['bank'];
                }

                if (!empty($r['channel'])){
                   $clientType = \Rpt\ClientTypeTrans::clientTypeSearch($r['channel']);
                }else {
                    $clientType = \Rpt\ClientTypeTrans::getClientTypeFromUser($r['customer_id']);
                    if (empty($clientType)) {
                        $clientType = \Prj\Consts\ClientType::www;
                    }
                }
                $tmp['clientType'] = $clientType;

                $status_desc = explode(',', $r['summary']);
                $tmp['statusCode'] = $status_desc[0];
                $tmp['resultDesc'] = $status_desc[1];
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $this->db_mysql->addRecord(\Rpt\Tbname::tb_bankcard_final, $tmp);
                    $this->ret->newadd++;
                }catch(\ErrorException $e) {
                    if(\Sooh\DB\Broker::errorIs($e)){
                        unset($tmp['orderId']);
                        $this->db_mysql->updRecords(\Rpt\Tbname::tb_bankcard_final, $tmp, ['orderId'=>$r['id']]);
                        $this->ret->newupd++;
                    }
                }
            }
        }

        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }

}
