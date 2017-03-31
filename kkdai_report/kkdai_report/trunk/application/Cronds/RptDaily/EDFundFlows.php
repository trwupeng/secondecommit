<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDFundFlows&ymdh=20160126"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/27 0027
 * Time: 下午 3:35
 */

class EDFundFlows extends  \Sooh\Base\Crond\Task {
    public function init() {
        parent::init();
        $this->toBeContinue =true;
        $this->_iissStartAfter = 2655;
        $this->ret = new \Sooh\Base\Crond\Ret();
    }

    public function free() {
        parent::free();
    }

    protected function onRun ($dt) {
        if ($this->_isManual) {
            $ymd = $dt->YmdFull;
        }else {
            $dt0 = strtotime($dt->YmdFull);
            switch ($dt->hour) {
                case 1: $ymd = date('Ymd',$dt0-86400*10);break;
                case 2: $ymd = date('Ymd',$dt0-86400*7);break;
                case 3: $ymd = date('Ymd',$dt0-86400*4);break;
                case 4: $ymd = date('Ymd',$dt0-86400*3);break;
                case 5: $ymd = date('Ymd',$dt0-86400*2);break;
                case 6: $ymd = date('Ymd',$dt0-86400*1);break;
                case 11:  $ymd = date('Ymd',$dt0-86400*1);break;
                case 15:  $ymd = date('Ymd',$dt0-86400*1);break;
                case 17: $ymd = date('Ymd',$dt0-86400*1);break;
                case 19:  $ymd = date('Ymd',$dt0-86400*1);break;
            }
        }
error_log('###############'.__CLASS__.' '.$ymd);
        $this->oneday($ymd);
        $this->toBeContinue = false;
        return true;
    }

//`yeb_sell_flow_withdraw` bigint(32) DEFAULT '0' COMMENT '天天赚转出资金流向,提现',
//`yeb_sell_flow_dyb` bigint(32) DEFAULT '0' COMMENT '天天赚转出资金流向,抵押标',
//`yeb_sell_flow_yeb_buy` bigint(32) DEFAULT '0' COMMENT '天天赚转出资金流向,转回天天赚',
//`yeb_sell_flow_account` bigint(32) DEFAULT '0' COMMENT '天天赚转出资金流向,存钱罐账户',
//`bid_repay_flow_withdraw` bigint(32) DEFAULT '0' COMMENT '标的还款资金流向,提现',
//`bid_repay_flow_dyb` bigint(32) DEFAULT '0' COMMENT '标的还款资金流向,抵押标',
//`bid_repay_flow_yeb_buy` bigint(32) DEFAULT '0' COMMENT '标的还款资金流向,转回天天赚',
//`bid_repay_flow_account` bigint(32) DEFAULT '0' COMMENT '标的还款资金流向,存钱罐账户',


    protected function oneday($ymd){
        $db_p2p = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        $db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $ymdFormat = date('Y-m-d', strtotime($ymd));
        $yeb_fund_flow_withdraw = \Rpt\EvtDaily\YebFundFlowsWithdraw::getCopy('YebFundFlowsWithdraw');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'yeb_sell_flow_withdraw', ['effect_date'=>$ymdFormat]);
        if ($n>0){
            error_log($n);
            $yeb_fund_flow_withdraw->add($n/100, 0, 0, 0);
            $yeb_fund_flow_withdraw->save($db_rpt, $ymd);
        }
        $yeb_fund_flow_withdraw->reset();

        $yeb_fund_flow_dyb = \Rpt\EvtDaily\YebFundFlowsDyb::getCopy('YebFundFlowsDyb');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'yeb_sell_flow_dyb', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $yeb_fund_flow_dyb->add($n/100, 0, 0);
            $yeb_fund_flow_dyb->save($db_rpt, $ymd);
        }
        $yeb_fund_flow_dyb->reset();

        $yeb_fund_flow_Account = \Rpt\EvtDaily\YebFundFlowsAccount::getCopy('YebFundFlowsAccount');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'yeb_sell_flow_account', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $yeb_fund_flow_Account->add($n/100, 0, 0);
            $yeb_fund_flow_Account->save($db_rpt, $ymd);
        }
        $yeb_fund_flow_Account->reset();

        $yeb_fund_flow_yeb = \Rpt\EvtDaily\YebFundFlowsYebbuy::getCopy('YebFundFlowsYebbuy');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'yeb_sell_flow_yeb_buy', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $yeb_fund_flow_yeb->add($n/100, 0, 0);
            $yeb_fund_flow_yeb->save($db_rpt, $ymd);
        }
        $yeb_fund_flow_yeb->reset();

        $bid_fund_flow_account = \Rpt\EvtDaily\BidFundFlowsAccount::getCopy('BidFundFlowsAccount');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'bid_repay_flow_account', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $bid_fund_flow_account->add($n/100, 0, 0);
            $bid_fund_flow_account->save($db_rpt, $ymd);
        }
        $bid_fund_flow_account->reset();

        $bid_fund_flow_dyb = \Rpt\EvtDaily\BidFundFlowsDyb::getCopy('BidFundFlowsDyb');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'bid_repay_flow_dyb', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $bid_fund_flow_dyb->add($n/100, 0, 0);
            $bid_fund_flow_dyb->save($db_rpt, $ymd);
        }
        $bid_fund_flow_dyb->reset();

        $bid_fund_flow_withdraw = \Rpt\EvtDaily\BidFundFlowsWithdraw::getCopy('BidFundFlowsWithdraw');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'bid_repay_flow_withdraw', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $bid_fund_flow_withdraw->add($n/100, 0, 0);
            $bid_fund_flow_withdraw->save($db_rpt, $ymd);
        }
        $bid_fund_flow_withdraw->reset();

        $bid_fund_flow_yeb = \Rpt\EvtDaily\BidFundFlowsYebBuy::getCopy('BidFundFlowsYebBuy');
        $n = $db_p2p->getOne(\Rpt\Tbname::licai_core_data, 'bid_repay_flow_yeb_buy', ['effect_date'=>$ymdFormat]);
        if ($n>0) {
            $bid_fund_flow_yeb->add($n/100, 0, 0);
            $bid_fund_flow_yeb->save($db_rpt, $ymd);
        }
        $bid_fund_flow_yeb->reset();


    }

}