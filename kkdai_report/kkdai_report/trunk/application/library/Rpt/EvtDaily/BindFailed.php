<?php 
namespace Rpt\EvtDaily;
/**
 * 当日绑卡失败人数
 * 注意：这里的clientType 始终是 0
 * flg: -1 表示过去成功绑卡过了，0,1,2,3,4。。。表示注册后过了几天才绑定的
 */
class BindFailed extends Base{
	protected function actName(){return 'BindFailed';}
	public static function displayName(){return '绑卡失败人数';}

}