<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：当日购买金额
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyPAmountDay extends Base{
	protected function actName(){return 'BuyPAmountDay';}
	public static function displayName(){return '当日购买金额（元）';}
	
	public function formula()
	{
		return ['BuyPAmountNew1','+','BuyPAmountOlder','+','BuyPAmountNew0'];
	}
}
