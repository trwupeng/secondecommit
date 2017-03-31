<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：快快金融平台财务流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class NewFinanceXS extends Base{
	protected function actName(){return 'NewFinanceXS';}
	public static function displayName(){return '线上业务财务新增';}
    protected function basement(){return 100;}
}
