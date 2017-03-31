<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 预留返还账
 * Class FKYuLiuFanHuanZhang
 * @package Prj\Data
 * @author  lingtm <lingtima@gmail.com>
 */
class FKYuLiuFanHuanZhang extends AFengKongFormat
{
    /**
     * 百分比类型的字段
     * @var array
     */
    public static $formatPercentageType = [
        1 => '',
    ];

    /**
     * 金钱类型的字段，以元为单位
     * @var array
     */
    public static $formatMoneyType = [
        'gerenyuliu'     => 1,
        'meiqifafang'    => 1,
        'jingliyuliu'    => 1,
        'meiqifafang258' => 1,
        'zongjianyuliu'  => 1,
        'meiqifafang261' => 1,
        'cunqianguanru'  => 1,
    ];

    /**
     * 整型的字段，例如int、tinyint
     * @var array
     */
    public static $formatIntType = [
        'yingfaqishu',
        'yifaqishu',
        'xiaciyingfayuefen',
    ];

    /**
     * 时间类型的数据
     * @var array
     */
    public static $formatDateType = [
        'fangkuanshijian' => '',
        'createTime'      => '',
        'updateTime'      => '',
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
        return 'fk_yuliufanhuanzhang';
    }
}
