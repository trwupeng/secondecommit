<?php
namespace Rpt\EvtDaily;
/**
 * 当日提现金额
 * 以这一天申请的算
 *
 */
class WithdrawAmountByClient extends Base {
	protected function actName(){return 'WithdrawAmountByClient';}
	public static function displayName(){return '当日提现到账金额（元）';}
	public function divisor () {return 100;}
	protected function basement() {return 100;}
}