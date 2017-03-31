<?php
/**
 * 流程单
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/17 0014
 * Time: 上午 15:01
 */
namespace Prj\Data;
class FKLiuChengDan extends \Prj\Misc\AFengKongFormat {

    public static function getCopy($pKey) {
        return parent::getCopy(['id'=>$pKey]);
    }

    protected static function splitedTbName($n, $isCache){
        return 'fk_liuchengdan';
    }


    /**
     * @param $pager
     * @param array $where
     * @param string $order
     * @return mixed
     */
    public static function paged($pager, $where=[], $order='') {
        $m = self::getCopy();
        $db = $m->db();
        $tb = $m->tbname();
        $pager->init($db->getRecordCount($tb, $where), -1);
        return $db->getRecords($tb, '*', $where, $order, $pager->page_size, $pager->rsFrom());
    }

    public static $formatIntType = [
//        'pipeizijinqingkuang',
//        'xianxiayifangkuan',
//        'xianshangyifangkuan',
//        'jibenxinxiyilu',
//        'xiangxixinxiyilu',
//        'tijiangyihesuan',
//        'xiangmuyiguidang',
//        'yishenhe',
    ];

    public static $formatDateType = [
        'fangkuanshijian'=>['Ymd', ''],
        'fangkuanshijian8'=>['Ymd', ''],
        'fangkuanshijian10'=>['Ymd', ''],
        'chulishijian'=>['Ymd', ''],
        'chulishijian14'=>['Ymd', ''],
        'chulishijian16'=>['Ymd', ''],
        'chulishijian18'=>['Ymd', ''],
        'chulishijian20'=>['Ymd', ''],
    ];
    public static $formatEnumAttr = [
        'pipeizijinqingkuang' => [
            2 => ['style' => 'color:red']
        ],
    ];
    public static $requeredFields = [
        'hetongbianhao'
    ];

    public static  $logicFields = [
        'pipeizijinqingkuang'   =>[1=>'已匹配', 2=>'未匹配'],
        'xianxiayifangkuan'     =>[1=>'是', 2=>'否'],
        'xianshangyifangkuan'   =>[1=>'是', 2=>'否'],
        'jibenxinxiyilu'        =>[1=>'是', 2=>'否'],
        'xiangxixinxiyilu'      =>[1=>'是', 2=>'否'],
        'tijiangyihesuan'       =>[1=>'是', 2=>'否'],
        'xiangmuyiguidang'      =>[1=>'是', 2=>'否'],
        'yishenhe'              =>[1=>'是', 2=>'否'],
    ];
}