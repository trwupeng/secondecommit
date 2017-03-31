<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/5 0005
 * Time: 下午 2:34
 */

class VoucherNUsed extends Base {
    protected function actName(){return 'VoucherNUsed';}
    public static function displayName(){return '当日发放券使用';}
    protected function basement() {return 100;}
}