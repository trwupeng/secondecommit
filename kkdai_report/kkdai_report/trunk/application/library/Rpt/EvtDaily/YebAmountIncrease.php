<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/16 0016
 * Time: 下午 2:07
 */

class YebAmountIncrease extends Base{
    protected function actName(){return 'YebAmountIncrease';}
    public static function displayName(){return '天天赚净增总额';}
    public function formula()
    {
        return explode(' ','YebBoughtAmountEmployee + YebBoughtAmountExternal + YebBoughtAmountInviter - YebOutAmountAll');
    }
}
