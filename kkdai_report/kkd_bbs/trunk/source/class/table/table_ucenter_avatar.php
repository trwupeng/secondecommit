<?php

if (!defined('IN_DISCUZ')) {
	exit('Access denied');
}

class table_ucenter_avatar extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'ucenter_avatar';

		parent::__construct();
	}

	public function insert($uid, $url = '', $type = 1, $status = 1)
	{
		$data = [
			'uid' => $uid,
		    'url' => $url,
		    'type' => $type,
		    'status' => $status,
		];

		parent::insert($data, false, false);
		return true;
	}

	public function update($uid, $field, $value)
	{
		if (in_array($field, ['url', 'status', 'type'])) {
			$data = [$field => $value];
			DB::update($this->_table, $data, ['uid' => intval($uid)], 'UNBUFFERED');
			$this->update_cache($uid, $data);
		}
	}

	public function fetch_by_uid($uid, $field = '*')
	{
		if (intval($uid)) {
			if ($field == '*') {
				$ret = DB::result_all('SELECT * FROM %t WHERE uid = %n', [$this->_table, $uid]);
			} else {
				$ret = DB::result_first('SELECT ' . "{$field}" . ' FROM pre_ucenter_avatar WHERE uid = ' . "'{$uid}'");
			}
		}
		return $ret;
	}
}