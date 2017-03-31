<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/15
 * Time: 17:47
 */

namespace Prj\Misc;

class ManageLog {
    /*
    //记录日志
    */
    public $loginName = '';
    public $itemType = '';
    public $doType = '';
    public $data = '';
    public $itemId = '';

    public static function createLog($loginName,$itemType,$doType,$data = [],$itemId = 0){
        $o = new ManageLog();
        $o->loginName = $loginName;
        $o->itemType = $itemType;
        $o->doType = $doType;
        $o->data = $data;
        $o->itemId = $itemId;
        return $o;
    }

    public function save(){
        if(empty($this->loginName))throw new \ErrorException('用户名不能为空！');
        if(empty($this->itemType))throw new \ErrorException('单据类型不能为空！');
        if(empty($this->doType))throw new \ErrorException('操作方式不能为空！');
        $fields = [
            'itemType'=>$this->itemType,
            'doType'=>$this->doType,
            'data'=>$this->data,
            'itemId'=>$this->itemId,
        ];
        \Prj\Data\ManageLog::addLogs($fields,$this->loginName);
    }
}