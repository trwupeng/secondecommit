<?php
/**
 * 客户经理名册
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/14 0014
 * Time: 上午 11:01
 */
namespace Prj\Data;
class FKKeHuJingLiMingCe extends \Prj\Misc\AFengKongFormat {

    public static function getCopy($pKey) {
        return parent::getCopy(['id'=>$pKey]);
    }

    protected static function splitedTbName($n, $isCache){
        return 'fk_kehujinglimingce';
    }

    public static $formatIntType = [
        'zaizhiqingkuang',
        'jibie',
    ];


    public static $logicFields = [
        'zaizhiqingkuang'=>[1=>'是',2=>'否'],
        'jibie'=>[1=>'专员',2=>'经理', 3=>'总监'],
    ];

    public static $formatEnumAttr = [
        'zaizhiqingkuang' => [
            2 => ['style' => 'color:red']
        ],
    ];

    /**
     * @param $pager
     * @param array $where
     * @param string $order
     * @return mixed
     */
    public static function paged($pager, $where=[], $order='') {
        $m = self::getCopy();
        $db = $m->db();
        $tb = $m->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb, '*', $where, $order, $pager->page_size, $pager->rsFrom());
    }

    public static function kehujingli () {
        $model = self::getCopy();
        $db = $model->db();
        $tb = $model->tbname();
        return $db->getPair($tb, 'id', 'xingming');
    }

    public static function getFieldForEnum($field)
    {
        $model = self::getCopy('');
        $ret = $model->db()->getPair($model->tbname(), 'id', $field);
        return $ret;
    }
}