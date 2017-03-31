<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/17 0017
 * Time: 上午 9:43
 */

class OldPrdtSuperBuyAmountThisDay extends Base{
    protected function actName(){return 'OldPrdtSuperBuyAmountThisDay';}
    public static function displayName(){return '当日旧标超级用户购买金额';}
    protected function basement() {return 100;}
}