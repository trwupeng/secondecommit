<?php
namespace Lib\Misc;
/**
 * ServiceController 专用异常类（记录data）
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ErrOnService extends \ErrorException{
	public $data;
	public static function throwWith($msg,$code,$data=null)
	{
		$err = new ErrOnService($msg,$code);
		$err->data = $data;
		throw $err;
	}
}
