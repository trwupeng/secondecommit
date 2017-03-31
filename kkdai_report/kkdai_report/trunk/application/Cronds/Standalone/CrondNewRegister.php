<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondNewRegister&ymdh=20150819"
 *
 * 时间段内，从 customer 表中获取在此时间段内注册的用户。同时 从account_info 表中获取改用户的账户信息。一起保存的报表系统的
 * tb_customer_final 表中
 *
 * 在添加新注册的用户的时候，如果该用户已经在tb_customer表中存在了也会从 account_info 中获取该用户的账户信息 来更新
 * tb_customer_final表
 *
 * TODO: 有的用户没有注册日期
 *
 *
 */

class CrondNewRegister extends \Rpt\Misc\DataCrondGather{
    protected $dbMysql;

    public function init() {
        parent::init();
        $this->_iissStartAfter=400; // 每小时的第55分 启动
        $this->dbMysql = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    public function free() {
        parent::free();
        $this->dbMysql=null;
    }

    protected function gather() {
        $db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $this->printLogOfTimeRang();
        $where = ['add_date]'=>$this->YmdHisFrom, 'add_date['=>$this->YmdHisTo];
        $arr_customer_id = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', $where);
        if ($this->ymd <= 20150701) {
            $null_add_date_userId = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', ['add_date'=>null]);
            $arr_customer_id = array_merge($arr_customer_id, $null_add_date_userId);
        }

        // 抓取 add_date 是 2015-01-02这样的用户
        $arr_customer_id_ext = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', ['add_date'=>date('Y-m-d', strtotime($this->YmdHisTo))]);
        if (!empty($arr_customer_id)) {
            $arr_customer_id = array_merge($arr_customer_id, $arr_customer_id_ext);
        }

        $arr_customer_id_ext = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', 
                ['add_date]'=>date('Y-m-d00:00:00', strtotime($this->YmdHisTo)), 'add_date['=>date('Y-m-d23:59:59', strtotime($this->YmdHisTo))]);
        if (!empty($arr_customer_id)) {
            $arr_customer_id = array_merge($arr_customer_id, $arr_customer_id_ext);
        }
        $arr_customer_id_ext = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', ['add_date'=>date('Y-m-j', strtotime($this->YmdHisTo))]);
        if (!empty($arr_customer_id)) {
            $arr_customer_id = array_merge($arr_customer_id, $arr_customer_id_ext);
        }


        $arr_customer_id_ext = $db_produce->getCol(\Rpt\Tbname::customer, 'customer_id', ['add_date'=>$this->ymd]);

        error_log('[ Trace ] ### '.__CLASS__.' ### Sizeof newRegister:'.sizeof($arr_customer_id));
        if (!empty($arr_customer_id)) {
            foreach ($arr_customer_id as $customer_id) {
                $record = $db_produce->getRecord(\Rpt\Tbname::customer, \Rpt\Fields::$produce_fields_customer,
                            array('customer_id'=>$customer_id));
//var_log($record, 'record>>>>>>>>>>>>>>>.');
//                break;
                $tmp = [];
                $tmp['userId'] = $record['customer_id'];
                $tmp['phone'] = $record['customer_cellphone'];

                if(!empty($record['realname_time'])) {
                    $tmp['ymdRealName'] = date('Ymd', strtotime($record['realname_time']));
                }

                if (empty($record['customer_cellphone'])) {
                    $tmp['phone'] = 0;
                }
                $tmp['nickname'] = $record['customer_name'];
                $tmp['realname'] = $record['customer_realname'];
                if (!empty($record['customer_idno'])) {
                    $tmp['idCard'] = $record['customer_idno'];
                    $tmp['ymdBirthday'] = substr($tmp['idCard'], -12, 8);;
                    $tmp['gender'] = (substr($tmp['idCard'], -2, 1) % 2) ? 'm' : 'f';
                }
                $tmp['userCreditGrade'] = $record['customer_creditgrade'];
                if (!empty($record['add_date'])) {
                    $timestamp = strtotime($record['add_date']);
                    $tmp['ymdReg'] = date('Ymd', $timestamp);
                    $tmp['hisReg'] = date('His', $timestamp);
                }else {
                    $tmp['ymdReg'] = 20150701;
                }

                $tmp['clientType'] = \Rpt\ClientTypeTrans::clientTypeTrans($record['pay_type']);
                //
                if (!empty($record['source'])) {
                    $copart = $record['source'];
                }elseif (!empty($record['download_source'])){
                    $copart = $record['download_source'];
                }else{
                    $copart = '999920150101000000'; // 之前老数据没有source 和download_source的
                }

                $contractId =  \Rpt\CopartnerTrans::transContractId($copart);
                if (!empty($contractId)) {
                    $tmp['copartnerId'] = \Rpt\CopartnerTrans::transCopartnerId($copart);
                    $tmp['contractId'] = $contractId;
                }else {
                    error_log('user:'.$record['customer_id'].' is not found contractId，maybe source source OR download_source is new');
                }




//                $tmp['dtLast']
                $tmp['flagUser'] = $record['flag'];
                // 邀请 暂时注释掉  TODO:
//                $inviteBy = \Rpt\InviteUser::findUserInvitedBy($record['customer_id']);
//                $tmp['inviteByUser'] = empty($inviteBy) ? '' : $inviteBy;
//                $inviteByParent = \Rpt\InviteUser::findUserInviteByParent($record['customer_id']);
//                $tmp['inviteByParent'] = empty($inviteByParent) ? '' : $inviteByParent;
//                $inviteByRoot = \Rpt\InviteUser::findUserInviterByRoot($record['customer_id']);

//                if (empty($inviteByRoot) || $inviteByRoot == $record['customer_id']) {
//                    $inviteByRoot = '';
//                }
//                $tmp['inviteByRoot'] =$inviteByRoot;

                $tmp['phoneOwnership'] = $record['phone_ownership'];
                $tmp['userOrigin'] = $record['customer_origin'];
                $tmp['cp_id'] = $record['cp_id'];
//var_log($tmp, 'tmp>>>>>>>>');
//                break;
                try {
                    \Sooh\DB\Broker::errorMarkSkip();
                    $this->dbMysql->addRecord(\Rpt\Tbname::tb_user_final, $tmp);
                    $this->ret->newadd++;
                }catch(\ErrorException $e) {
                    if (\Sooh\DB\Broker::errorIs($e)){
                        unset($tmp['userId']);
                        $this->dbMysql->updRecords(\Rpt\Tbname::tb_user_final, $tmp, ['userId'=>$customer_id]);
                        $this->ret->newupd++;
                    }else {
                        error_log($e->getMessage()."\n".$e->getTraceAsString());
                    }
                }

            }
        }
        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
    }
}
