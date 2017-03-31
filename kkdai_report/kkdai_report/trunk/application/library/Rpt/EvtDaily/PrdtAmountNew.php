<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新上线标的总额
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PrdtAmountNew extends Base{
	protected function actName(){return 'PrdtAmountNew';}
	public static function displayName(){return '新上线标的总额';}
	protected function basement() {return 100;}
}
