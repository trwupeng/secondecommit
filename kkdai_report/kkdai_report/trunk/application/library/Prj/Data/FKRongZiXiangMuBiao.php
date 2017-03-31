<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 融资项目
 * Class FKRongZiXiangMuBiao
 * @package Prj\Data
 */
class FKRongZiXiangMuBiao extends AFengKongFormat
{
    /**
     * 百分比的字段
     * @var array
     */
    public static $formatPercentageType = [
        'lixiyue',
        'zhongjiefeiyue',
        'zhongjiefeizong',
        'zongheyue',
        'yuexifeilv',
        'baozhengjin',
    ];

    public static $formatDecimalType = [
        'fuwufeianyue' => 1,
        'fuwufeianyueyingshou' => 1,
        'fuwufeiyicixingyue' => 1,
        'lixiyingshou' => 1,
    ];

    /**
     * 金钱字段，以分为单位
     * @var array
     */
    public static $formatMoneyType = [
        'jiekuanewanyuan'         => 10000,
        'lixiyingshouyuan'        => 1,
        'lixishishouyuan'         => 1,
        'fuwufeiyingshouyuan'     => 1,
        'fuwufeishishouyuan'      => 1,
        'dianzifeijineyuan'       => 1,
        'zhongjiefeiyingshouyuan' => 1,
        'zhongjiefeishishouyuan'  => 1,
        'baozhengjinyuan'         => 1,
    ];


    /**
     * 字段类型为int、tinyint
     * @var array
     */
    public static $formatIntType = [
        'bianhao',
        'jiekuanewanyuan',
        'qishiriqi',
        'yue',
        'tian',
        'daoqiriqi',
        'lixiyue',
        'lixiyingshouyuan',
        'lixishishouyuan',
        'fuwufeiyingshouyuan',
        'fuwufeishishouyuan',
        'dianzifeijineyuan',
        'dianzifeibilv',
        'zhongjiefeiyue',
        'zhongjiefeizong',
        'zhongjiefeiyingshouyuan',
        'zhongjiefeishishouyuan',
        'zongheyue',
        'yuexifeilv',
        'baozhengjin',
        'baozhengjinyuan',
        'gongzheng',
        'quanweidaoqiri',
        'jieqingqingkuang',
        'jieqingriqi',
    ];

    public static $formatDateType = [
        'qishiriqi'      => '',
        'daoqiriqi'      => '',
        'quanweidaoqiri' => '',
        'jieqingriqi'    => '',
    ];

    public static $formatEnumAttr = [
        'gongzheng'        => [
            3 => ['style' => 'color:red'],
        ],
        'jieqingqingkuang' => [
            2 => ['style' => 'color:red'],
        ],
    ];

    public static $formatSelectsAttr = [
        'fengkongjingli',
    ];

    /**
     * @param \Sooh\DB\Pager $pager
     * @param array          $where
     * @param string         $order
     * @return mixed
     */
    public static function paged($pager, $where = [], $order = '')
    {
        $model = self::getCopy('');
        $db = $model->db();
        $tb = $model->tbname();

        $pager->init($db->getRecordCount($tb, $where), -1);

        return $db->getRecords($tb, '*', $where, $order, $pager->page_size, $pager->rsFrom());
    }

    public static function getFieldForEnum($field)
    {
        $model = self::getCopy('');
        $ret = $model->db()->getPair($model->tbname(), 'id', $field);
        return $ret;
    }

    public static function getCopy($k)
    {
        return parent::getCopy(['id' => $k]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_rongzixiangmubiao';
    }
}
