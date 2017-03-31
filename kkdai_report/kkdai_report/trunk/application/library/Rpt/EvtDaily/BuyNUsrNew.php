<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新增购买人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType，prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyNUsrNew extends Base{
	protected function actName(){return 'BuyNUsrNew';}
	public static function displayName(){return '新增购买人数';}
	public function formula()
	{
		return ['BuyNUsrNew1','+','BuyNUsrNew0'];
	}	
}
