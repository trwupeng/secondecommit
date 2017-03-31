<?php
namespace Prj\Misc;
/**
 * 错误日志
 *
 * @author simon.wang
 */
class Logerror {
	const usedForError=1;
	const usedForTrace=2;
	private $fullname;
	public function __construct($type) {
		$tmp  = \Sooh\Base\Ini::getInstance()->get('logerror');
		if(empty($tmp)){
			$this->fullname = '/var/www/logs/prj_log.txt';
		}else{
			$this->fullname=$tmp[$type][0];
		}
	}
	public function write($logData)
	{
		$arr = $logData->toArray();
		$ret = array('ret'=>$arr['ret']);
		unset($arr['ret']);
		$arr = array_merge($ret,$arr);
		if($this->fullname==='/'){
			error_log(json_encode($arr));
		}else{
			file_put_contents($this->fullname, '['.date('m-d H:i:s').']'.json_encode($arr), FILE_APPEND);
		}
		//TODO: 通过udp把错误进一步通知下去
	}
	public function free()
	{
		
	}
}
