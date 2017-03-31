<?php
/**
 * 融人企信息
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/17 0014
 * Time: 上午 14:01
 */
namespace Prj\Data;
class FKRongRenQiXinxi extends \Prj\Misc\AFengKongFormat {

    public static function getCopy($pKey) {
        return parent::getCopy(['id'=>$pKey]);
    }

    protected static function splitedTbName($n, $isCache){
        return 'fk_rongrenqixinxi';
    }

    /**
     * 类型
     * @var array
     */

    public static $logicFields = [
        'leixing' => [1=>'人员',2=>'企业'],
    ];

    public static $formatEnumAttr = [
        'leixing' => [
            2 => ['style' => 'color:red']
        ],
    ];

    public static $formatDateType = [
        'beizhixingchaxunshijian' => ['',''],
        'zhengxinchaxunshijian' => ['',''],
    ];
    public static $formatIntType = [
        'leixing','nianling'
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
}