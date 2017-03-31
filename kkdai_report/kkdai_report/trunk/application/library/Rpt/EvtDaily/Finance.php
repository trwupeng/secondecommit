<?php 
namespace Rpt\EvtDaily;
/**
 * 当日申请绑卡人数
 * flg: -1 表示过去成功绑卡过了，0,1,2,3,4。。。表示注册后过了几天才绑定的
 */
class Finance extends Base{
	protected function actName(){return 'Finance';}
	public static function displayName(){return '财务存量汇总';}
	public function formula()
	{
		return ['FinanceK','+','FinanceM','+','FinanceXS','+','FinanceXX'];
	}
}