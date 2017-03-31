<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/5 0005
 * Time: 下午 2:34
 */

class VoucherGrant extends Base {
    protected function actName(){return 'VoucherGrant';}
    public static function displayName(){return '券发放';}
    protected function basement() {return 100;}
}