<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/2/16
 * Time: 17:43
 */

if (!defined('IN_DISCUZ')) {
	exit('Access denied');
}

class table_common_member_nickname extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'common_member_nickname';
		$this->_pk = 'uid';

		parent::__construct();
	}

	public function fetch_all_by_where($where, $start = 0, $limit = 0)
	{
		$where = $where ? ' WHERE ' . (string)$where : '';
		return DB::fetch_all('SELECT * FROM ' . DB::table($this->_table) . $where . ' ' . DB::limit($start, $limit));
	}

	public function fetch_by_uid($kkduid)
	{
		$ret = DB::fetch_first('SELECT * FROM ' . DB::table($this->_table) . ' WHERE ' . DB::field($this->_pk, $kkduid));
		if(!empty($ret)) {
			return $ret;
		} else {
			return '';
		}
	}
}