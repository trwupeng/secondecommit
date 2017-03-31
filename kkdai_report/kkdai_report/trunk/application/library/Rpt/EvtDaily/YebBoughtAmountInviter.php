<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：平台存量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class YebBoughtAmountInviter extends Base{
	protected function actName(){return 'YebBoughtAmountInviter';}
	public static function displayName(){return '员工推荐';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}

	public function numOfAct($db, $ymd)
	{
		//BuyPAmountOlder  BuyPAmountNew1  BuyPAmountNew0
		return  \Rpt\EvtDaily\Base::getCopy('BuyPAmountOlder')->numOfAct($db, $ymd, null,['flgext01'=>0, 'flgext02'=>3])
				+ \Rpt\EvtDaily\Base::getCopy('BuyPAmountNew1')->numOfAct($db, $ymd,null,['flgext01'=>0, 'flgext02'=>3])
				+ \Rpt\EvtDaily\Base::getCopy('BuyPAmountNew0')->numOfAct($db, $ymd,null,['flgext01'=>0, 'flgext02'=>3]);
	}
}
