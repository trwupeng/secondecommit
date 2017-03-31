<?php
namespace Rpt\EvtDaily;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/28 0028
 * Time: 下午 1:05
 */

class RegAndBindFailedByClient extends \Rpt\EvtDaily\Base{
    protected function actName() {return 'RegAndBindFailedByClient';}
    public static function  displayName() {return '注册绑卡失败人数';}
}