<?php
/**
 * @param 线下本息费账
 * @author wu.peng
 * 
 * **/
namespace Prj\Data;

class FKXianXiaBenXiFeiZhang extends \Prj\Misc\AFengKongFormat
{
 
    /**
     * 百分比的字段
     * @var array
     */
    public static $formatPercentageType = [
        'yuqililv',
    ];
    
    /**
     * 金钱字段，以分为单位
     * @var array
     */
    public static $formatMoneyType = [
        'yingfujineyuan' => 1,
        'yifujineyuan' => 1,
        'qianfuyincang' => 1,
        'yuqifeiyuan' => 1,
        'qianfujineyuan' => 1,
    ];
    
    
    //字段类型为int、tinyint
    public static $formatIntType = [
        0 => 'rongzihetongbianhao',
        1 => 'jiekuanren',
        2 => 'yuqitianshu',
        3 => 'beizhu',
    ];
    
    public static $formatDateType = [
        'yingfushijian' => '',
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
    

    protected static function splitedTbName($n, $isCache)
    {
        return 'fk_xianxiabenxifeizhang';
    }
}
