<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新上线的标的数量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class PrdtAmountDay extends Base{
	protected function actName(){return 'PrdtAmountDay';}
	public static function displayName(){return '有效标的总金额';}
	public function formula()
	{
		return ['PrdtAmountNew','+','PrdtAmountOlder'];
	}
}
