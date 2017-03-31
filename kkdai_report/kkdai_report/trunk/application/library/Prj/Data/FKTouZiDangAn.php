<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 投资档案
 * Class FKTouZiDangAn
 * @package Prj\Data
 * @author  lingtm <lingtima@gmail.com>
 */
class FKTouZiDangAn extends AFengKongFormat
{
    /**
     * 百分比类型的字段
     * @var array
     */
    public static $formatPercentageType = [
        1 => 'yuexi',
    ];

    /**
     * 金钱类型的字段，以元为单位
     * @var array
     */
    public static $formatMoneyType = [
        'touziewanyuan' => 10000,
    ];

    /**
     * 整型的字段，例如int、tinyint
     * @var array
     */
    public static $formatIntType = [
        'id',
        'touziewanyuan',
        'qishiriqi',
        'yue',
        'tian',
        'daoqiriqi',
        'fangkuanrenyinhang',
        'yuexi',
        'touzihetong',
        'haikuanmingxibiao',
        'zhuanzhangpingtiao',
        'haikuanzhuanzhangpingzheng',
        'taxiangquanzheng',
        'createTime',
        'updateTime',
        'status',
    ];

    /**
     * 时间类型的数据
     * @var array
     */
    public static $formatDateType = [
        'qishiriqi'  => '',
        'daoqiriqi'  => '',
        'createTime' => '',
        'updateTime' => '',
    ];

    public static $formatEnumAttr = [
        'touzihetong'                => [
            2 => ['style' => 'color:red'],
        ],
        'haikuanmingxibiao'          => [
            2 => ['style' => 'color:red'],
        ],
        'zhuanzhangpingtiao'         => [
            2 => ['style' => 'color:red'],
        ],
        'haikuanzhuanzhangpingzheng' => [
            2 => ['style' => 'color:red'],
        ],
        'taxiangquanzheng'           => [
            1 => ['style' => 'color:red'],
        ],
        'jieqingzhuangkuang'=>[
            2 => ['style' => 'color:red'],
        ],
    ];

    /**
     * 分页
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
        return 'fk_touzidangan';
    }
}
