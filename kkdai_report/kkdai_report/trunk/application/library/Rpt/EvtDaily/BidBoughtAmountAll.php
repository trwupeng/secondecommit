<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：平台存量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BidBoughtAmountAll extends Base{
	protected function actName(){return 'BidBoughtAmountAll';}
	public static function displayName(){return '抵押标理财总额';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}

	public function formula()
	{
		return explode(' ','BidBoughtAmountEmployee + BidBoughtAmountExternal + BidBoughtAmountInviter');
	}
}
