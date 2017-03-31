<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDBuyers&ymdh=20160126"
 *
 * 日报购买统计
 * 快快贷这里，`flagUser`==1的超级用户不参与统计
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EDBuyers extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=840;//每14分钟启动一次
		$this->_iissStartAfter=455;//每小时05分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();
		$this->db = \Sooh\DB\Broker::getInstance();
	}
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All 
	 */
	protected $db;
	protected $ymd;
	public function free() {
		$this->db = null;
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		$this->oneday($dt->YmdFull);
		if(!$this->_isManual && $dt->hour<=6){
			$dt0 = strtotime($dt->YmdFull);
			switch ($dt->hour){
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
	protected function oneday($ymd){
		$this->ymd = $ymd;
		error_log("Trace007#".__CLASS__.'->'.__FUNCTION__.'('.$ymd.')');


		$this->gowith(	\Rpt\EvtDaily\BuyNUsrAll::getCopy('BuyNUsrAll'), 
						\Rpt\EvtDaily\BuyNAmountAll::getCopy('BuyNAmountAll'),
					$this->sqlBaseNone('tb_orders_final.ymd<='.$ymd)
		);

		//今日注册并购买
		$this->gowith(	\Rpt\EvtDaily\BuyNUsrNew0::getCopy('BuyNUsrNew0'), 
						\Rpt\EvtDaily\BuyNAmountNew0::getCopy('BuyNAmountNew0'),
				$this->sqlBaseNone('tb_orders_final.ymd='.$ymd.' and tb_user_final.ymdReg='.$ymd)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyCUsrNew0::getCopy('BuyCUsrNew0'), 
						\Rpt\EvtDaily\BuyCAmountNew0::getCopy('BuyCAmountNew0'),
				$this->sqlBaseClient('tb_orders_final.ymd='.$ymd.' and tb_user_final.ymdReg='.$ymd)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyPUsrNew0::getCopy('BuyPUsrNew0'), 
						\Rpt\EvtDaily\BuyPAmountNew0::getCopy('BuyPAmountNew0'),
				$this->sqlBasePrdt('tb_orders_final.ymd='.$ymd.' and tb_user_final.ymdReg='.$ymd)
		);
		//往日注册，今日首购
		$NewBuy = $this->db->getCol('tb_orders_final','distinct(userId)',['firstTimeInAll'=>1, 'ymd'=>$ymd]);
		//error_log('total new buy:'.sizeof($NewBuy).'#'. \Sooh\DB\Broker::lastCmd());
		if(!empty($NewBuy)){
			$oldRegNewBuy = $this->db->getCol('tb_user_final', 'userId',['userId'=>$NewBuy,'ymdReg!'=>$ymd]);//排除今日注册的
			//error_log('total new buy:'.sizeof($NewBuy).'#'. \Sooh\DB\Broker::lastCmd());
//var_log($oldRegNewBuy, '$oldRegNewBuy>>>>');
			if(!empty($oldRegNewBuy)){
				$this->gowith(	\Rpt\EvtDaily\BuyNUsrNew1::getCopy('BuyNUsrNew1'), 
								\Rpt\EvtDaily\BuyNAmountNew1::getCopy('BuyNAmountNew1'),
						$this->sqlBaseNone('tb_orders_final.ymd='.$ymd.'  and tb_orders_final.userId in (\''.implode("','",$oldRegNewBuy).'\')')
				);
				$this->gowith(	\Rpt\EvtDaily\BuyCUsrNew1::getCopy('BuyCUsrNew1'),
								\Rpt\EvtDaily\BuyCAmountNew1::getCopy('BuyCAmountNew1'),
						$this->sqlBaseClient('tb_orders_final.ymd='.$ymd.'  and tb_orders_final.userId in (\''.implode("','",$oldRegNewBuy).'\')')
				);
				$this->gowith(	\Rpt\EvtDaily\BuyPUsrNew1::getCopy('BuyPUsrNew1'), 
								\Rpt\EvtDaily\BuyPAmountNew1::getCopy('BuyPAmountNew1'),
						$this->sqlBasePrdt('tb_orders_final.ymd='.$ymd.'  and tb_orders_final.userId in (\''.implode("','",$oldRegNewBuy).'\')')
				);
			}
		}

		//往日注册，再次购买
		$where= 'tb_orders_final.ymd='.$ymd;
		if(!empty($NewBuy)){
			$where.=' and tb_orders_final.userId not in (\''.implode("','",$NewBuy).'\')';
		}
		$this->gowith(	\Rpt\EvtDaily\BuyNUsrNew1::getCopy('BuyNUsrOlder'), 
						\Rpt\EvtDaily\BuyNAmountNew1::getCopy('BuyNAmountOlder'),
				$this->sqlBaseNone($where)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyCUsrNew1::getCopy('BuyCUsrOlder'), 
						\Rpt\EvtDaily\BuyCAmountNew1::getCopy('BuyCAmountOlder'),
				$this->sqlBaseClient($where)
		);
		$this->gowith(	\Rpt\EvtDaily\BuyPUsrNew1::getCopy('BuyPUsrOlder'), 
						\Rpt\EvtDaily\BuyPAmountNew1::getCopy('BuyPAmountOlder'),
				$this->sqlBasePrdt($where)
		);
		
		$BuyAmountDay = \Rpt\EvtDaily\BuyNAmountAll::getCopy('BuyNAmountDay');
		$BuyUsrDay = \Rpt\EvtDaily\BuyNUsrAll::getCopy('BuyNUsrDay');


		// 日报中标的数据的统计
//		$this->db = \Sooh\DB\Broker::getInstance();
		error_log('标的统计需要状态位参与判定');
		// 新标数量
		$prdtNumNew = \Rpt\EvtDaily\PrdtNumNew::getCopy('PrdtNumNew');
		$prdtNumNew->reset();
		// 信标金额
		$prdtAmountNew  = \Rpt\EvtDaily\PrdtAmountNew::getCopy('PrdtAmountNew');
		$prdtAmountNew->reset();
		// 旧标数量
		$prdtNumOlder  = \Rpt\EvtDaily\PrdtNumOlder::getCopy('PrdtNumOlder');
		$prdtNumOlder->reset();
		// 旧表金额
		$prdtAmountOlder = \Rpt\EvtDaily\PrdtAmountOlder::getCopy('PrdtAmountOlder');
		$prdtAmountOlder->reset();

		$rs = $this->db->getRecords('db_kkrpt.tb_products_final', 'shelfId,mainType, count(*) as n, sum(amount)/100 as a ',
				['ymdStartReal'=>$ymd,  'mainType!'=>501,
						'statusCode!'=>[
								\Prj\Consts\Wares::status_failure,
								\Prj\Consts\Wares::status_applyed,
								\Prj\Consts\Wares::status_commit,
								\Prj\Consts\Wares::status_rezhenged,
								\Prj\Consts\Wares::status_ready,
								\Prj\Consts\Wares::status_liubiao_ing,
								\Prj\Consts\Wares::status_initial_a,
								\Prj\Consts\Wares::status_checked,
								\Prj\Consts\Wares::status_new,
						]

				],'group shelfId group mainType');
		foreach ($rs as $r){
			$prdtAmountNew->add($r['a'], 0, 0, $r['shelfId'],$r['mainType']);
			$prdtNumNew->add($r['n'], 0, 0, $r['shelfId'],$r['mainType']);
		}
		$prdtAmountNew->save($this->db, $ymd);
		$prdtNumNew->save($this->db, $ymd);
		//var_log($rs);
		//var_log(\Sooh\DB\Broker::lastCmd(false));

		$rs = $this->db->getRecords('db_kkrpt.tb_products_final', 'shelfId,mainType, count(*) as n, sum(amount)/100 as a ',
				['ymdStartReal<'=>$ymd,'ymdEndReal]'=>$ymd, 'mainType!'=>501,
						'statusCode!'=>[
								\Prj\Consts\Wares::status_failure,
								\Prj\Consts\Wares::status_applyed,
								\Prj\Consts\Wares::status_commit,
								\Prj\Consts\Wares::status_rezhenged,
								\Prj\Consts\Wares::status_ready,
								\Prj\Consts\Wares::status_liubiao_ing,
								\Prj\Consts\Wares::status_initial_a,
								\Prj\Consts\Wares::status_checked,
								\Prj\Consts\Wares::status_new,
						]
				],
				'group shelfId group mainType');

		foreach ($rs as $r){
			//$prdtAmountNew->add($r['a'], 0, 0, $r['shelfId'],$r['mainType']);
			$prdtAmountOlder->add($r['a'], 0, 0, $r['shelfId'],$r['mainType']);
			$prdtNumOlder->add($r['n'], 0, 0, $r['shelfId'],$r['mainType']);
		}
		$rs = $this->db->getRecords('db_kkrpt.tb_products_final', 'shelfId,mainType, count(*) as n, sum(amount)/100 as a ',
				['ymdStartReal<'=>$ymd,'ymdEndReal'=>0, 'mainType!'=>501,
						'statusCode!'=>[
								\Prj\Consts\Wares::status_failure,
								\Prj\Consts\Wares::status_applyed,
								\Prj\Consts\Wares::status_commit,
								\Prj\Consts\Wares::status_rezhenged,
								\Prj\Consts\Wares::status_ready,
								\Prj\Consts\Wares::status_liubiao_ing,
								\Prj\Consts\Wares::status_initial_a,
								\Prj\Consts\Wares::status_checked,
								\Prj\Consts\Wares::status_new,
						]
				],
				'group shelfId group mainType');
		if(!empty($rs)) {
			foreach($rs as $r) {
				$prdtAmountOlder->add($r['a'], 0, 0, $r['shelfId'],$r['mainType']);
				$prdtNumOlder->add($r['n'], 0, 0, $r['shelfId'],$r['mainType']);
			}
		}

//		var_log($rs);
//		var_log(\Sooh\DB\Broker::lastCmd(false));
		$prdtNumOlder->save($this->db, $ymd);
		$prdtAmountOlder->save($this->db, $ymd);




		// 新标产品id
		$newPrdt = $this->db->getCol('db_kkrpt.tb_products_final', 'waresId', ['ymdStartReal'=>$ymd,  'mainType!'=>501,
				'statusCode!'=>[
						\Prj\Consts\Wares::status_failure,
						\Prj\Consts\Wares::status_applyed,
						\Prj\Consts\Wares::status_commit,
						\Prj\Consts\Wares::status_rezhenged,
						\Prj\Consts\Wares::status_ready,
						\Prj\Consts\Wares::status_liubiao_ing,
						\Prj\Consts\Wares::status_initial_a,
						\Prj\Consts\Wares::status_checked,
						\Prj\Consts\Wares::status_new,
				]

		]);
//error_log(\Sooh\DB\Broker::lastCmd());
//		var_log($newPrdt, '新标产品id:');
		// 旧标产品id
		$oldPrdt = $this->db->getCol('db_kkrpt.tb_products_final', 'waresId', ['ymdStartReal<'=>$ymd,'ymdEndReal]'=>$ymd, 'mainType!'=>501,
				'statusCode!'=>[
						\Prj\Consts\Wares::status_failure,
						\Prj\Consts\Wares::status_applyed,
						\Prj\Consts\Wares::status_commit,
						\Prj\Consts\Wares::status_rezhenged,
						\Prj\Consts\Wares::status_ready,
						\Prj\Consts\Wares::status_liubiao_ing,
						\Prj\Consts\Wares::status_initial_a,
						\Prj\Consts\Wares::status_checked,
						\Prj\Consts\Wares::status_new,
				]
		]);

		$tmp_old_prdt = $this->db->getCol('db_kkrpt.tb_products_final', 'waresId', ['ymdStartReal<'=>$ymd,'ymdEndReal'=>0, 'mainType!'=>501,
				'statusCode!'=>[
						\Prj\Consts\Wares::status_failure,
						\Prj\Consts\Wares::status_applyed,
						\Prj\Consts\Wares::status_commit,
						\Prj\Consts\Wares::status_rezhenged,
						\Prj\Consts\Wares::status_ready,
						\Prj\Consts\Wares::status_liubiao_ing,
						\Prj\Consts\Wares::status_initial_a,
						\Prj\Consts\Wares::status_checked,
						\Prj\Consts\Wares::status_new,
				]
		]);

		$oldPrdt = array_merge($oldPrdt, $tmp_old_prdt);
//		var_log($oldPrdt, '旧标产品Id:');
		$superUser = $this->db->getCol('db_kkrpt.tb_user_final', 'userId', ['flagUser'=>1]);


		// 新标超级用户当日购买
		$newPrdtSuperBuyAmountThisDay = \Rpt\EvtDaily\NewPrdtSuperBuyAmountThisDay::getCopy('NewPrdtSuperBuyAmountThisDay');
		$newPrdtSuperBuyAmountThisDay->reset();
		$where = ' ymd='.$ymd.' and tb_orders_final.userId  in (\''.implode("','",$superUser).'\') and tb_orders_final.waresId in (\''.implode("','", $newPrdt).'\')' ;
		$rs = $this->getResult($this->db, $this->sqlBaseBuy($where));
//var_log($rs, '当日超级用户购买新标金额：');
		if (!empty($rs)) {
			foreach($rs as $r) {
				$newPrdtSuperBuyAmountThisDay->add($r['n'], 0, 0, 0, 0);
			}
			$newPrdtSuperBuyAmountThisDay->save($this->db, $ymd);
		}

		// 新标普通用户当日购买
		$newPrdtCommonBuyAmountThisDay = \Rpt\EvtDaily\NewPrdtCommonBuyAmountThisDay::getCopy('NewPrdtCommonBuyAmountThisDay');
		$newPrdtCommonBuyAmountThisDay->reset();
		$where = ' ymd='.$ymd.' and tb_orders_final.userId not in (\''.implode("','",$superUser).'\') and tb_orders_final.waresId in (\''.implode("','", $newPrdt).'\')' ;
		$rs = $this->getResult($this->db, $this->sqlBaseBuy($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if (!empty($rs)) {
			foreach($rs as $r) {
				$newPrdtCommonBuyAmountThisDay->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$newPrdtCommonBuyAmountThisDay->save($this->db, $ymd);
		}
//var_log($rs, '当日非超级用户新标购买金额：');
		// 新标券使用金额
		$newPrdtVoucherUseAmountThisDay = \Rpt\EvtDaily\NewPrdtVoucherUseAmountThisDay::getCopy('NewPrdtVoucherUseAmountThisDay');
		$newPrdtVoucherUseAmountThisDay->reset();
		$where = ' ymd='.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $newPrdt).'\')' ;
		$rs = $this->getResult($this->db, $this->sqlBaseVoucher($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if(!empty($rs)) {
			foreach($rs as $r) {
				$newPrdtVoucherUseAmountThisDay->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$newPrdtVoucherUseAmountThisDay->save($this->db, $ymd);
		}

//var_log($rs, '当日新标券使用金额:');



		// 旧标超级用户之前购买
		$oldPrdtSuperBuyAmountBefore = \Rpt\EvtDaily\OldPrdtSuperBuyAmountBefore::getCopy('OldPrdtSuperBuyAmountBefore');
		$oldPrdtSuperBuyAmountBefore->reset();
		$where = ' ymd<'.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $oldPrdt).'\') and tb_user_final.flagUser=1';
		$rs = $this->getResult($this->db, $this->sqlBaseBuy($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if(!empty($rs)) {
			foreach($rs as $r) {
				$oldPrdtSuperBuyAmountBefore->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$oldPrdtSuperBuyAmountBefore->save($this->db, $ymd);
		}

//var_log($rs, '旧标超级用户之前购买:');

		// 旧标非超级用户之前购买
		$oldPrdtCommonBuyAmountBefore = \Rpt\EvtDaily\OldPrdtCommonBuyAmountBefore::getCopy('OldPrdtCommonBuyAmountBefore');
		$oldPrdtCommonBuyAmountBefore->reset();
		$where = 'ymd<'.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $oldPrdt).'\') and tb_user_final . flagUser != 1';
		$rs = $this->getResult($this->db, $this->sqlBaseBuy($where));
		if(!empty($rs)){
			foreach($rs as $r) {
				$oldPrdtCommonBuyAmountBefore->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$oldPrdtCommonBuyAmountBefore->save($this->db, $ymd);
		}
//var_log($rs, '旧标非超级用户之前购买:');


		// 旧标券之前使用
		$oldPrdtVoucherUseAmountBefore = \Rpt\EvtDaily\OldPrdtVoucherUseAmountBefore::getCopy('OldPrdtVoucherUseAmountBefore');
		$oldPrdtVoucherUseAmountBefore->reset();
		$where = 'ymd<'.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $oldPrdt).'\') and amountExt>0';
		$rs = $this->getResult($this->db, $this->sqlBaseVoucher($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if(!empty($rs)){
			foreach($rs as $r) {
				$oldPrdtVoucherUseAmountBefore->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$oldPrdtVoucherUseAmountBefore->save($this->db, $ymd);
		}
//var_log($rs, '旧标券之前使用:');

		// 旧标当日超级用户购买
		$oldPrdtSuperBuyAmountThisDay = \Rpt\EvtDaily\OldPrdtSuperBuyAmountThisDay::getCopy('OldPrdtSuperBuyAmountThisDay');
		$oldPrdtSuperBuyAmountThisDay->reset();
		$where = 'ymd='.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $oldPrdt).'\') and tb_user_final.flagUser=1';
		$rs = $this->getResult($this->db, $this->sqlBaseBuy($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if(!empty($rs)) {
			foreach($rs as $r) {
				$oldPrdtSuperBuyAmountThisDay->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$oldPrdtSuperBuyAmountThisDay->save($this->db, $ymd);
		}

//var_log($rs, '旧标超级用户当日购买：');

		// 旧标当日非超级用户购买
		$oldPrdtCommonBuyAmountThisDay = \Rpt\EvtDaily\OldPrdtCommonBuyAmountThisDay::getCopy('OldPrdtCommonBuyAmountThisDay');
		$oldPrdtCommonBuyAmountThisDay->reset();
		$where = 'ymd='.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $oldPrdt).'\') and tb_user_final.flagUser != 1';
		$rs = $this->getResult($this->db, $this->sqlBaseBuy($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if(!empty($rs)) {
			foreach($rs as $r) {
				$oldPrdtCommonBuyAmountThisDay->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$oldPrdtCommonBuyAmountThisDay->save($this->db, $ymd);
		}

//var_log($rs, '旧标非超级用户当日购买：');

		// 旧标当日券使用金额
		$oldPrdtVoucherUseAmountThisDay = \Rpt\EvtDaily\OldPrdtVoucherUseAmountThisDay::getCopy('OldPrdtVoucherUseAmountThisDay');
		$oldPrdtVoucherUseAmountThisDay->reset();
		$where = 'ymd='.$ymd.' and tb_orders_final.waresId in (\''.implode("','", $oldPrdt).'\') and amountExt>0';
		$rs = $this->getResult($this->db, $this->sqlBaseVoucher($where));
//error_log(\Sooh\DB\Broker::lastCmd());
		if(!empty($rs)) {
			foreach($rs as $r) {
				$oldPrdtVoucherUseAmountThisDay->add($r['n'], 0, $r['copartnerId'], 0, 0);
			}
			$oldPrdtVoucherUseAmountThisDay->save($this->db, $ymd);
		}


		$this->lastMsg = ' Today:'.$BuyUsrDay->numOfAct($this->db, $this->ymd) .' amount:'.$BuyAmountDay->numOfAct($this->db, $this->ymd);//要在运行日志中记录的信息
	}


	protected function sqlBaseNone($where)
	{
		return 'select 0 as clientType,0 as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, count(distinct(tb_orders_final.userId)) as u,sum(tb_orders_final.amount)/100 as n'
				. ' from tb_orders_final '
				. ' left join tb_user_final on tb_orders_final.userid=tb_user_final.userId '
				. ' where '.$where . ' and tb_orders_final.orderStatus in (8,10,39) and tb_orders_final.poi_type!=1 '//and tb_user_final.flagUser!=1
				. ' and tb_orders_final.waresId not in (select waresId from tb_products_final where mainType=501)'
				. ' group by  tb_user_final.copartnerId,tb_user_final.flagUser';
	}
	protected function sqlBaseClient($where)
	{
		return 'select tb_orders_final.clientType as clientType,0 as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, count(distinct(tb_orders_final.userId)) as u,sum(tb_orders_final.amount)/100 as n'
				. ' from tb_orders_final '
				. ' left join tb_user_final on tb_orders_final.userid=tb_user_final.userId '
				. ' where '.$where . ' and tb_orders_final.orderStatus in (8,10,39) and tb_orders_final.poi_type!=1 '//and tb_user_final.flagUser!=1
				. ' and tb_orders_final.waresId not in (select waresId from tb_products_final where mainType=501)'
				. ' group by tb_orders_final.clientType,tb_user_final.copartnerId,tb_user_final.flagUser';
	}
	protected function sqlBasePrdt($where)
	{
		return 'select 0 as clientType,tb_orders_final.shelfId as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, count(distinct(tb_orders_final.userId)) as u,sum(tb_orders_final.amount)/100 as n'
				. ' from tb_orders_final '
				. ' left join tb_user_final on tb_orders_final.userid=tb_user_final.userId '
				. ' where '.$where . ' and tb_orders_final.orderStatus in (8,10,39) and tb_orders_final.poi_type!=1 '//and tb_user_final.flagUser!=1
				. ' and tb_orders_final.waresId not in (select waresId from tb_products_final where mainType=501)'
				. ' group by tb_orders_final.shelfId,tb_user_final.copartnerId,tb_user_final.flagUser';
	}	
	/**
	 * @param \Rpt\EvtDaily\Base $user
	 * @param \Rpt\EvtDaily\Base $amount
	 * @param string $sql select u,n,clientType,copartnerId,prdtType,uType
	 */
	protected function gowith($user,$amount,$sql)
	{
//		error_log($sql);
		$result = $this->db->execCustom(['sql'=>$sql]);
		$rs = $this->db->fetchAssocThenFree($result);
		$user->reset();
		$amount->reset();
		foreach ($rs as $r){
			$user->add($r['u'], $r['clientType']>0?$r['clientType']:900, $r['copartnerId'],$r['prdtType'],$r['uType']-0);
			$amount->add($r['n'], $r['clientType']>0?$r['clientType']:900, $r['copartnerId'],$r['prdtType'],$r['uType']-0);
		}
//		var_log($rs,  \Sooh\DB\Broker::lastCmd());
		$user->save($this->db, $this->ymd);
		$amount->save($this->db, $this->ymd);
	}

// 下面的是从EDProducts迁移过来的

	protected function sqlBaseBuy ($where){
		return 'select 0 as clientType,0 as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, sum(tb_orders_final.amount)/100 as n'
		. ' from tb_orders_final '
		. ' left join tb_user_final on tb_orders_final.userid=tb_user_final.userId '
		. ' where '.$where . ' and tb_orders_final.orderStatus in (8,10,39)  and tb_orders_final.poi_type!=1 '
		. ' group by  tb_user_final.copartnerId,tb_user_final.flagUser';
	}

	protected function sqlBaseVoucher($where) {
		return 'select 0 as clientType,0 as prdtType,tb_user_final.copartnerId, tb_user_final.flagUser as uType, sum(tb_orders_final.amountExt)/100 as n'
		. ' from tb_orders_final '
		. ' left join tb_user_final on tb_orders_final.userid=tb_user_final.userId '
		. ' where '.$where . ' and tb_orders_final.orderStatus in (8,10,39) and amountExt >0  and tb_orders_final.poi_type!=1 '
		. ' group by  tb_user_final.copartnerId,tb_user_final.flagUser';
	}

	protected function getResult ($db, $sql) {
		$result = $db->execCustom(['sql'=>$sql]);
		$rs = $db->fetchAssocThenFree($result);
		return $rs;
	}
}
