<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新增购买人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyCUsrNew extends Base{
	protected function actName(){return 'BuyCUsrNew';}
	public static function displayName(){return '新增购买人数';}
	public function formula()
	{
		return ['BuyCUsrNew1','+','BuyCUsrNew0'];
	}	
}
