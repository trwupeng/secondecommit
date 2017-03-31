<?php
/**
 * 风控经理名册
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/17 0014
 * Time: 上午 15:01
 */
namespace Prj\Data;
class FKXianXiaRiJiZhang extends  \Prj\Misc\AFengKongFormat {

    public static function getCopy($pKey) {
        return parent::getCopy(['id'=>$pKey]);
    }

    protected static function splitedTbName($n, $isCache){
        return 'fk_xianxiarijizhang';
    }


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


    public static $formatIntType = [
        'zhonglei','zhanghu'
    ];

    public static $logicFields = [
        'zhonglei' => [1=>'收', 2=>'支'],
    ];

    public static $formatDateType = [
        'riqi' => ['',''],
    ];

    public static $formatMoneyType = [
        'jineyuan' => 1,

    ];

    public static $formatEnumAttr = [
        'zhonglei'=> [
            2=>['style'=>'color:red']
        ],
    ];

}