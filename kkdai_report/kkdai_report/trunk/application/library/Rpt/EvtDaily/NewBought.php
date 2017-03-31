<?php
namespace Rpt\EvtDaily;
/**
 * 新增购买人数
 * 
 * 第一次购买（包括新用户老用户 ）
 * 
 */
class NewBought extends Base{
	protected function actName(){return 'NewBought';}
	public static function displayName(){return '新增购买人数';}
}