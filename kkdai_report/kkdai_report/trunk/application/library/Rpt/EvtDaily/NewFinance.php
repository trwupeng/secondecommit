<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：快快金融平台财务流水总金额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class NewFinance extends Base{
	protected function actName(){return 'NewFinance';}
	public static function displayName(){return '财务增量';}
    protected function basement(){return 1;}
    public function formula()
    {
        return ['NewFinanceK','+','NewFinanceM','+','NewFinanceXS','+','NewFinanceXX'];
    }
}
