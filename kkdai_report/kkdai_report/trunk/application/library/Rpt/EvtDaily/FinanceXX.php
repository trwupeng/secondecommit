<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：线下投标流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FinanceXX extends Base{
	protected function actName(){return 'FinanceXX';}
	public static function displayName(){return '线下投标';}
    protected function basement(){return 100;}
}
