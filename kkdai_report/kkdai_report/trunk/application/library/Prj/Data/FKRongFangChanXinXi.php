<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 融房产信息
 * Class FKRongFangChanXinXi
 * @package Prj\Data
 * @author  lingtm <lingtima@gmail.com>
 */
class FKRongFangChanXinXi extends AFengKongFormat
{
    /**
     * 百分比类型的字段
     * @var array
     */
    public static $formatPercentageType = [
        1 => 'diyalv',
    ];

    /**
     * 金钱类型的字段，以元为单位
     * @var array
     */
    public static $formatMoneyType = [
        'pingguzhiwanyuan' => 10000,
        'yinhangdiyae'     => 1,
        'yinhangshengyue'  => 1,
        'jiekuane'         => 1,
    ];

    /**
     * 时间类型的数据
     * @var array
     */
    public static $formatDateType = [
        'pinggushijian'         => '',
        'jiekuandaoqishijian'   => '',
        'chandiaochaxunshijian' => '',
        'xiacichaxunshijian'    => '',
        'createTime'            => '',
        'updateTime'            => '',
    ];

    /**
     * 整型的字段，例如int、tinyint
     * @var array
     */
    public static $formatIntType = [
        'id',
        'fangchanquyu',
        'fangchanleixing',
        'shifouxuweihu',
        'shifoudiya',
        'pingguzhiwanyuan',
        'pinggushijian',
        'yinhangdiyae',
        'yinhangshengyue',
        'jiekuane',
        'diyalv',
        'jiekuandaoqishijian',
        'chandiaochaxunshijian',
        'xiacichaxunshijian',
        'createTime',
        'updateTime',
        'status',
    ];

    public static $formatDoubleType = [
        'mianji' => 2,
    ];

    public static $formatEnumAttr = [
        'shifouxuweihu' => [
            1 => ['style' => 'color:red'],
        ],
        'shifoudiya'    => [
            1 => ['style' => 'color:red'],
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
        return 'fk_rongfangchanxinxi';
    }
}
