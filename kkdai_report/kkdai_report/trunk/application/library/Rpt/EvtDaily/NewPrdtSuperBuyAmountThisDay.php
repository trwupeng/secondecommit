<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/17 0017
 * Time: 上午 9:43
 */

class NewPrdtSuperBuyAmountThisDay extends Base{
    protected function actName(){return 'NewPrdtSuperBuyAmountThisDay';}
    public static function displayName(){return '当日新标超级用户购买金额';}
    protected function basement() {return 100;}
}