<?php
/**
 * 快快贷表中的一些状态及含义
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/11/28 0028
 * Time: 下午 4:21
 */
namespace Rpt;
class KkdStatus {
    public static $bid_status_of_bid= [
        4002 => '已申请',
        4003 => '提交',
        4004 => '已符合',
        4005 => '已终审',
        5000 => '标的初建',
        5001 => '新建',
        5002 => '招标中',
        5003 => '满标',
        5004 => '还款中',
        5005 => '标的还款已还清',
        5006 => '待发布',
        5007 => '流标处理中',
        5008 => '逾期',
        4011 => '流标',
    ];

    public static $bid_type_of_bid = [
        501 => '体验标',
        502 => '信用标',
        503 => '实地标-抵押标',
        504 => '抵押标',
        505 => '抵押标',
    ];

    public static $status_of_yuebao_poi= [
        0 => '等待支付',
        1 => '支付成功',
        2 => '支付失败',
        4 => '处理中',
        6 => '等待退款',
        7 => '退款成功',
        8 => '投标失败',
    ];

    public static $poi_status_of_bid_poi =[
        600 => '等待支付',
        601 => '支付成功',
        602 => '支付失败',
        603 => '放款成功',
        604 => '处理中',
        606 => '等待退款',
        607 => '退款成功',
        608 => '投标失败',
        609 => '流标',
        610 => '已还清',

    ];

    public static function  returnCodeName ($varname, $code) {
        if(isset(self::${$varname}[$code])) {
            return self::${$varname}[$code];
        }else {
            return $code;
        }

    }
}