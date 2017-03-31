<?php
namespace PrjCronds;
/**
 * 检查失败的订单，解冻相关金额
 *  php /var/www/fk_php/run/crond.php "__=crond/run&task=Standalone.CrondActivityDoubleEleven" 2>&1
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CrondActivityDoubleEleven extends \Sooh\Base\Crond\Task{
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
        error_log('###[warning]双十一活动排行榜开始###');
        $cache = \Prj\Misc\CacheFK::getCopy('activityDoubleEleven');
        $dateLine = ['2016-11-09','2016-11-27'];
        $rs = \Prj\Data\MiscData::getActivityDoubleElevenRecords($dateLine);
        $ret = $cache->save($rs);
        var_log($ret,'###[warning]拉取结果');
        error_log('###[warning]双十一活动排行榜结束###');
    }
}
