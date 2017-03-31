<?php
namespace Rpt\Misc;

class CrondGather extends \Sooh\Base\Crond\Task{
	public function init() {
		$this->toBeContinue = true;
		$this->ret = new \Sooh\Base\Crond\Ret;
		$this->ret->newadd = 0;
		$this->ret->newupd = 0;
	}
	protected $ymd;
	protected $dt;
	protected function onRun($dt) {
		$this->dt = $dt;
		if($this->_isManual){
			$this->toBeContinue = false;
			$this->ymd = $dt->YmdFull;
			$this->his = $dt->his;
			$ret = $this->gather();
		}else{
			$this->toBeContinue=false;
			switch($dt->hour){
				case 0:
					$this->ymd = date('Ymd', $dt->timestamp(-2));
					$ret=$this->gather();
					break;
				case 1:
					$this->ymd = date('Ymd', $dt->timestamp(-1));
					$ret=$this->gather();
					break;
				case 2:
					$this->ymd = date('Ymd', $dt->timestamp(-4));
					$ret = $this->gather();
					break;
				case 3:
					$this->ymd = date('Ymd', $dt->timestamp(-10));
					$ret=$this->gather();
					break;
				case 4:
					$this->lastMsg='skip';
					return true;
				default:
					$this->toBeContinue=false;
					$this->ymd = $dt->YmdFull;
					$ret=$this->gather();
					break;
			}
		}
		
		$this->lastMsg = $this->ret->toString();
		return $ret;
	}
	protected function gather(){}
	
}