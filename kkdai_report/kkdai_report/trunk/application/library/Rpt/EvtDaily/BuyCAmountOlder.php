<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：以前购买过的用户当日购买金额
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyCAmountOlder extends Base{
	protected function actName(){return 'BuyCAmountOlder';}
	public static function displayName(){return '老投资客当日购买金额';}
	protected function basement() {return 100;}
}
