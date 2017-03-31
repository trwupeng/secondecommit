<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */

namespace Prj\Data;

class HdHPLog  extends \Prj\Data\BaseFK {

    protected static $_pk = 'id'; //主键

    protected static $_tbname = 'hd_hp_log'; //表名

    protected static $_host = 'manage'; //配置名

    const type_collect = 'collect';
    const type_award = 'award';

    public static function add($customerId , $old , $add , $type = 'collect'){
        $max = 10;
        do{
            $tmp = self::getCopy(time().mt_rand(1000,9999));
            $tmp->load();
            $retry = $tmp->exists() ? true : false;
            $max --;
        }while($retry && $max>0);
        $tmp->setField('customerId',$customerId);
        $tmp->setField('oldHP',$old);
        $tmp->setField('addHP',$add);
        $tmp->setField('type',$type);
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
