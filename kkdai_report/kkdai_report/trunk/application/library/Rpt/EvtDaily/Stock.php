<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：平台存量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Stock extends Base{
	protected function actName(){return 'Stock';}
	public static function displayName(){return '存量（元）';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}
	
	public function numOfAct($db, $ymd, $grpBy = null, $where = null)
	{
		$basement = $this->basement();
		$rechargeAmountTotal= $db->getOne(\Rpt\Tbname::tb_evtdaily, 'sum(n)', ['ymd['=>$ymd, 'act'=>'ChargeAmount']);
		$withdrawAmountTotal = $db->getOne(\Rpt\Tbname::tb_evtdaily, 'sum(n)',['ymd['=>$ymd, 'act'=>'WithdrawAmount']);
		$withdrawAmountTotal = abs($withdrawAmountTotal);

		return ($rechargeAmountTotal - $withdrawAmountTotal)/$basement;
	}
}
