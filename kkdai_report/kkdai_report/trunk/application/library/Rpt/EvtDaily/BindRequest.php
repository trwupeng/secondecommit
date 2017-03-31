<?php 
namespace Rpt\EvtDaily;
/**
 * 当日申请绑卡人数
 * 注意：这里的clientType 始终是 0
 * flg: -1 表示过去成功绑卡过了，0,1,2,3,4。。。表示注册后过了几天才绑定的
 */
class BindRequest extends Base{
	protected function actName(){return 'BindRequest';}
	public static function displayName(){return '申请绑卡总人数';}
	public function formula()
	{
		return ['BindOk','+','BindFailed'];
	}
	protected function basement(){return 1;}
}