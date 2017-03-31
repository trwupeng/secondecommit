<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：当日购买人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType，prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyNUsrDay extends Base{
	protected function actName(){return 'BuyNUsrDay';}
	public static function displayName(){return '当日购买人数';}
	public function formula()
	{
		return ['BuyNUsrNew1','+','BuyNUsrOlder','+','BuyNUsrNew0'];
	}
}
