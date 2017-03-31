<?php
namespace Prj\Misc;
/**
 * 一些事件
 *
 * @author simon.wang
 */
class Evt {
	/**
	 * app启动时获取系统消息
	 */
	public static function msgOnAppStart()
	{
		$msg = file_get_contents(__DIR__.'/txt/'.__FUNCTION__.'.txt');
		return $msg;
	}
}
