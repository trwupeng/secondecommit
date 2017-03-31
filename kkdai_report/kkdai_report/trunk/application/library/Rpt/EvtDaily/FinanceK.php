<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：快快金融平台财务流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class FinanceK extends Base{
	protected function actName(){return 'FinanceK';}
	public static function displayName(){return '快快金融';}
    protected function basement(){return 100;}
}
