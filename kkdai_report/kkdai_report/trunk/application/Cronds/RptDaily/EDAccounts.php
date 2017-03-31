<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDAccounts&ymdh=20160128"
 * 日报账户统计
 * 快快贷这里，`flagUser`==1的超级用户不参与统计
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EDAccounts extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=1200;//每20分钟启动一次
		$this->_iissStartAfter=255;//每小时03分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}

	/**
	 *
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
	
	protected function oneday($ymd)
	{
		$accounts = \Rpt\EvtDaily\Accounts::getCopy('Accounts');
		$accounts->reset();
		$newReg  = \Rpt\EvtDaily\NewRegister::getCopy('NewRegister');
		$newReg->reset();
		$db = \Sooh\DB\Broker::getInstance();//,'flagUser!'=>1
		$rs = $db->getRecords('db_kkrpt.tb_user_final', 'flagUser,clientType, copartnerId, count(*) as total ',['ymdReg['=>$ymd],'group clientType group copartnerId group flagUser');
//		error_log("[traceEDAccounts][".($this->_isManual?"manual":"crond").".".$dt->YmdFull."]".\Sooh\DB\Broker::lastCmd());
//		var_log($rs);
		$total = 0;
		foreach ($rs as $r){
			$total+=$r['total'];
			$accounts->add($r['total'], $r['clientType']>0?$r['clientType']:900, $r['copartnerId'],0,$r['flagUser']);
		}
		//,'flagUser!'=>1
		$rs = $db->getRecords('db_kkrpt.tb_user_final', 'flagUser,clientType, copartnerId, count(*) as total ',['ymdReg'=>$ymd],'group clientType group copartnerId group flagUser');
		//var_log($rs,  '[trace-NewRegister]'.\Sooh\DB\Broker::lastCmd());
		foreach ($rs as $r){
			$newReg->add($r['total'], $r['clientType']>0?$r['clientType']:900, $r['copartnerId'],0,$r['flagUser']);
		}
		$newReg->save($db,  $ymd);
		//error_log(\Sooh\DB\Broker::lastCmd());
		$accounts->save($db, $ymd);
//		error_log(\Sooh\DB\Broker::lastCmd());
//		var_log(\Sooh\DB\Broker::lastCmd(false),"[trace-save]".$accounts->totalAdd);
		$this->lastMsg = 'Total('.$ymd.'):'.$accounts->totalAdd;//要在运行日志中记录的信息
	}
}
