<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/17 0017
 * Time: 上午 9:43
 */

class OldPrdtAmountLeft extends Base{
    protected function actName(){return 'OldPrdtAmountLeft';}
    public static function displayName(){return '旧标剩余金额';}
    public function formula()
    {
        return ['PrdtAmountOlder','-','OldPrdtSuperBuyAmountThisDay','-','OldPrdtCommonBuyAmountThisDay','-','OldPrdtVoucherUseAmountThisDay','-',
            'OldPrdtSuperBuyAmountBefore','-','OldPrdtCommonBuyAmountBefore', '-', 'OldPrdtVoucherUseAmountBefore'];
    }
}