<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：平台存量
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class YebOutUserNumInviter extends Base{
	protected function actName(){return 'YebOutUserNumInviter';}
	public static function displayName(){return '员工推荐';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}
	public function numOfAct($db, $ymd)
	{
		return  \Rpt\EvtDaily\Base::getCopy('YebOutUserNumAll')->numOfAct($db, $ymd,null,['flgext02'=>3]);
	}
}
