<?php
namespace Rpt\EvtDaily;
class StockChange extends Base {
	protected function actName(){return 'StockChange';}
	public static function displayName(){return '存量变化（元）';}
	public function formula()
	{
	    return explode(' ','ChargeAmount - WithdrawAmount');
	}
	
}



