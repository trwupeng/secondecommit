<?php
namespace Lib\Services;
/**
 * Description of SessionStorage
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class SessionStorage extends \Sooh\Base\Session\Storage{
	public static function setStorageIni($dbGrpId='session',$numSplit=1)
	{
		\Sooh\DB\Cases\SessionStorage::$__id_in_dbByObj=$dbGrpId;
		\Sooh\DB\Cases\SessionStorage::$__nSplitedBy=$numSplit;
	}
}
