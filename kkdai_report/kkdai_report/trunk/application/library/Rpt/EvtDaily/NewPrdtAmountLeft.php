<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新上线的标的数量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class NewPrdtAmountLeft extends Base{
	protected function actName(){return 'NewPrdtAmountLeft';}
	public static function displayName(){return '当日新标剩余金额';}
	public function formula()
	{
		return ['PrdtAmountNew','-','NewPrdtSuperBuyAmountThisDay','-','NewPrdtCommonBuyAmountThisDay','-','NewPrdtVoucherUseAmountThisDay'];
	}
}
