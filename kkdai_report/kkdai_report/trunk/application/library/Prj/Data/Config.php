<?php
namespace Prj\Data;
/**
 * User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Config  extends \Sooh\DB\Base\KVObj{

	public static function getCopy($k) {
		$tmp         = parent::getCopy(array('k' => $k));
		return $tmp;
	}

    public static function get($k){
        $tmp         = parent::getCopy(array('k' => $k));
        $tmp->load();
        if(!$tmp->exists())return '';
        return $tmp->getField('v');
    }

    protected static function splitedTbName($n, $isCache) {
        return 'tb_config';
    }
}
