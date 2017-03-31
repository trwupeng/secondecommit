<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/run/crond.php "__=crond/run&task=Standalone.CrondTagUserForDisplay&ymdh=20150819"
 * 按照后台协议管理中设置的规则给用户打展示标记
 * 早上
 * 每天凌晨3点半多执行一次，给昨日的用户打标记
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/3/25 0025
 * Time: 下午 3:22
 */

class CrondTagUserForDisplay extends \Sooh\Base\Crond\Task{
    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_iissStartAfter = 3800; // 每个小时的第32分钟执行一次
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    public function free() {
        parent::free();
        $this->db_rpt=null;
    }

    protected $db_rpt;
    protected $contract_rule_fields;

    protected function onRun($dt)
    {
        $ymd = $dt->yesterday('Ymd');
        if(!$this->_isManual) {
            if($dt->hour != 3) {
                return true;
            }
        }
error_log('###################################执行日期'.$ymd.'###################################');
        foreach(\Rpt\LimitRules::$displayRule as $rule => $name) {
            switch($rule) {

                case \Rpt\LimitRules::unlimitrule:  // 没有任何规则限制的用户
                    $this->unlimitRule($ymd, $rule);
                    break;
                case \Rpt\LimitRules::regrule:      // 注册规则的用户
                    $this->regRule($ymd, $rule);
                    break;
                case \Rpt\LimitRules::bindrule:     // 绑卡规则的用户
                    $this->bindRule($ymd, $rule);
                    break;
                case \Rpt\LimitRules::buyrule:      // 购买规则的用户
                    $this->buyRule($ymd, $rule);
                    break;
                default:
                    error_log('#此规则限制没有处理：'.$rule.' '. $name);
            }
        }
        $this->toBeContinue = false;
        return true;
    }

    protected function unlimitRule($ymd, $rule) {
        $unlimit_contract = $this->getLimitContracts($rule);
        if(empty($unlimit_contract)) {
            error_log('所有协议都有扣量规则限制');
            return;
        }
        // 给没有规则限制渠道昨天的用户全部打上展示标记1
        $where = ['ymdReg'=>$ymd, 'flagDisplay'=>0, 'contractId'=>array_keys($unlimit_contract)];
        $this->db_rpt->updRecords(\Rpt\Tbname::tb_user_final, ['flagDisplay'=>1], $where);
    }


    /**
     *
     *  处理注册规则的用户
     * @param $ymd
     */
    protected function regRule ($ymd, $rule) {
        $reg_rule_contract = $this->getLimitContracts($rule);
        if(empty($reg_rule_contract)){
            error_log('无注册规则限制的协议');
            return;
        }

        $tmp_contract  = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'distinct(contractId)',
            ['contractId'=>array_keys($reg_rule_contract),'ymdReg'=>$ymd]);
        if(empty($tmp_contract)){
            error_log('注册规则：此规则的协议没有用户注册');
            return;
        }


        foreach($tmp_contract as $contractId){
            // 当日已经有打标记了，直接跳过
            $tagedNum = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final, ['ymdReg'=>$ymd, 'contractId'=>$contractId, 'flagDisplay'=>1]);
            if($tagedNum >0){
                error_log('注册规则 #协议'.$contractId.' 当日已经打过标记');
                continue;
            }

            // 此协议当日注册的用户
            $users = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final,'userId', ['ymdReg'=>$ymd, 'contractId'=>$contractId]);

            // 根据比例给用户打标记
            $rate = $reg_rule_contract[$contractId] / 100;
            $rate = ($rate >1 ? 1 : $rate);
            $total_num = sizeof($users);
            $display_num = floor($total_num * $rate);

            $tmp_users = [];
            while($display_num > 0) {
                $index = array_rand($users);
                $tmp_users[$index] = $users[$index];
                unset($users[$index]);
                $display_num--;
            }
            $users = null;

            if(!empty($tmp_users)){
                $this->db_rpt->updRecords(\Rpt\Tbname::tb_user_final, ['flagDisplay'=>1], ['userId'=>$tmp_users]);
            }
            error_log('#注册规则:'.$contractId.' #展示比列:'.$rate.' #今日总注册：'.$total_num.'个  #展示数量：'.sizeof($tmp_users));
        }
    }

    /**
     *
     * 处理认证规则的用户
     * @param $ymd
     */
    protected function bindRule($ymd, $rule) {
        $bind_rule_contract = $this->getLimitContracts($rule);
        if(empty($bind_rule_contract)) {
            error_log('无认证规则限制的协议');
            return;
        }

        // 1. 找到绑卡规则限制协议中当日注册协议
        $reg_contract = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'distinct(contractId)',
            ['contractId'=>array_keys($bind_rule_contract), 'ymdReg'=>$ymd]);
        if(empty($reg_contract)) {
            error_log('绑卡限制规则的协议中没有用户注册');
            return;
        }

        // 2. 循环给每个绑卡规则协议的用户打标签
        foreach($reg_contract as $contractId) {
            $rate = $bind_rule_contract[$contractId] /100;
            $rate = ($rate > 1 ? 1 : $rate);

            // 当日此协议已经打过标签直接跳过
            $tagedNum= $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final,
                ['contractId'=>$contractId, 'ymdReg'=>$ymd, 'flagDisplay'=>1]);
            if($tagedNum >0 ) {
                error_log('绑卡规则 #协议'.$contractId.' 当日已经打过标记');
                continue;
            }

            // 当日此协议所有注册的用户
            $users = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'userId',
                ['contractId'=>$contractId, 'ymdReg'=>$ymd]);
            $tmp_total_num = sizeof($users);

            // 此协议当日注册的用户并且当日绑卡的用户
            $bind_users = $this->db_rpt->getCol(\Rpt\Tbname::tb_bankcard_final, 'distinct(userId)',
                ['userId'=>$users, 'createYmd'=>$ymd, 'statusCode'=>[4, 16]]);
            // 此协议注册测但未绑卡的用户
            $nobind_users = array_diff($users, $bind_users);
            $users = null;

            // 要展示的用户
            $tmp_users = [];

            // 未当日注册未绑卡要展示的用户
            $display_nobind_num = floor(sizeof($nobind_users) * $rate);

            while($display_nobind_num >0) {
                $index = array_rand($nobind_users);
                $tmp_users[] = $nobind_users[$index];
                unset($nobind_users[$index]);
                $display_nobind_num--;
            }
            $nobind_users = null;

            // 当日注册并绑卡要展示的用户
            $bind_num= sizeof($bind_users);
            $tmp_display_bind_num = $display_bind_num = floor($bind_num * $rate);
            while($display_bind_num >0) {
                $index = array_rand($bind_users);
                $tmp_users[] = $bind_users[$index];
                unset($bind_users[$index]);
                $display_bind_num--;
            }
            $bind_users = null;

            if(!empty($tmp_users)) {
                $this->db_rpt->updRecords(\Rpt\Tbname::tb_user_final, ['flagDisplay'=>1], ['userId'=>$tmp_users]);
            }
            error_log('绑卡规则 #协议:'.$contractId.'#展示百分比：'.$rate.' #当日总注册:'.$tmp_total_num.' #当日注册并绑卡共'.$bind_num.'个，展示'.$tmp_display_bind_num.'个 #总共展示'.sizeof($tmp_users).'个');
        }
    }

    // 处理购买规则的用户
    protected function buyRule($ymd, $rule) {
        $buy_rule_contract = $this->getLimitContracts($rule);
        if(empty($buy_rule_contract)) {
            error_log('无购买限制规则的协议');
            return;
        }

        // 当日这些协议中有用户注册的协议
        $reg_contract = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'distinct(contractId)',
            ['ymdReg'=>$ymd, 'contractId'=>array_keys($buy_rule_contract)]);
        if(empty($reg_contract)) {
            error_log('购买限制规则的协议中没有用户注册');
            return;
        }

        foreach($reg_contract as $contractId) {

            // 今日已经打过标记 直接跳过
            $taggedNum = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_user_final,
                ['contractId'=>$contractId, 'ymdReg'=>$ymd, 'flagDisplay'=>1]);
            if($taggedNum > 0){
                error_log('购买规则 #协议'.$contractId.' 当日已经打过标记');
                continue;
            }

            $rate = $buy_rule_contract[$contractId] / 100;
            $rate = ($rate > 1 ? 1: $rate);

            // 此协议当日注册的用户
            $users = $this->db_rpt->getCol(\Rpt\Tbname::tb_user_final, 'userId', ['contractId'=>$contractId, 'ymdReg'=>$ymd]);
            $tmp_total_num = sizeof($users);

            // 此协议当日注册并购买的用户
            $buy_user = $this->db_rpt->getCol(\Rpt\Tbname::tb_orders_final, 'distinct(userId)',
                ['userId'=>$users, 'ymd'=>$ymd,'orderStatus'=>[8, 10, 39]]);
            $tmp_buy_num = sizeof($buy_user);
            // 此协议当日注册并未购买的用户
            $nobuy_user = array_diff($users, $buy_user);
            $users = null;

            // 所有要展示的用户
            $tmp_users = [];

            // 获取当日注册并未购买应该展示的用户
            $display_nobuy_num = floor(sizeof($nobuy_user) * $rate);
            while($display_nobuy_num > 0) {
                $index = array_rand($nobuy_user);
                $tmp_users[] = $nobuy_user[$index];
                unset($nobuy_user[$index]);
                $display_nobuy_num--;
            }


            // 获取当日注册并购买应该展示的用户
            $display_buy_num = $tmp_display_buy_num = floor($tmp_buy_num * $rate);
            while($display_buy_num > 0) {
                $index = array_rand($buy_user);
                $tmp_users[] = $buy_user[$index];
                unset($buy_user[$index]);
                $display_buy_num--;
            }

            if(!empty($tmp_users)){
                $this->db_rpt->updRecords(\Rpt\Tbname::tb_user_final, ['flagDisplay'=>1], ['userId'=>$tmp_users]);
            }
            error_log('购买规则 #协议:'.$contractId.'#展示百分比：'.$rate.' #当日总注册:'.$tmp_total_num.' #当日注册并购买共'.$tmp_buy_num.'个，展示'.$tmp_display_buy_num.'个 #总共展示'.sizeof($tmp_users).'个');
        }


    }


    protected function getLimitContracts ($rule) {
        return $this->db_rpt->getPair(\Rpt\Tbname::tb_contract, 'contractId', 'displayPercent', ['displayRule'=>$rule]);
    }
}
