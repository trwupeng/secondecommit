<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 融资档案
 * Class FKRongZiDangAn
 * @package Prj\Data
 */
class FKRongZiDangAn extends AFengKongFormat
{
    /**
     * 百分比的字段
     * @var array
     */
    public static $formatPercentageType = [];

    /**
     * 金钱字段，以分为单位
     * @var array
     */
    public static $formatMoneyType = [
        'jiekuanjinewanyuan' => 10000,
    ];

    /**
     * 时间类型的数据
     * @var array
     */
    public static $formatDateType = [
        'tazhengqishiri'        => '',
        'tazhengdaoqiri'        => '',
        'tazhengzhuxiaoshijian' => '',
        'createTime'            => '',
        'updateTime'            => '',
    ];

    /**
     * 字段类型为int、tinyint
     * @var array
     */
    public static $formatIntType = [
        'jiekuanjinewanyuan',
        'fangchanzhengliuzhi',
        'tazhengqishiri',
        'tazhengdaoqiri',
        'jiekuanhetong',
        'buchongxieyi',
        'zhuanzhangpingtiao',
        'qitaziliao',
        'tazhengzhuxiaoshijian',
        'createTime',
        'updateTime',
        'status',
    ];

    public static $formatEnumAttr = [
        'jiekuanhetong'      => [
            2 => ['style' => 'color:red'],
        ],
        'buchongxieyi'       => [
            2 => ['style' => 'color:red'],
        ],
        'zhuanzhangpingtiao' => [
            2 => ['style' => 'color:red'],
        ],
        'qitaziliao'         => [
            2 => ['style' => 'color:red'],
        ],
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

    public static function getCopy($k)
    {
        return parent::getCopy(['id' => $k]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_rongzidangan';
    }
}
