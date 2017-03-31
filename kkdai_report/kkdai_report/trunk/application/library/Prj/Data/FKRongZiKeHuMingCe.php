<?php
/***
 * @param 融资客户名册
 * @author wu.peng
 * 
 ***/
namespace Prj\Data;

class FKRongZiKeHuMingCe extends \Prj\Misc\AFengKongFormat
{
    
    //字段类型为int、tinyint
    public static $formatIntType = [
        0 => 'bianhao',
        1 => 'xingming',
        2 => 'weihuren',
        3 => 'guishuren',
        4 => 'yuanguishu',
        5 => 'daoqiriqi',
        6 => 'jieshaoren',
    ];
    
    public static $formatDateType = [
        'jieqingriqi' => '',
    ];
    
    public static $formatEnumAttr = [
        'zaibaoqingkuang' => [
            1 => ['style' => 'color:red']
        ],
       
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
    
    public static function getFieldForEnum($field)
    {
        $model = self::getCopy('');
        $ret = $model->db()->getPair($model->tbname(), 'id', $field);
        return $ret;
    }
    
    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_rongzikehumingce';
    }
}
