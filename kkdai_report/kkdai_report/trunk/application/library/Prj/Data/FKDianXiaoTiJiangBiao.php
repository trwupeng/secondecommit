<?php
/**
 * @param 线下本息费账
 * @author wu.peng
 * 
 * **/
namespace Prj\Data;

class FKDianXiaoTiJiangBiao extends \Prj\Misc\AFengKongFormat
{
 
    /**
     * 百分比的字段
     * @var array
     */
    public static $formatPercentageType = [
        'tijiangbili',
        'tijiangbili284',
        'tijiangbili287',
        'tijiangbili290',
        'tijiangbili293',
    ];
    
    /**
     * 金钱字段，以分为单位
     * @var array
     */
    public static $formatMoneyType = [
        'rongzijinewanyuan' => 10000,
        'shoufeijinewanyuan' => 10000,
        'dangyueheji' => 10000,
        'tijiangjine' => 1,
        'tijiangjine285' => 1,
        'tijiangjine288' => 1,
        'tijiangjine291' => 1,
        'tijiangjine294' => 1,
    ];
    
    
    //字段类型为int、tinyint
    public static $formatIntType = [
        0 => 'yewubianhao',
        1 => 'kehuxingming',
        2 => 'hezuoyinhang',
        3 => 'bumenjingli',
    ];
    
    public static $formatDateType = [
        'shoufeiriqi' => '',
       
    ];
    
    public static $formatEnumAttr = [
        'yewuleixing' => [
            1 => ['style' => 'color:red']
        ],
         
    ];
    
    
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
        return 'fk_dianxiaotijiangbiao';
    }
}
