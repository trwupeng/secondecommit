<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */

namespace Prj\Data;

class HdRedLog  extends \Prj\Data\BaseFK {

    protected static $_pk = 'id'; //主键

    protected static $_tbname = 'hd_red_log'; //表名

    protected static $_host = 'manage'; //配置名

    public static function add($customerId , $amount , $hp , $investAmount , $hplogid ){
        $max = 10;
        do{
            $tmp = self::getCopy(time().mt_rand(1000,9999));
            $tmp->load();
            $retry = $tmp->exists() ? true : false;
            $max --;
        }while($retry && $max>0);
        $tmp->setField('customerId',$customerId);
        $tmp->setField('amount',$amount);
        $tmp->setField('hp',$hp);
        $tmp->setField('investAmount',$investAmount);
        $tmp->setField('hplogid',$hplogid);
        $tmp->setField('createYmd',date('YmdHis'));
        $tmp->setField('statusCode',-1);
        $tmp->setField('exp','初始化,等待操作...');
        try{
            $tmp->update();
            return $tmp;
        }catch (\ErrorException $e){
            return null;
        }
    }

}
