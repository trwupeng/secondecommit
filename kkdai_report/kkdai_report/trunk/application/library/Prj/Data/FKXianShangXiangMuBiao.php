<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 线上项目表
 * Class FKXianShangXiangMuBiao
 * @package Prj\Data
 * @author lingtm <lingtima@gmail.com>
 */
class FKXianShangXiangMuBiao extends AFengKongFormat
{
    /**
     * 百分比类型的字段
     * @var array
     */
    public static $formatPercentageType = [
        1 => 'nianlilv',
        2 => 'fuwufei',
    ];

    /**
     * 金钱类型的字段，以元为单位
     * @var array
     */
    public static $formatMoneyType = [
        'biaodijineyuan' => 1,
        'fuwufeiyuan' => 1,
        'shijidaozhangyuan' => 1,
        'toubiaojineyuan' => 1,
        'kehutoubiaojineyuan' => 1,
    ];

    /**
     * 整型的字段，例如int、tinyint
     * @var array
     */
    public static $formatIntType = [
    ];

    /**
     * 时间类型的数据
     * @var array
     */
    public static $formatDateType = [
        'shangbiaoriqi' => '',
        'fangkuanriqi' => '',
        'shouquriqi' => '',
        'daoqiriqi' => '',
        'createTime' => '',
        'updateTime' => '',
    ];

    public static $formatEnumAttr = [
        'jieqingqingkuang' => [
            2 => ['style' => 'color:red'],
        ],
    ];

    /**
     * 分页
     * @param \Sooh\DB\Pager $pager
     * @param array  $where
     * @param string $order
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
        return 'fk_xianshangxiangmubiao';
    }
}
