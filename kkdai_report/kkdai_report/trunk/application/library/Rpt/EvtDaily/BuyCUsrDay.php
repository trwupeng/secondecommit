<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：当日购买人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyCUsrDay extends Base{
	protected function actName(){return 'BuyCUsrDay';}
	public static function displayName(){return '当日购买人数';}
	public function formula()
	{
		return ['BuyCUsrNew1','+','BuyCUsrOlder','+','BuyCUsrNew0'];
	}
}
