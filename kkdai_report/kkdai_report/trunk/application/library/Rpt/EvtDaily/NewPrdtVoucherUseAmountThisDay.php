<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/17 0017
 * Time: 上午 9:43
 */

class NewPrdtVoucherUseAmountThisDay extends Base{
    protected function actName(){return 'NewPrdtVoucherUseAmountThisDay';}
    public static function displayName(){return '当日新标红包使用额';}
    protected function basement() {return 100;}
}