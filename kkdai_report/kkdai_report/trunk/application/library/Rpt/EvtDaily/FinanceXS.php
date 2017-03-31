<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：线上投标流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FinanceXS extends Base{
	protected function actName(){return 'FinanceXS';}
	public static function displayName(){return '线上投标';}
    protected function basement(){return 100;}
}
