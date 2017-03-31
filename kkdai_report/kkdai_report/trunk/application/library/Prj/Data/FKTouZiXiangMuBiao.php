<?php
/**
 * 投资项目表
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/19 0014
 * Time: 上午 11:01
 */
namespace Prj\Data;
class FKTouZiXiangMuBiao extends \Prj\Misc\AFengKongFormat {

    public static function getCopy($pKey) {
        return parent::getCopy(['id'=>$pKey]);
    }

    protected static function splitedTbName($n, $isCache){
        return 'fk_touzixiangmubiao';
    }

    public static function getFieldForEnum($field)
    {
        $model = self::getCopy('');
        $ret = $model->db()->getPair($model->tbname(), 'id', $field);
        return $ret;
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

    public static $formatMoneyType = [
        'touziewanyuan'     => 10000,
        'yingfuyuan'        =>1,
        'zongeyuan'         =>1,
        'tichengyuan'       =>1,
        'tichengzongeyuan'  =>1,
    ];

    /** 字段名 => [存库的格式, 展示的格式];
     * @var array
     */
    public static $formatDateType = [
        'qishiriqi'=>['Ymd',''],
        'daoqiriqi'=>['Ymd',''],
    ];

    public static $formatIntType = [
//        'touziewanyuan',
//        'yingfuyuan',
//        'zongeyuan',
//        'tichengyuan',
//        'tichengzongeyuan',
//        'qishiriqi',
//        'daoqiriqi',
//        'jieqingqingkuang',
        'yue',
        'tian',
    ];

    public static $logicFields = [
        'jieqingqingkuang'=>[1=>'未结清',2=>'已结清'],
    ];

    public static $formatEnumAttr = [
        'jieqingqingkuang' => [
            1 => ['style' => 'color:red']
        ],
    ];

    public static $formatPercentageType = [
        'ticheng','yuexi',
    ];

    public static $requeredFields = [
        'touzihetongbianhao'
    ];
//    public static function fangkuanren () {
//        $model = self::getCopy();
//        $db = $model->db();
//        $tb = $model->tbname();
//        return $db->getPair($tb, 'id', ['fangkuanren']);
//    }
}