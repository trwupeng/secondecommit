<?php
use \Rpt\EvtDaily\Base as prjlib_evtdaily;
/**
 * 每日数据简报，包括注册数，购买数，绑卡数等（数字、分布图、走势图）
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
require_once __DIR__.'/Rptsimple2Ctrl.php';

class RptdailybasicController extends \Rptsimple2Ctrl {
	protected function fillRs(&$rs,$evt,$ymd,$tomorrow,$db,$where=null){
		$kk = '{'.$evt.'}';
		$rs[$ymd][$kk] = prjlib_evtdaily::getCopy($evt)->numOfAct($db, $ymd,null,$where);
		$rs[$ymd]['{'.$evt.'_UP}']=0;
		if(isset($rs[$tomorrow][$kk])){
			if($rs[$ymd][$kk]>0){
				$rs[$tomorrow]['{'.$evt.'_UP}'] = sprintf('%.1f',($rs[$tomorrow][$kk]-$rs[$ymd][$kk])/$rs[$ymd][$kk]*100);
			}else{
				$rs[$tomorrow]['{'.$evt.'_UP}'] = 0;
			}
		}
	}
	/**
	 * 日报摘要
	 * 
	 */
	public function recentAction()
{
	$acl = \Prj\Acl\RptLimit::initWho($this->manager);
	$lst = [];
	if($acl->hasOperation()){
		$lst['p_newreg']=[
				'fmt'=>['平台新增注册情况','新增注册：{NewRegister}人(增长{NewRegister_UP}%)<br>其中{RegAndBindOk}人绑卡成功，{RegAndBindFailed}人绑卡失败。其中有{BuyNUsrNew0}人当日购买'],
				'extraRate'=>['{reg2bind}'=>['{BindNew0}','{NewRegister}'],'{reg2buy}'=>['{BuyNUsrNew0}','{NewRegister}']],
				'rights'=>'operation'
		];
		$lst['p_newbuy']=[
				'fmt'=>['平台新增购买情况','新增购买：{BuyNUsrNew}人(其中新注册用户{BuyNUsrNew0},占{reg2buy}%)'],
				'extraRate'=>['{reg2buy}'=>['{BuyNUsrNew0}','{BuyNUsrNew}']],
				'rights'=>'operation',
		];
		$lst['p_bindinfo']=[
				'fmt'=>['平台绑卡情况','{BindOk}人绑卡成功（其中新注册{RegAndBindOk}人）;<br>'
						.'{BindFailed}人绑卡失败；（其中新注册绑{RegAndBindFailed}人）'
				],//成功绑卡{BindOk}人，失败todo人
				'extraRate'=>[],
		];
		$lst['p_buyinfo']=[
				'fmt'=>['平台购买情况','总计{BuyNUsrDay}人(增长{BuyNUsrDay_UP}%，首投用户数占{newBuyerRate}%）<br>'
						. '购买{BuyNAmountDay}元(增长{BuyNAmountDay_UP}%，其中天天赚占{tiantianRate}%)'],
				'extraRate'=>['{newBuyerRate}'=>['{BuyNUsrNew}','{BuyNUsrDay}'],'{tiantianRate}'=>['{BuyTianTianAmountDay}','{BuyNAmountDay}']],
		];
//				'p_rebuyinfo'=>[
//		'fmt'=>['平台复投情况','todo'],
//		'extraRate'=>[],
//	],
		$lst['p_summary']=[
				'fmt'=>['平台现况总计','注册{Accounts}人，投资客{BuyNUsrAll}人'],
				'extraRate'=>[],
		];
	}else{
		error_log('no rights for option');
	}
	if($acl->hasFinance()){
		$lst['p_remaininfo']=[
				'fmt'=>['平台存量情况','平台存量：{Stock}元（增量：{StockChange}元），其中：<br />'
						.'（充值金额：{ChargeAmount}元,提现到银行卡：{WithdrawAmount}元'
						.'今日申请提现：{ApplyWithdrawAmount}元）'],
				'extraRate'=>[],
		];
//			//先关闭掉资金流向
//			$lst['p_yebfundflows']=[
//					'fmt'=>['天天赚资金流向情况','转出总额：{YebFundFlowsTotalAmount}元（提现{YebFundFlowsWithdraw}元，抵押标{YebFundFlowsDyb}元，转回天天赚{YebFundFlowsYebbuy}元，存钱罐{YebFundFlowsAccount}元）'],
//					'extraRate'=>[],
//			];
//
//			$lst['p_dybfundflows']=[
//					'fmt'=>['标的还款资金流向','提现{BidFundFlowsWithdraw}元，抵押标{BidFundFlowsDyb}元，转回天天赚{BidFundFlowsYebBuy}元，存钱罐{BidFundFlowsAccount}元'
//
//					],
//			];

//            $lst['f_summary'] = [
//                    'fmt'=>['财务情况','增量{NewFinance}元，（=收入{iFinance}元，支出{pFinance}元）'],
//            ];
//
//            $lst['f_xxsum'] = [
//                    'fmt'=>['线下业务','存量{FinanceXX}元（增量{NewFinanceXX}元 = 收入{iFinanceXX}元 - 支出{pFinanceXX}元）'],
//            ];
		/*
        $lst['f_summary']=[
                'fmt'=>['财务存量情况','快快金融平台存量是{FinanceK}元,<br>美豫平台存量是{FinanceM}元,<br>线上投标存量是{FinanceXS}元,<br>线下投标存量是{FinanceXX}元,<br>4个数据汇总后存量是{Finance}元'],
                'extraRate'=>[],
            ];
        $lst['f_income']=[
                'fmt'=>['财务收入情况','快快金融平台收入是{iFinanceK}元,<br>美豫平台收入是{iFinanceM}元,<br>线上投标收入是{iFinanceXS}元,<br>线下投标收入是{iFinanceXX}元,<br>4个数据汇总后收入是{iFinance}元']
        ];
        $lst['f_payment']=[
            'fmt'=>['财务支出情况','快快金融平台支出是{pFinanceK}元,<br>美豫平台支出是{pFinanceM}元,<br>线上投标支出是{pFinanceXS}元,<br>线下投标支出是{pFinanceXX}元,<br>4个数据汇总后支出是{pFinance}元']
        ];
        $lst['f_xxsum']=[
                'fmt'=>['线下业务存量情况','投资人本金存量{xxsum103}元,<br>借款人还借款本金存量{xxsum104}元,<br>借款人服务费存量{xxsum105}元,<br>借款人保证金存量{xxsum106}元,<br>借款人贷款利息存量{xxsum107}元,<br>中介费存量{xxsum108}元,<br>其它收入存量{xxsum109}元,<br>借款人贷款金额存量{xxsum110}元,<br>支付投资人理财利息存量{xxsum111}元,<br>退还投资人本金存量{xxsum112}元,<br>退还借款人保证金存量{xxsum113}元,<br>中介返佣存量{xxsum114}元,<br>其它支出存量{xxsum115}元']
        ];
        */

	}else{
		error_log('no rights for hasFinance');
	}
	if($acl->hasBusiness()){
		// TODO:
		$lst['p_prdtinfo']=[
				'fmt'=>['平台标的情况','有效标的{PrdtNumDay}个,其中：<br>新标的{PrdtNumNew}个，{PrdtAmountNew}元'],
				'extraRate'=>[],
		];
	}else{
		error_log('no rights for hasBusiness');
	}

	$this->_view->assign('rptGroupDefine',$lst);

	$nameMap = prjlib_evtdaily::AllEvt();
	$db = $this->getDB();
//		$n = \Rpt\EvtDaily\BuyUsrAll::getCopy('NewRegister')->numOfAct($db, 20160131);
//		var_log($n,'########################NewReg#####');
//		var_log(\Sooh\DB\Broker::lastCmd(false));
	$rs = array();
	$dt = time();
	$prdtbuy = [];
	//$tmp = \Rpt\EvtDaily\BuyAmountDay::getCopy('BuyAmountDay')->numOfAct($db, 20160122);
//		var_log($tmp,'#############################');
//		var_log(\Sooh\DB\Broker::lastCmd(false));
	for($i=0;$i<8;$i++){
		$ymd = date('Ymd',$dt);
		$tomorrow = date('Ymd',$dt+86400);

		/**
		 * 新标旧标数据统计
		 */
		$prdtbuy[$ymd]['newPrdtSuperBuyAmountThisDay'] = number_format(prjlib_evtdaily::getCopy('NewPrdtSuperBuyAmountThisDay')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['newPrdtCommonBuyAmountThisDay'] = number_format(prjlib_evtdaily::getCopy('NewPrdtCommonBuyAmountThisDay')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['newPrdtVoucherUseAmountThisDay'] = number_format(prjlib_evtdaily::getCopy('NewPrdtVoucherUseAmountThisDay')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['newPrdtAmountLeft'] = number_format(prjlib_evtdaily::getCopy('newPrdtAmountLeft')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['prdtAmountNew'] = number_format(prjlib_evtdaily::getCopy('PrdtAmountNew')->numOfAct($db, $ymd, null), 2);

		$prdtbuy[$ymd]['oldPrdtSuperBuyAmountBefore'] = number_format(prjlib_evtdaily::getCopy('OldPrdtSuperBuyAmountBefore')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['oldPrdtCommonBuyAmountBefore'] = number_format(prjlib_evtdaily::getCopy('OldPrdtCommonBuyAmountBefore')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['oldPrdtVoucherUseAmountBefore'] = number_format(prjlib_evtdaily::getCopy('OldPrdtVoucherUseAmountBefore')->numOfAct($db, $ymd, null), 2);

		$prdtbuy[$ymd]['oldPrdtSuperBuyAmountThisDay'] = number_format(prjlib_evtdaily::getCopy('OldPrdtSuperBuyAmountThisDay')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['oldPrdtCommonBuyAmountThisDay'] = number_format(prjlib_evtdaily::getCopy('OldPrdtCommonBuyAmountThisDay')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['oldPrdtVoucherUseAmountThisDay'] = number_format(prjlib_evtdaily::getCopy('OldPrdtVoucherUseAmountThisDay')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['oldPrdtAmountLeft'] =  number_format(prjlib_evtdaily::getCopy('OldPrdtAmountLeft')->numOfAct($db, $ymd, null), 2);
		$prdtbuy[$ymd]['prdtAmountOlder'] = number_format(prjlib_evtdaily::getCopy('PrdtAmountOlder')->numOfAct($db, $ymd, null), 2);

		//财务********************
		$rs[$ymd]['{NewFinance}'] = prjlib_evtdaily::getCopy('NewFinance')->numOfAct($db, $ymd,null,['flgext01'=>'0']);
		$rs[$ymd]['{NewFinanceXX}'] = prjlib_evtdaily::getCopy('NewFinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>'0']);

		$this->fillRs($rs, 'FinanceK', $ymd, $tomorrow, $db,['flgext01'=>'102']);
		$rs[$ymd]['{iFinanceK}'] = prjlib_evtdaily::getCopy('FinanceK')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
		$rs[$ymd]['{pFinanceK}'] = prjlib_evtdaily::getCopy('FinanceK')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
		$this->fillRs($rs, 'FinanceM', $ymd, $tomorrow, $db,['flgext01'=>'102']);
		$rs[$ymd]['{iFinanceM}'] = prjlib_evtdaily::getCopy('FinanceM')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
		$rs[$ymd]['{pFinanceM}'] = prjlib_evtdaily::getCopy('FinanceM')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
		$this->fillRs($rs, 'FinanceXS', $ymd, $tomorrow, $db,['flgext01'=>'102']);
		$rs[$ymd]['{iFinanceXS}'] = prjlib_evtdaily::getCopy('FinanceXS')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
		$rs[$ymd]['{pFinanceXS}'] = prjlib_evtdaily::getCopy('FinanceXS')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
		$this->fillRs($rs, 'FinanceXX', $ymd, $tomorrow, $db,['flgext01'=>['103','104','105','106','107','108','109','110','111','112','113','114','115']]);
		$rs[$ymd]['{iFinanceXX}'] = prjlib_evtdaily::getCopy('FinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
		$rs[$ymd]['{pFinanceXX}'] = prjlib_evtdaily::getCopy('FinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
		$this->fillRs($rs, 'Finance', $ymd, $tomorrow, $db,['flgext01'=>['102','103','104','105','106','107','108','109','110','111','112','113','114','115']]);
		$rs[$ymd]['{iFinance}'] = prjlib_evtdaily::getCopy('Finance')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
		$rs[$ymd]['{pFinance}'] = prjlib_evtdaily::getCopy('Finance')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
		for($j=103;$j<=115;$j++){
			$rs[$ymd]['{xxsum'.$j.'}'] = prjlib_evtdaily::getCopy('FinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>$j]);
		}
		//***********************

//            $this->fillRs($rs, 'FinanceK', $ymd, $tomorrow, $db);
//            $this->fillRs($rs, 'FinanceM', $ymd, $tomorrow, $db);
//			$this->fillRs($rs, 'FinanceXS', $ymd, $tomorrow, $db);
//			$this->fillRs($rs, 'FinanceXS', $ymd, $tomorrow, $db);
		//过滤掉系统兜底账户
		$this->fillRs($rs, 'Accounts', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'NewRegister', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'RegAndBindFailed', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'RegAndBindOk', $ymd, $tomorrow, $db);

		$this->fillRs($rs, 'BuyNAmountAll', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'BuyNUsrAll', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyAmountDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyAmountNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyAmountNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyUsrDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyUsrNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyUsrNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);

		$this->fillRs($rs, 'BuyNAmountDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'BuyNAmountNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'BuyNAmountNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'BuyNUsrDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'BuyNUsrNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
		$this->fillRs($rs, 'BuyNUsrNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);

		$this->fillRs($rs, 'BindOk', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'BindFailed', $ymd, $tomorrow, $db);

		$this->fillRs($rs, 'Stock', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'StockChange', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'ChargeAmount', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'WithdrawAmount', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'ApplyWithdrawAmount', $ymd, $tomorrow, $db);

		$this->fillRs($rs, 'YebFundFlowsTotalAmount', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'YebFundFlowsWithdraw', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'YebFundFlowsDyb', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'YebFundFlowsYebbuy', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'YebFundFlowsAccount', $ymd, $tomorrow, $db);

		$this->fillRs($rs, 'BidFundFlowsWithdraw', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'BidFundFlowsDyb', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'BidFundFlowsYebBuy', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'BidFundFlowsAccount', $ymd, $tomorrow, $db);

		$this->fillRs($rs, 'PrdtNumNew', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'PrdtNumDay', $ymd, $tomorrow, $db);
		$this->fillRs($rs, 'PrdtAmountNew', $ymd, $tomorrow, $db);
		$rs[$ymd]['{BuyTianTianAmountDay}'] = prjlib_evtdaily::getCopy('BuyPAmountDay')->numOfAct($db, $ymd,null,['flgext01'=>0]);


//			$rs[$ymd]['Stock'] = prjlib_evtdaily::getCopy('Stock')->numOfAct($db, $ymd);
//
//
//			$rs[$ymd]['NewChargeNum'] = prjlib_evtdaily::getCopy('NewChargeNum')->numOfAct($db, $ymd);
//			$rs[$ymd]['NewBought'] = prjlib_evtdaily::getCopy('NewBought')->numOfAct($db, $ymd);
//			$rs[$ymd]['BoughtTotal'] = prjlib_evtdaily::getCopy('BoughtTotal')->numOfAct($db, $ymd);
//
//			$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd);
//			$rs[$ymd]['BindFailed']=prjlib_evtdaily::getCopy('BindFailed')->numOfAct($db, $ymd);
//			$rs[$ymd]['BindRequest']=prjlib_evtdaily::getCopy('BindRequest')->numOfAct($db, $ymd);
//
//			$rs[$ymd]['BoughtAmount'] = prjlib_evtdaily::getCopy('BoughtAmount')->numOfAct($db, $ymd);
//			$rs[$ymd]['StockChange'] = prjlib_evtdaily::getCopy('StockChange')->numOfAct($db, $ymd);
//			$rs[$ymd]['ChargeAmount'] = prjlib_evtdaily::getCopy('ChargeAmount')->numOfAct($db, $ymd);
//			$rs[$ymd]['WithdrawAmount'] = prjlib_evtdaily::getCopy('WithdrawAmount')->numOfAct($db, $ymd);
		//$rs[$ymd]['BoughtFeeTotal']=prjlib_evtdaily::getCopy('BoughtFeeTotal')->numOfAct($db, $ymd,null);
		//$rs[$ymd]['WithdrawFee']=prjlib_evtdaily::getCopy('WithdrawFee')->numOfAct($db, $ymd,null);

		//$rs[$ymd]['Wallet2BankCard']=prjlib_evtdaily::getCopy('Wallet2BankCard')->numOfAct($db, $ymd,null) / 100;
		//$rs[$ymd]['WalletRecharge']=prjlib_evtdaily::getCopy('WalletRecharge')->numOfAct($db, $ymd,null) / 100;
		//$rs[$ymd]['Wallet2JBY']=prjlib_evtdaily::getCopy('Wallet2JBY')->numOfAct($db, $ymd,null) / 100;
		//$rs[$ymd]['WalletFromJBY']=prjlib_evtdaily::getCopy('WalletFromJBY')->numOfAct($db, $ymd,null) / 100 ;

		//$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd,null);
		//$rs[$ymd]['Accessed']=prjlib_evtdaily::getCopy('Accessed')->numOfAct($db, $ymd,null);
		//$rs[$ymd]['LoginPc']=prjlib_evtdaily::getCopy('Login')->numOfAct($db, $ymd,null, array('clienttype'=>900));
		//$rs[$ymd]['LoginMobile']=prjlib_evtdaily::getCopy('Login')->numOfAct($db, $ymd,null, array('clienttype!'=>900));

		$maxOrMin[$ymd]['MaxNewRegCopartner'] = prjlib_evtdaily::getCopy('MaxNewRegCopartner')->maxNumGrpBy($db, $ymd);
		$maxOrMin[$ymd]['MinNewRegCopartner'] = prjlib_evtdaily::getCopy('MinNewRegCopartner')->maxNumGrpBy($db, $ymd);
		$dt-=86400;
	}
	unset($rs[$ymd]);
	$this->_view->assign('records',$rs);
//var_log($rs, 'rs>>>>>');
	$this->_view->assign('maxOrMin', $maxOrMin);
	$this->_view->assign('actName', $nameMap);
	$this->_view->assign('weekdayName', $this->weekdays);
	$this->_view->assign('prdtbuy', $prdtbuy);
//var_log($prdtbuy, '标的购买信息');
	$this->_view->assign('copartners', \Prj\Misc\Funcs::copartnerIdWithcopartnerName());
}

	public function detailAction () {
	    $pageId = $this->_request->get('pageId',1)-0;
	    $pageSize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
	    $isDownloadExcel = $this->_request->get('__EXCEL__');
	    $pager = new \Sooh\DB\Pager($pageSize,$this->pageSizeEnum,false);
	    
		$nameMap = rpt_evtdaily::AllEvt();
		$actname = $this->_request->get('_pkey_val');
		$dt = time();
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
			->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem ('_ymd_g2', form_def::factory('起始日期', date('Ymd', $dt-6*86400), form_def::date))
			->addItem('_ymd_l2', form_def::factory('起始日期', date('Ymd', $dt), form_def::date))
			->addItem('_act_eq', form_def::factory('', $actname, form_def::hidden));
		
		$frm->fillValues();
// var_log($actname, 'actname>>>>>>>>>');		
		$where = $frm->getWhere();
		if ($isDownloadExcel == 1) {
		    $where = $this->_request->get('where');
		    $where['ymd]'] = $where['ymdFrom'];
		    $where['ymd['] = $where['ymdTo'];
		    $where['act='] = $where['act'];
		    unset($where['ymdFrom']);
		    unset($where['ymdTo']);
		    unset($where['act']);
		}
		if (empty($actname)) {
		    $actname = $where['act='];
		}
		$db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2prpt);
		
		$act = '\\Rpt\\EvtDaily\\'.$where['act='];
		$actObj = new $act();		
		if ($actObj->formula() != null) {
		    $ymdFrom = $where['ymd]'];
		    $ymdTo = $where['ymd['];
		    $rs = array();
		    while ($ymdTo >= $ymdFrom){
		        $n = $db->getRecordCount(\Rpt\Tbname::tb_evtdaily,array('ymd'=>$ymdTo,'act'=>$actObj->formula()));
// var_log(\Sooh\DB\Broker::lastCmd(),'lastCmd>>>>>>>>>>>');		        
// var_log($n, $ymdTo.'>>>>>>>>>>>>>>>>>');		        
		        if ($n<1) {
		            $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
		            continue;
		        }
		        $rs[] = array('ymd'=>$ymdTo,'n'=>$actObj->numOfAct($db,$ymdTo,null)/$actObj->divisor());
		        
		        $ymdTo = date('Ymd', strtotime($ymdTo)-86400);
		    }
		}else {
		  $rs = $db->getRecords(\Rpt\Tbname::tb_evtdaily, 'ymd, sum(n)/100 as n',$where, 'groupby ymd rsort ymd');
		}
        $headers = array('日期'=>40, $nameMap[$actname]=>70);
        if ($isDownloadExcel==1){
            return $this->downExcel($rs,array_keys($headers),null,false);
        }
// 		error_log(\Sooh\DB\Broker::lastCmd());
		$this->_view->assign('records', $rs);
		$this->_view->assign('headers', $headers);
		$where['ymdFrom'] = $where['ymd]'];
		$where['ymdTo'] = $where['ymd['];
		$where['act'] = $where['act='];
		unset($where['ymd]']);
		unset($where['ymd[']);
		unset($where['act=']);
		$this->_view->assign('where', $where);
	}

	public function init()
	{
		parent::init();
		define('SOOH_ACL_CLASS', '\PrjLib\Acl\JYM\sess');
//		$form = \Sooh\Base\Form\Broker::getCopy($id);
//		$form->items=array(
//			'username'=>\Sooh\Base\Form\Item::factory('用户', '', \Sooh\Base\Form\Item::text),
//			'password'=>\Sooh\Base\Form\Item::factory('密码', '', \Sooh\Base\Form\Item::passwd),
//		);
//		$form->fillValues(array_merge($this->_request->getQuery(),$this->_request->getPost()));
		$this->_view->assign('actName',prjlib_evtdaily::AllEvt());
		$this->_view->assign('weekdayName',  $this->weekdays);
	}
	
	
	public function weekforAction()
	{
		if($this->checkLogin()==false){
			$this->_view->assign('showLogin',1);
			return;
		} else
			$this->_view->assign('showLogin', 0);
		$db = $this->getDB();
		$chart = new \Rpt\Misc\Chart1;
		$chart->parseInput($this->_request->getQuery());
		$grpBy = $chart->grpBy;
		
		$ymds=[];
		$dt = strtotime($chart->ymd);
		$nDays=6;
		for($i=$nDays;$i>=0;$i--){
			$n = $dt-86400*$i;
			$ymds[]= date('Ymd',$n);
		}
		$header=array();
		$records=array();
		$actList = $chart->where['act'];
		unset($chart->where['act']);
		$pos=0;
		if(!empty($chart->grpBy)){
			$funcForNameOfGrp='\\Rpt\\Misc\\NameOfGrpInChart::'.$chart->grpBy;
		}
		foreach($ymds as $pos=>$ymd){
			$ymdDesc = floor(($ymd%10000)/100).'-'.($ymd%100).'('.$this->weekdays[date('w',  strtotime($ymd))].')';
			$header[]=$ymdDesc;
			foreach($actList as $actId){
				$actDesc = $this->nameOfAct($actId);
				if(!empty($chart->grpBy)){
					$tmp = prjlib_evtdaily::getCopy($actId)->numOfAct($db, $ymd,$chart->grpBy,$chart->where);
					foreach($tmp as $k=>$n){
						$records[$actDesc.'('.call_user_func($funcForNameOfGrp,$k,$actId).')'][$pos]=$n;
					}
				}else{
					$records[$actDesc][$pos] = prjlib_evtdaily::getCopy($actId)->numOfAct($db, $ymd,null,$chart->where);
				}
			}
			$pos++;
		}
		if(!empty($chart->grpBy)){
			foreach($records as $s=>$r){
				for($pos=0;$pos<=$nDays;$pos++){
					if(!isset($r[$pos])){
						$records[$s][$pos]=0;
					}
					ksort($records[$s]);
				}
			}
		}
//var_log($header);
//array (  0 => '12-29(一)',  1 => '12-30(二)',  2 => '12-31(三)',  3 => '1-1(四)',  4 => '1-2(五)',  5 => '1-3(六)',  6 => '1-4(日)',)

//var_log($records);
//array (
//  '当日申请绑卡总人数' =>  array (0 => 0,    1 => 0,    2 => 0,    3 => 44,    4 => 88,    5 => 44,    6 => 88,  ),
//  '当日成功绑卡人数' =>   array (    0 => 0,    1 => 0,    2 => 0,    3 => 40,    4 => 80,    5 => 40,    6 => 80,  ),
//  '当日绑卡失败的人数' =>   array (    0 => 0,    1 => 0,    2 => 0,    3 => 4,    4 => 8,    5 => 4,    6 => 8,  ),
//)
		$this->_view->assign('rptData', \Rpt\Misc\Echarts::format($chart->titleMain, 
			$header, 	$records,
			$chart->titleSub
			)
		);
	}
	protected function nameOfAct($actId)
	{
		return call_user_func('\\Rpt\\EvtDaily\\'.$actId.'::displayName');
	}
	public function onedayforAction()
	{
		if($this->checkLogin()==false){
			$this->_view->assign('showLogin',1);
			return;
		}else $this->_view->assign('showLogin',0);	
		
		$db = $this->getDB();
		$chart = new \Rpt\Misc\Chart1;
		$chart->parseInput($this->_request->getQuery());
		
		$actList=  $chart->where['act'];
		unset($chart->where['act']);
		$records=array();
		if(!empty($chart->grpBy)){
			$funcForNameOfGrp='\\Rpt\\Misc\\NameOfGrpInChart::'.$chart->grpBy;
		}
		foreach($actList as $actId){
			
			$actIntro =$this->nameOfAct($actId);
			if(empty($chart->grpBy)){
				$records[$actIntro]=prjlib_evtdaily::getCopy($actId)->numOfAct($db, $chart->ymd, null,$chart->where);
			}else{
				$tmp = prjlib_evtdaily::getCopy($actId)->numOfAct($db, $chart->ymd, $chart->grpBy,$chart->where);
				if(sizeof($actList)==1){
					foreach($tmp as $k=>$n){
						$records[call_user_func($funcForNameOfGrp,$k,$actId)]=$n;
					}
				}else{
					foreach($tmp as $k=>$n){
						$records[$actIntro.'('.call_user_func($funcForNameOfGrp,$k,$actId).')']=$n;
					}
				}
			}
		}
		$header = array_keys($records);
		$tmp = array_values($records);
		$records=['data'=>$tmp];
//var_log($header);
//array (  0 => '当日成功绑卡人数(IOS)',  1 => '当日成功绑卡人数(安卓)',  2 => '当日绑卡失败的人数(IOS)',  3 => '当日绑卡失败的人数(安卓)',)
//var_log($records);
//array (  'data' =>   array (    0 => '40',    1 => '40',    2 => '4',    3 => '5',  ),)
		
		$this->_view->assign('rptData',  \Rpt\Misc\Echarts::format($chart->titleMain, 
			$header, $records,
			$chart->titleSub
			)
		);

	}

	public function recent2Action()
	{
		$acl = \Prj\Acl\RptLimit::initWho($this->manager);
		$lst2 = [];
		$lst = [];
		if($acl->hasOperation()){
			$lst['p_newreg']=[
					'fmt'=>['平台新增注册情况','新增注册：{NewRegister}人(增长{NewRegister_UP}%)<br>其中{RegAndBindOk}人绑卡成功，{RegAndBindFailed}人绑卡失败。其中有{BuyNUsrNew0}人当日购买'],
					'extraRate'=>['{reg2bind}'=>['{BindNew0}','{NewRegister}'],'{reg2buy}'=>['{BuyNUsrNew0}','{NewRegister}']],
					'rights'=>'operation'
			];
			$lst['p_newbuy']=[
					'fmt'=>['平台新增购买情况','新增购买：{BuyNUsrNew}人(其中新注册用户{BuyNUsrNew0},占{reg2buy}%)'],
					'extraRate'=>['{reg2buy}'=>['{BuyNUsrNew0}','{BuyNUsrNew}']],
					'rights'=>'operation',
			];
			$lst['p_bindinfo']=[
					'fmt'=>['平台绑卡情况','{BindOk}人绑卡成功（其中新注册{RegAndBindOk}人）;<br>'
							.'{BindFailed}人绑卡失败；（其中新注册绑{RegAndBindFailed}人）'
					],//成功绑卡{BindOk}人，失败todo人
					'extraRate'=>[],
			];
			$lst['p_buyinfo']=[
					'fmt'=>['平台购买情况','总计{BuyNUsrDay}人(增长{BuyNUsrDay_UP}%，首投用户数占{newBuyerRate}%）<br>'
							. '购买{BuyNAmountDay}元(增长{BuyNAmountDay_UP}%，其中天天赚占{tiantianRate}%)'],
					'extraRate'=>['{newBuyerRate}'=>['{BuyNUsrNew}','{BuyNUsrDay}'],'{tiantianRate}'=>['{BuyTianTianAmountDay}','{BuyNAmountDay}']],
			];
//				'p_rebuyinfo'=>[
//		'fmt'=>['平台复投情况','todo'],
//		'extraRate'=>[],
//	],
			$lst['p_summary']=[
					'fmt'=>['平台现况总计','注册{Accounts}人，投资客{BuyNUsrAll}人'],
					'extraRate'=>[],
			];
		}else{
			error_log('no rights for option');
		}
		if($acl->hasFinance()){
			$lst['p_remaininfo']=[
					'fmt'=>['平台存量情况','平台存量：{Stock}元（增量：{StockChange}元），其中：<br />'
							.'（充值金额：{ChargeAmount}元,提现到银行卡：{WithdrawAmount}元'
							.'今日申请提现：{ApplyWithdrawAmount}元）'],
					'extraRate'=>[],
			];
//			//先关闭掉资金流向
//			$lst['p_yebfundflows']=[
//					'fmt'=>['天天赚资金流向情况','转出总额：{YebFundFlowsTotalAmount}元（提现{YebFundFlowsWithdraw}元，抵押标{YebFundFlowsDyb}元，转回天天赚{YebFundFlowsYebbuy}元，存钱罐{YebFundFlowsAccount}元）'],
//					'extraRate'=>[],
//			];
//
//			$lst['p_dybfundflows']=[
//					'fmt'=>['标的还款资金流向','提现{BidFundFlowsWithdraw}元，抵押标{BidFundFlowsDyb}元，转回天天赚{BidFundFlowsYebBuy}元，存钱罐{BidFundFlowsAccount}元'
//
//					],
//			];

//            $lst['f_summary'] = [
//                    'fmt'=>['财务情况','增量{NewFinance}元，（=收入{iFinance}元，支出{pFinance}元）'],
//            ];
//
//            $lst['f_xxsum'] = [
//                    'fmt'=>['线下业务','存量{FinanceXX}元（增量{NewFinanceXX}元 = 收入{iFinanceXX}元 - 支出{pFinanceXX}元）'],
//            ];
			/*
			$lst['f_summary']=[
					'fmt'=>['财务存量情况','快快金融平台存量是{FinanceK}元,<br>美豫平台存量是{FinanceM}元,<br>线上投标存量是{FinanceXS}元,<br>线下投标存量是{FinanceXX}元,<br>4个数据汇总后存量是{Finance}元'],
					'extraRate'=>[],
				];
            $lst['f_income']=[
                    'fmt'=>['财务收入情况','快快金融平台收入是{iFinanceK}元,<br>美豫平台收入是{iFinanceM}元,<br>线上投标收入是{iFinanceXS}元,<br>线下投标收入是{iFinanceXX}元,<br>4个数据汇总后收入是{iFinance}元']
            ];
            $lst['f_payment']=[
                'fmt'=>['财务支出情况','快快金融平台支出是{pFinanceK}元,<br>美豫平台支出是{pFinanceM}元,<br>线上投标支出是{pFinanceXS}元,<br>线下投标支出是{pFinanceXX}元,<br>4个数据汇总后支出是{pFinance}元']
            ];
            $lst['f_xxsum']=[
                    'fmt'=>['线下业务存量情况','投资人本金存量{xxsum103}元,<br>借款人还借款本金存量{xxsum104}元,<br>借款人服务费存量{xxsum105}元,<br>借款人保证金存量{xxsum106}元,<br>借款人贷款利息存量{xxsum107}元,<br>中介费存量{xxsum108}元,<br>其它收入存量{xxsum109}元,<br>借款人贷款金额存量{xxsum110}元,<br>支付投资人理财利息存量{xxsum111}元,<br>退还投资人本金存量{xxsum112}元,<br>退还借款人保证金存量{xxsum113}元,<br>中介返佣存量{xxsum114}元,<br>其它支出存量{xxsum115}元']
            ];
            */

		}else{
			error_log('no rights for hasFinance');
		}
		if($acl->hasBusiness()){
//			TODO:
			$lst['p_prdtinfo']=[
					'fmt'=>['平台标的情况','有效标的{PrdtNumDay}个,其中：<br>新标的{PrdtNumNew}个，{PrdtAmountNew}元'],
					'extraRate'=>[],
			];
		}else{
			error_log('no rights for hasBusiness');
		}


		// 改版用的
		if($acl->hasOperation()){
			$lst2['p_newreg']=[
					'注册数'=>'{NewRegister}',
			];
			$lst2['p_realnameauth'] =['实名认证数'=>'{RealnameAuth}'];
			$lst2['p_newregbindok'] = ['当日注册新增绑卡数'=>'{RegAndBindOk}'];
			$lst2['p_newregbuy'] = ['当日注册新增理财人数'=>'{BuyNUsrNew0}'];
			$lst2['p_newbindok'] = ['当日新增绑卡数目'=>'{BindOk}'];
			$lst2['p_newbuy'] = ['当日新增理财人数'=>'{BuyNUsrNew}'];

			$lst2['p_summary']=[
				'平台现况统计'=> [
						'总注册人数'=>'{Accounts}',
						'投资客人数'=>'{BuyNUsrAll}',
				],
			];

			$lst2['p_bidbuyamount'] = [
				'抵押标理财总额'=>[
					'内部员工'=>'{BidBoughtAmountEmployee}',
					'员工推荐'=>'{BidBoughtAmountInviter}',
					'外部用户'=>'{BidBoughtAmountExternal}'
				]
			];

			$lst2['p_bidbuyuser'] = [
				'抵押标理财人数'=>[
					'内部员工'=>'{BidBoughtUserNumEmployee}',
					'员工推荐'=>'{BidBoughtUserNumInviter}',
					'外部用户'=>'{BidBoughtUserNumExternal}'
				]
			];

			$lst2['p_yuebaobuyamount'] = [
				'天天赚理财金额'=>[
						'内部员工'=>'{YebBoughtAmountEmployee}',
						'员工推荐'=>'{YebBoughtAmountInviter}',
						'外部用户'=>'{YebBoughtAmountExternal}'
				]
			];

			$lst2['p_yuebaobuyuser'] = [
				'天天赚理财人数'=>[
						'内部员工'=>'{YebBoughtUserNumEmployee}',
						'员工推荐'=>'{YebBoughtUserNumInviter}',
						'外部用户'=>'{YebBoughtUserNumExternal}'
				]
			];

			$lst2['p_yuebaooutamount'] = [
				'天天赚转出总额'=>[
						'内部员工'=>'{YebOutAmountEmployee}',
						'员工推荐'=>'{YebOutAmountInviter}',
						'外部用户'=>'{YebOutAmountExternal}'
				]
			];
			$lst2['p_yuebaooutuser'] = [
				'天天赚转出人数'=>[
						'内部员工'=>'{YebOutUserNumEmployee}',
						'员工推荐'=>'{YebOutUserNumInviter}',
						'外部用户'=>'{YebOutUserNumExternal}'
				]
			];

			$lst2['p_yuebaoincrease'] = [
				'天天赚净增总额'=>'{YebAmountIncrease}'
			];

			$lst2['p_yebfundflows'] = [
				'天天赚转出资金流向'=>[
					'提现'=>'{YebFundFlowsWithdraw}',
					'抵押标'=>'{YebFundFlowsDyb}',
					'转回天天赚'=>'{YebFundFlowsYebbuy}',
					'存钱罐'=>'{YebFundFlowsAccount}',
				]
			];

			$lst2['p_dybfundflows'] = [
				'标的还款资金流向'=>[
					'提现'=>'{BidFundFlowsWithdraw}',
					'抵押标'=>'{BidFundFlowsDyb}',
					'转回天天赚'=>'{BidFundFlowsYebBuy}',
					'存钱罐'=>'{BidFundFlowsAccount}',
				]
			];

		}else {
			error_log('no rights for hasFinance');
		}

		if($acl->hasFinance()){
			$lst2['p_remaininfo'] = [
					'平台存量情况'=>[
					'平台存量'=>'{Stock}元',
					'当日增量'=>'{StockChange}元',
					'当日充值'=>'{ChargeAmount}元',
					'当日提现到银行卡'=>'{WithdrawAmount}元',
					'当日申请提现'=>'{ApplyWithdrawAmount}元',
				]
			];
		}

		if($acl->hasBusiness()){
//			$lst['p_prdtinfo']=[
//					'fmt'=>['平台标的情况','有效标的{PrdtNumDay}个,其中：<br>新标的{PrdtNumNew}个，{PrdtAmountNew}元'],
//					'extraRate'=>[],
//			];
// TODO:

			$lst2['p_prdtinfo'] = [
				'标的'=>[
					'有效标的数'=>'{PrdtNumDay}个',
					'新标的数'=>'{PrdtNumNew}个',
					'新标的总金额'=>'{PrdtAmountNew}元',
				]
			];
		}else {
			error_log('no rights for hasBusiness');
		}


		$this->_view->assign('rptGroupDefine',$lst);
		$this->_view->assign('rptGroupDefine2',$lst2);
		$nameMap = prjlib_evtdaily::AllEvt();
		$db = $this->getDB();
//		$n = \Rpt\EvtDaily\BuyUsrAll::getCopy('NewRegister')->numOfAct($db, 20160131);
//		var_log($n,'########################NewReg#####');
//		var_log(\Sooh\DB\Broker::lastCmd(false));
		$rs = array();
		$dt = time();
		//$tmp = \Rpt\EvtDaily\BuyAmountDay::getCopy('BuyAmountDay')->numOfAct($db, 20160122);
//		var_log($tmp,'#############################');
//		var_log(\Sooh\DB\Broker::lastCmd(false));
		for($i=0;$i<10;$i++){
			$ymd = date('Ymd',$dt);
			$tomorrow = date('Ymd',$dt+86400);

			//财务********************
			$rs[$ymd]['{NewFinance}'] = prjlib_evtdaily::getCopy('NewFinance')->numOfAct($db, $ymd,null,['flgext01'=>'0']);
			$rs[$ymd]['{NewFinanceXX}'] = prjlib_evtdaily::getCopy('NewFinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>'0']);

			$this->fillRs($rs, 'FinanceK', $ymd, $tomorrow, $db,['flgext01'=>'102']);
			$rs[$ymd]['{iFinanceK}'] = prjlib_evtdaily::getCopy('FinanceK')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
			$rs[$ymd]['{pFinanceK}'] = prjlib_evtdaily::getCopy('FinanceK')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
			$this->fillRs($rs, 'FinanceM', $ymd, $tomorrow, $db,['flgext01'=>'102']);
			$rs[$ymd]['{iFinanceM}'] = prjlib_evtdaily::getCopy('FinanceM')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
			$rs[$ymd]['{pFinanceM}'] = prjlib_evtdaily::getCopy('FinanceM')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
			$this->fillRs($rs, 'FinanceXS', $ymd, $tomorrow, $db,['flgext01'=>'102']);
			$rs[$ymd]['{iFinanceXS}'] = prjlib_evtdaily::getCopy('FinanceXS')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
			$rs[$ymd]['{pFinanceXS}'] = prjlib_evtdaily::getCopy('FinanceXS')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
			$this->fillRs($rs, 'FinanceXX', $ymd, $tomorrow, $db,['flgext01'=>['103','104','105','106','107','108','109','110','111','112','113','114','115']]);
			$rs[$ymd]['{iFinanceXX}'] = prjlib_evtdaily::getCopy('FinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
			$rs[$ymd]['{pFinanceXX}'] = prjlib_evtdaily::getCopy('FinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
			$this->fillRs($rs, 'Finance', $ymd, $tomorrow, $db,['flgext01'=>['102','103','104','105','106','107','108','109','110','111','112','113','114','115']]);
			$rs[$ymd]['{iFinance}'] = prjlib_evtdaily::getCopy('Finance')->numOfAct($db, $ymd,null,['flgext01'=>'100']);
			$rs[$ymd]['{pFinance}'] = prjlib_evtdaily::getCopy('Finance')->numOfAct($db, $ymd,null,['flgext01'=>'101'])*-1;
			for($j=103;$j<=115;$j++){
				$rs[$ymd]['{xxsum'.$j.'}'] = prjlib_evtdaily::getCopy('FinanceXX')->numOfAct($db, $ymd,null,['flgext01'=>$j]);
			}
			//***********************

//            $this->fillRs($rs, 'FinanceK', $ymd, $tomorrow, $db);
//            $this->fillRs($rs, 'FinanceM', $ymd, $tomorrow, $db);
//			$this->fillRs($rs, 'FinanceXS', $ymd, $tomorrow, $db);
//			$this->fillRs($rs, 'FinanceXS', $ymd, $tomorrow, $db);
			//过滤掉系统兜底账户
			$this->fillRs($rs, 'Accounts', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'NewRegister', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'RegAndBindFailed', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'RegAndBindOk', $ymd, $tomorrow, $db);

			$this->fillRs($rs, 'BuyNAmountAll', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'BuyNUsrAll', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyAmountDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyAmountNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyAmountNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyUsrDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyUsrNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
//			$this->fillRs($rs, 'BuyUsrNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);

			$this->fillRs($rs, 'BuyNAmountDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'BuyNAmountNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'BuyNAmountNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'BuyNUsrDay', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'BuyNUsrNew', $ymd, $tomorrow, $db,['flgext02!'=>1]);
			$this->fillRs($rs, 'BuyNUsrNew0', $ymd, $tomorrow, $db,['flgext02!'=>1]);

			$this->fillRs($rs, 'BindOk', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'BindFailed', $ymd, $tomorrow, $db);

			$this->fillRs($rs, 'Stock', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'StockChange', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'ChargeAmount', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'WithdrawAmount', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'ApplyWithdrawAmount', $ymd, $tomorrow, $db);

			$this->fillRs($rs, 'YebFundFlowsTotalAmount', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'YebFundFlowsWithdraw', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'YebFundFlowsDyb', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'YebFundFlowsYebbuy', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'YebFundFlowsAccount', $ymd, $tomorrow, $db);

			$this->fillRs($rs, 'BidFundFlowsWithdraw', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'BidFundFlowsDyb', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'BidFundFlowsYebBuy', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'BidFundFlowsAccount', $ymd, $tomorrow, $db);

			$this->fillRs($rs, 'PrdtNumNew', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'PrdtNumDay', $ymd, $tomorrow, $db);
			$this->fillRs($rs, 'PrdtAmountNew', $ymd, $tomorrow, $db);

			$rs[$ymd]['{BuyTianTianAmountDay}'] = prjlib_evtdaily::getCopy('BuyPAmountDay')->numOfAct($db, $ymd,null,['flgext01'=>0]);

//			$lst2['p_realnameauth'] =['实名认证数'=>'{RealnameAuth}'];
			$rs[$ymd]['{BidBoughtAmountEmployee}'] = prjlib_evtdaily::getCopy('BidBoughtAmountEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{BidBoughtAmountEmployee}'] = prjlib_evtdaily::getCopy('BidBoughtAmountEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{BidBoughtAmountInviter}'] = prjlib_evtdaily::getCopy('BidBoughtAmountInviter')->numOfAct($db, $ymd);
			$rs[$ymd]['{BidBoughtAmountExternal}'] = prjlib_evtdaily::getCopy('BidBoughtAmountExternal')->numOfAct($db, $ymd);


			$rs[$ymd]['{BidBoughtUserNumEmployee}'] = prjlib_evtdaily::getCopy('BidBoughtUserNumEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{BidBoughtUserNumInviter}'] = prjlib_evtdaily::getCopy('BidBoughtUserNumInviter')->numOfAct($db, $ymd);
			$rs[$ymd]['{BidBoughtUserNumExternal}'] = prjlib_evtdaily::getCopy('BidBoughtUserNumExternal')->numOfAct($db, $ymd);

			$rs[$ymd]['{YebBoughtAmountEmployee}'] = prjlib_evtdaily::getCopy('YebBoughtAmountEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebBoughtAmountInviter}'] = prjlib_evtdaily::getCopy('YebBoughtAmountInviter')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebBoughtAmountExternal}'] = prjlib_evtdaily::getCopy('YebBoughtAmountExternal')->numOfAct($db, $ymd);

			$rs[$ymd]['{YebBoughtUserNumEmployee}'] = prjlib_evtdaily::getCopy('YebBoughtUserNumEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebBoughtUserNumInviter}'] = prjlib_evtdaily::getCopy('YebBoughtUserNumInviter')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebBoughtUserNumExternal}'] = prjlib_evtdaily::getCopy('YebBoughtUserNumExternal')->numOfAct($db, $ymd);


			$rs[$ymd]['{YebOutAmountEmployee}'] = prjlib_evtdaily::getCopy('YebOutAmountEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebOutAmountInviter}'] = prjlib_evtdaily::getCopy('YebOutAmountInviter')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebOutAmountExternal}'] = prjlib_evtdaily::getCopy('YebOutAmountExternal')->numOfAct($db, $ymd);

			$rs[$ymd]['{YebOutUserNumEmployee}'] = prjlib_evtdaily::getCopy('YebOutUserNumEmployee')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebOutUserNumInviter}'] = prjlib_evtdaily::getCopy('YebOutUserNumInviter')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebOutUserNumExternal}'] = prjlib_evtdaily::getCopy('YebOutUserNumExternal')->numOfAct($db, $ymd);
			$rs[$ymd]['{YebAmountIncrease}'] = prjlib_evtdaily::getCopy('YebAmountIncrease')->numOfAct($db, $ymd);

			$rs[$ymd]['{RealnameAuth}'] = prjlib_evtdaily::getCopy('RealnameAuth')->numOfAct($db, $ymd);
//			$rs[$ymd]['Stock'] = prjlib_evtdaily::getCopy('Stock')->numOfAct($db, $ymd);
//
//
//			$rs[$ymd]['NewChargeNum'] = prjlib_evtdaily::getCopy('NewChargeNum')->numOfAct($db, $ymd);
//			$rs[$ymd]['NewBought'] = prjlib_evtdaily::getCopy('NewBought')->numOfAct($db, $ymd);
//			$rs[$ymd]['BoughtTotal'] = prjlib_evtdaily::getCopy('BoughtTotal')->numOfAct($db, $ymd);
//
//			$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd);
//			$rs[$ymd]['BindFailed']=prjlib_evtdaily::getCopy('BindFailed')->numOfAct($db, $ymd);
//			$rs[$ymd]['BindRequest']=prjlib_evtdaily::getCopy('BindRequest')->numOfAct($db, $ymd);
//
//			$rs[$ymd]['BoughtAmount'] = prjlib_evtdaily::getCopy('BoughtAmount')->numOfAct($db, $ymd);
//			$rs[$ymd]['StockChange'] = prjlib_evtdaily::getCopy('StockChange')->numOfAct($db, $ymd);
//			$rs[$ymd]['ChargeAmount'] = prjlib_evtdaily::getCopy('ChargeAmount')->numOfAct($db, $ymd);
//			$rs[$ymd]['WithdrawAmount'] = prjlib_evtdaily::getCopy('WithdrawAmount')->numOfAct($db, $ymd);
			//$rs[$ymd]['BoughtFeeTotal']=prjlib_evtdaily::getCopy('BoughtFeeTotal')->numOfAct($db, $ymd,null);
			//$rs[$ymd]['WithdrawFee']=prjlib_evtdaily::getCopy('WithdrawFee')->numOfAct($db, $ymd,null);

			//$rs[$ymd]['Wallet2BankCard']=prjlib_evtdaily::getCopy('Wallet2BankCard')->numOfAct($db, $ymd,null) / 100;
			//$rs[$ymd]['WalletRecharge']=prjlib_evtdaily::getCopy('WalletRecharge')->numOfAct($db, $ymd,null) / 100;
			//$rs[$ymd]['Wallet2JBY']=prjlib_evtdaily::getCopy('Wallet2JBY')->numOfAct($db, $ymd,null) / 100;
			//$rs[$ymd]['WalletFromJBY']=prjlib_evtdaily::getCopy('WalletFromJBY')->numOfAct($db, $ymd,null) / 100 ;

			//$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd,null);
			//$rs[$ymd]['Accessed']=prjlib_evtdaily::getCopy('Accessed')->numOfAct($db, $ymd,null);
			//$rs[$ymd]['LoginPc']=prjlib_evtdaily::getCopy('Login')->numOfAct($db, $ymd,null, array('clienttype'=>900));
			//$rs[$ymd]['LoginMobile']=prjlib_evtdaily::getCopy('Login')->numOfAct($db, $ymd,null, array('clienttype!'=>900));

			$maxOrMin[$ymd]['MaxNewRegCopartner'] = prjlib_evtdaily::getCopy('MaxNewRegCopartner')->maxNumGrpBy($db, $ymd);
			$maxOrMin[$ymd]['MinNewRegCopartner'] = prjlib_evtdaily::getCopy('MinNewRegCopartner')->maxNumGrpBy($db, $ymd);
			$dt-=86400;
		}
		unset($rs[$ymd]);
		$this->_view->assign('records',$rs);

		$this->_view->assign('maxOrMin', $maxOrMin);
		$this->_view->assign('actName', $nameMap);
		$this->_view->assign('weekdayName', $this->weekdays);
		$this->_view->assign('copartners', \Prj\Misc\Funcs::copartnerIdWithcopartnerName());
	}

	public function depth1Action()
	{
		if($this->checkLogin()==false){
			$this->_view->assign('showLogin',1);
			return;
		}else $this->_view->assign('showLogin',0);		
		$forAct = $this->_request->get('forAct');
		$ymd=$this->_request->get('ymd');

		$this->_view->assign('args',array('forAct'=>$forAct,'ymd'=>$ymd));
		$this->_view->assign('grpName',$this->grpName);
	}
/**
	 * 
	 * @return \Sooh\DB\Interfaces\All
	 */
	protected function getDB()
	{
		return \Sooh\DB\Broker::getInstance('dbForRpt');
	}
	/**
	 * @return boolean 
	 */
	protected function checkLogin()
	{
		return true;
		$acl = \Sooh\DB\Acl\Acl::getInstance();
		return $acl->isLogined();
	}
	
	public function loginAction()
	{
		$form = \Sooh\Base\Form\Base::getCopy();
		$arr = $form->getFields();

		if(!empty($arr['username']) && !empty($arr['password'])){
			try{
				$acl = \Sooh\DB\Acl\Acl::getInstance();
				$camefrom = 'Jym';
				$acl->login($arr['username'], $arr['password'],1800,$camefrom);
				$account = $acl->getAclManager()->getAccount($arr['username'],$camefrom);
				$acl->setSessionVal('nickname', $account['nickname']);

				$this->_view->assign ('result','ok');
			}catch(\ErrorException $e){
				$this->_view->assign ('result', '登入失败：'.$e->getMessage());
			}
		}else  $this->_view->assign('result','用户名密码错误');
		
	}
	
//	public function indexAction()
//	{
//		$pwd = $this->_request->get('pwd');
//		if(!empty($pwd)){
//			$_SESSION['rptDailybasicPassExpire']=time()+600;
//			$this->_view->assign('goto', \Sooh\Base\Tools::uri(null, 'recent'));
//		}
//	}
	public function errorAction()
	{
		
	}
	
//	public function briefAction()
//	{
//		if($this->checkLogin()==false){
//			$this->_view->assign('showLogin',1);
//			return;
//		}else $this->_view->assign('showLogin',0);
//		$db = $this->getDB();
//		$rs = array();
//		$dt = time();
//		for($i=0;$i<10;$i++){
//			$ymd = date('Ymd',$dt);
//			$rs[$ymd]['Accounts']=prjlib_evtdaily::getCopy('Accounts')->numOfAct($db, $ymd);
//			$rs[$ymd]['BoughtTotal']=prjlib_evtdaily::getCopy('BoughtTotal')->numOfAct($db, $ymd);
//			$rs[$ymd]['NewBuyer']=prjlib_evtdaily::getCopy('NewBuyer')->numOfAct($db, $ymd);
//			$rs[$ymd]['BoughtFeeTotal']=prjlib_evtdaily::getCopy('BoughtFeeTotal')->numOfAct($db, $ymd);
//			$rs[$ymd]['WithdrawFee']=prjlib_evtdaily::getCopy('WithdrawFee')->numOfAct($db, $ymd);
//			$rs[$ymd]['Register']=prjlib_evtdaily::getCopy('Register')->numOfAct($db, $ymd,null);
//			
//			$rs[$ymd]['BindOk']=prjlib_evtdaily::getCopy('BindOk')->numOfAct($db, $ymd,null);
//			$rs[$ymd]['Accessed']=prjlib_evtdaily::getCopy('Accessed')->numOfAct($db, $ymd,null);
//			$rs[$ymd]['Login']=prjlib_evtdaily::getCopy('Login')->numOfAct($db, $ymd,null);
//			$dt-=86400;
//		}
//		
//		$this->_view->assign('records',$rs);
//
//	}

}