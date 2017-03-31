<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class User  extends \Sooh\DB\Base\KVObj{
	const errUserNotExist = '用户不存在';

	public $userId;

	/**
	 * 分页
	 * @param \Sooh\DB\Pager $pager  pagerClass
	 * @param array          $where  条件数组
	 * @param null           $order  排序条件
	 * @param string         $fields select fields
	 * @return array
	 */
	public static function paged($pager, $where = [], $order = null, $fields) {
		$sys = self::getCopy('');
		$db  = $sys->db();
		$tb  = $sys->tbname();

		$maps = [

		];
		$maps = array_merge($maps, $where);
		$pager->init($db->getRecordCount($tb, $maps), -1);

		if (empty($order)) {
			$order = 'rsort ymdReg';
		} else {
			$order = str_replace('_', ' ', $order);
		}

		$rs = $db->getRecords($tb, $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
		return $rs;
	}

	/**
	 * 获取符合条件的记录条数
	 * @param array $where 查询条件
	 * @return mixed
	 */
	public static function getCount($where) {
		return static::loopGetRecordsCount($where);
	}

	protected static function idFor_dbByObj_InConf($isCache) {
		return 'user' . ($isCache ? 'Cache' : '');
	}


	protected static function splitedTbName($n, $isCache) {
		return 'tb_user_' . ($n % static::numToSplit());
	}

	public static function getCopy($userId) {
		$tmp         = parent::getCopy(array('userId' => $userId));
		$tmp->userId = $userId;
		return $tmp;
	}
}
