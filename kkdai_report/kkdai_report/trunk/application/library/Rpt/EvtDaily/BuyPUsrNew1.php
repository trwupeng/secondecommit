<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：之前注册，当日首购的总人数
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyPUsrNew1 extends Base{
	protected function actName(){return 'BuyPUsrNew1';}
	public static function displayName(){return '老注册首购人数';}
}
