<?php
namespace PrjCronds;
/**
 * 检查失败的订单，解冻相关金额
 *  php /var/www/fk_php/run/crond.php "__=crond/run&task=Standalone.CrondLuoDiYeTongJi" 2>&1
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CrondLuoDiYeTongJi extends \Sooh\Base\Crond\Task{
    public function init() {
        parent::init();
        $this->toBeContinue=true;
        $this->_secondsRunAgain=1800;//每60分钟启动一次
        $this->_iissStartAfter=100;//每小时01分后启动
        $this->ret = new \Sooh\Base\Crond\Ret();
    }
    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        isset($dt);
        if($this->_isManual){
            $m='manual';
        }else{
            $m='auto';
        }
        if($this->_counterCalled==1){
            error_log("[TRace]".__CLASS__.'# first by '.$m.' #'.$this->_counterCalled);
        }else{
            error_log("[TRace]".__CLASS__.'# continue by '.$m.' #'.$this->_counterCalled);
        }
        $this->lastMsg = $this->ret->toString();//要在运行日志中记录的信息
        $this->updateData();
        $this->toBeContinue = false;
        return true;
    }

    protected function updateData(){
        error_log('###[warning]落地页统计###');
        $data = \Prj\Data\MiscData::getLuodiyeTotal();
        $tmp = [];
        foreach($data as $v){
            $tmp[$v['title']] = $v['total'] - 0;
            try{
                \Prj\Data\Temp::set($v['title'],$v['total'] - 0,$v['exp']);
            }catch (\ErrorException $e){
                error_log('落地页统计ERROR:'.$e->getMessage());
            }
        }
        \Prj\Data\Temp::get('xxx');
        var_log($tmp,'records>>>');
        error_log('###[warning]落地页统计###');
    }
}
