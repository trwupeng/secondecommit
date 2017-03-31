<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：累计购买总额
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType，prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyNAmountAll extends Base{
	protected function actName(){return 'BuyNAmountAll';}
	public static function displayName(){return '累计购买总额';}
	protected function basement() {return 100;}
}
