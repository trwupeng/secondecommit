<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：美豫平台财务流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FinanceM extends Base{
	protected function actName(){return 'FinanceM';}
	public static function displayName(){return '美豫平台';}
    protected function basement(){return 100;}
}
