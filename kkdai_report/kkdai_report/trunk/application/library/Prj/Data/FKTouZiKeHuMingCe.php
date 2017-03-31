<?php

/***
 * @param 投资客户名册
 * @author wu.peng
 * 
 ***/
namespace Prj\Data;

class  FKTouZiKeHuMingCe extends \Prj\Misc\AFengKongFormat
{
  
    /**
     * 字段类型为int、tinyint
     * @var array
     */
    public static $formatIntType = [
        0 => 'xingming',
        1 => 'zhengjianhaoma',
        2 => 'lianxidianhua',
        3 => 'yinhangzhanghao',
        4 => 'kaihuxingxinxi',
        5 => 'jiatingzhuzhi',
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
        return 'fk_touzikehumingce';
    }
}
