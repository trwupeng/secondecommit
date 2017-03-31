<?php
namespace Rpt\EvtDaily;
/**
 * 余额宝转出
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class YebOutUserNumExternal extends Base{
	protected function actName(){return 'YebOutUserNumExternal';}
	public static function displayName(){return '外部用户';}
	public function divisor () { return 100;}
	protected function basement() {return 100;}
	public function numOfAct($db, $ymd)
	{
		return  \Rpt\EvtDaily\Base::getCopy('YebOutUserNumAll')->numOfAct($db, $ymd,null,['flgext02'=>0]);
	}
}
