<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新注册用户注册当日购买金额
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyPAmountNew extends Base{
	protected function actName(){return 'BuyPAmountNew';}
	public static function displayName(){return '当日首购金额';}
	public function formula()
	{
		return ['BuyPAmountNew0','+','BuyPAmountNew1'];
	}
}
