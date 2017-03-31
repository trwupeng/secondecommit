<?php
namespace Rpt\EvtDaily;
/**
 *
 * 资金流向提现
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/27 0027
 * Time: 下午 4:05
 */

class YebFundFlowsAccount extends \Rpt\EvtDaily\Base {
    protected function actName() {return 'YebFundFlowsAccount';}
    public static function displayName(){return '存钱罐';}
    public function basement() {return 100;}
}