<?php
namespace Rpt\EvtDaily;
/**
 * 当日充值金额
 * 注意：这里的clientType 始终是 0
 * 以这一天申请的算
 *
 */
class ChargeAmount extends Base {
	protected function actName(){return 'ChargeAmount';}
	public static function displayName(){return '当日充值金额（元）';}
	public function divisor () {
	    return 100;
	}
	
	protected function basement() {return 100;}
}