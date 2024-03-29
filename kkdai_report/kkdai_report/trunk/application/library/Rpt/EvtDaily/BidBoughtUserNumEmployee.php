<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：平台存量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BidBoughtUserNumEmployee extends Base{
	protected function actName(){return 'BidBoughtUserNumEmployee';}
	public static function displayName(){return '内部员工';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}
	
	public function numOfAct($db, $ymd)
	{
		//BuyPUsrOlder  BuyPUsrNew1  BuyPUsrNew0
		return  \Rpt\EvtDaily\Base::getCopy('BuyPUsrOlder')->numOfAct($db, $ymd, null,['flgext01!'=>0,'flgext02'=>2])
		+ \Rpt\EvtDaily\Base::getCopy('BuyPUsrNew1')->numOfAct($db, $ymd, null,['flgext01!'=>0,'flgext02'=>2])
		+ \Rpt\EvtDaily\Base::getCopy('BuyPUsrNew0')->numOfAct($db, $ymd, null,['flgext01!'=>0,'flgext02'=>2]);
	}
}
