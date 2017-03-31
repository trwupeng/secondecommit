<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：快快金融平台财务流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class NewFinanceM extends Base{
	protected function actName(){return 'NewFinanceM';}
	public static function displayName(){return '美豫财务新增';}
    protected function basement(){return 100;}
}
