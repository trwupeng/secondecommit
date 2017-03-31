<?php
namespace Rpt\EvtDaily;
/**
 * 余额宝转出
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class YebOutAmountAll extends Base{
	protected function actName(){return 'YebOutAmountAll';}
	public static function displayName(){return '余额宝转出总额';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}

}
