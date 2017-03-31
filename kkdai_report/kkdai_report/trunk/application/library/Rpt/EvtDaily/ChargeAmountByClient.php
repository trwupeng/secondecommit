<?php
namespace Rpt\EvtDaily;
/**
 * 当日充值金额
 * 以这一天申请的算
 *
 */
class ChargeAmountByClient extends Base {
	protected function actName(){return 'ChargeAmountByClient';}
	public static function displayName(){return '当日充值金额（元）';}
	public function divisor () {
	    return 100;
	}
	
	protected function basement() {return 100;}
}