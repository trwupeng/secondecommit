<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/27
 * Time: 15:27
 */

namespace Prj\Data;

class HdUser  extends \Prj\Data\BaseFK {

    protected static $_pk = 'customerId'; //主键

    protected static $_tbname = 'hd_user'; //表名

    protected static $_host = 'manage'; //配置名

    public function getHP(){
        $hp = $this->getField('investHP') + $this->getField('normalHP');
        $hp = $hp > 10000 ? 10000 : $hp;
        return $hp;
    }
}
