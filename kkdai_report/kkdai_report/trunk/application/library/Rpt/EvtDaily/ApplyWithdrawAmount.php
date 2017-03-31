<?php
namespace Rpt\EvtDaily;
/**
 * 当日提现金额
 * 以这一天申请的算
 *
 */
class ApplyWithdrawAmount extends Base {
	protected function actName(){return 'ApplyWithdrawAmount';}
	public static function displayName(){return '当日申请提现金额（元）';}
	public function divisor () {return 100;}
	protected function basement() {return 100;}
}