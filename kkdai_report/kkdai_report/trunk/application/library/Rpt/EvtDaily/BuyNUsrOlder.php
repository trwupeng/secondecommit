<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：以前购买过的用户当日购买人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType，prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyNUsrOlder extends Base{
	protected function actName(){return 'BuyNUsrOlder';}
	public static function displayName(){return '老投资客当日购买人数';}
}
