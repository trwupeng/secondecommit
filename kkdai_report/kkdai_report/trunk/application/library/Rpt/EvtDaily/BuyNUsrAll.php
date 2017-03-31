<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：总购买人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType，prdtType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyNUsrAll extends Base{
	protected function actName(){return 'BuyNUsrAll';}
	public static function displayName(){return '累计购买人数';}

}
