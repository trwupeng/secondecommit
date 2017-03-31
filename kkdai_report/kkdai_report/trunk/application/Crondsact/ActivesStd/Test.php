<?php
namespace PrjCronds;
/**
 * 检查失败的订单，回复本金券状态
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Test extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=60;//每5分钟启动一次
		$this->_iissStartAfter=0;//每小时02分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		
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
		return true;
	}
}
