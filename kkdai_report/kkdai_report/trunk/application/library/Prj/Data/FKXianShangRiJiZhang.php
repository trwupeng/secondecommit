<?php

namespace Prj\Data;

use \Prj\Misc\AFengKongFormat;

/**
 * 线上日记账
 * Class FKXianShangRiJiZhang
 * @package Prj\Data
 * @author  lingtm <lingtima@gmail.com>
 */
class FKXianShangRiJiZhang extends AFengKongFormat
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
        'qichuyueyuan'       => 1,
        'cunqianguanru'      => 1,
        'xianxiachongzhiru'  => 1,
        'qiyehuchongzhiru'   => 1,
        'haoyoufanxianru'    => 1,
        'shoudaofangkuaneru' => 1,
        'daoqibenjinru'      => 1,
        'daoqilixiru'        => 1,
        'diaopeizijinru'     => 1,
        'jiedongzijinru'     => 1,
        'tixianchu'          => 1,
        'shouxufeichu'       => 1,
        'zhuanzhangzijinchu' => 1,
        'dongjiezijinchu'    => 1,
        'zhifubenxichu'      => 1,
        'zhifutoubiaochu'    => 1,
        'qimoyueyuan'        => 1,
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
        'riqi'       => '',
        'createTime' => '',
        'updateTime' => '',
    ];

    /**
     * 显示在表单上需要其他参数的字段
     * @var array
     */
    public static $formatEnumAttr = [
        'zhonglei' => [
            1 => ['style' => 'color:red',],
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

    public static function getTbName()
    {
        return 'db_kkrpt.' . self::splitedTbName(0, true);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_xianshangrijizhang';
    }
}
