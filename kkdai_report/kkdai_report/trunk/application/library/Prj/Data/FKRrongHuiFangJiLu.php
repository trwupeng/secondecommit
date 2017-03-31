<?php
/***
 * @param 融回访记录
 * @author wu.peng
 * 
 ***/
namespace Prj\Data;

class FKRrongHuiFangJiLu extends \Prj\Misc\AFengKongFormat
{
    
    //字段类型为int、tinyint
    public static $formatIntType = [
        0 => 'kehubianhao',
        1 => 'kehu',
        2 => 'huifangrenyuan',
        3 => 'huifangqingkuang',
    ];
    
    public static $formatDateType = [
        'huifangshijian' => '',
    ];
    
    /**
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

    public static function getCopy($k)
    {
        return parent::getCopy(['id' => $k]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_ronghuifangjilu';
    }
}
