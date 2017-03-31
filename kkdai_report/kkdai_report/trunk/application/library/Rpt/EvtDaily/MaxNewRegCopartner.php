<?php
namespace Rpt\EvtDaily;
/**
 * 指定日期，新注册用户人数最多的渠道
 * @author yixiu
 *
 */

class MaxNewRegCopartner extends Base {
	protected function actName(){return 'MaxNewRegCopartner';}
	public static function displayName(){return '新增注册人数最多渠道';}
	public function formula() {
		return 'NewRegister';
	}
	public function maxNumGrpBy($db, $ymd) {
		$actname = $this->formula()?$this->formula():$this->actName();
		$where = array('ymd='=>$ymd, 'act'=>$actname);
		$fields = array('copartnerId', 'sum(n) as sum');
		
		$rs = $db->getRecord(self::$tb, $fields, $where, 'groupby copartnerId rsort sum', 1);
		return $rs;
	}
}