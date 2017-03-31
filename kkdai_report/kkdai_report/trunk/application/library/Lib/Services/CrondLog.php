<?php
namespace Lib\Services;
/**
 * 计划任务日志
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class CrondLog {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return CheckinBook
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			self::$_instance = new CrondLog;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;

	protected $tbname;

	/**
	 * 写txt-log
	 * @param type $taskid
	 * @param type $msg
	 */
	public function writeCrondLog($taskid,$msg)
	{
		error_log("\tCrond ".  getmypid()."#\t$taskid\t$msg");
	}
	/**
	 * 更新db里记录的每个小时执行的状态
	 * @param type $taskid
	 * @param type $msg
	 */
	public function updCrondStatus($ymd,$hour,$taskid,$lastStatus,$isOkFinal,$isManual=0)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('ymd'=>$ymd,'hour'=>$hour,'taskid'=>$taskid,'lastStatus'=>$lastStatus,'isOkFinal'=>$isOkFinal,'isManual'=>$isManual))->send(__FUNCTION__);
		}else{
			$db = $this->getDB();
			try{
				if(strlen($lastStatus)>250){
					error_log('updCrondStatus_msgTooLong:'.$lastStatus);
					$lastStatus = substr($lastStatus,0,250)."...";
				}
				\Sooh\DB\Broker::errorMarkSkip();
				\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
				$db->addRecord($this->tbname, array('ymdh'=>$ymd*100+$hour,'taskid'=>$taskid,'lastStatus'=>$lastStatus,'ymdhis'=>date('YmdHis'),'lastRet'=>$isOkFinal,'isManual'=>$isManual));
			} catch (\ErrorException $e) {
				if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
					$this->ensureCrondTable();
					$db->addRecord($this->tbname, array('ymdh'=>$ymd*100+$hour,'taskid'=>$taskid,'lastStatus'=>$lastStatus,'ymdhis'=>date('YmdHis'),'lastRet'=>$isOkFinal,'isManual'=>$isManual));
				}elseif(\Sooh\DB\Broker::errorIs($e)){
					$db->updRecords($this->tbname, array('lastStatus'=>$lastStatus,'ymdhis'=>date('YmdHis'),'lastRet'=>$isOkFinal,'isManual'=>$isManual),array('ymdh'=>$ymd*100+$hour,'taskid'=>$taskid,));
				}else {
					throw $e;
				}
			}
		}
	}
	
	protected function getDB()
	{
		$dbid='crondForLog';
//		$pre = \Sooh\Base\Ini::getInstance()->get('dbConf.'.$dbid);
//		$pre = $pre['dbEnums']['default'];
//		$pre = $pre['dbEnums']['dbgrpForLog'];
		$pre = 'db_kkrpt';
		$this->tbname = $pre.'.tb_crond_log';
		return \Sooh\DB\Broker::getInstance($dbid);
	}
	/**
	 * 建数据库表
	 */
	public function ensureCrondTable()
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array())->send(__FUNCTION__);
		}else{
			$this->getDB()->ensureObj($this->tbname, array(
				'ymdh'=>'bigint not null default 0','taskid'=>'varchar(64) not null',
				'lastStatus'=>'varchar(512)','lastRet'=>'tinyint not null default 0','isManual'=>'tinyint not null default 0',
				'ymdhis'=>'bigint not null default 0'
			),array('ymdh','taskid'));
		}
	}
	/**
	 * 删除过于久远的执行状态记录（默认半年）
	 */
	public function remoreCrondLogExpired($dayExpired=190)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('dayExpired'=>$dayExpired))->send(__FUNCTION__);
		}else{
			$dt = \Sooh\Base\Time::getInstance()->getInstance()->timestamp(-$dayExpired);
			$this->getDB()->delRecords($this->tbname, array('ymdh<'=>date('YmdH',$dt)));
		}
	}	
}
