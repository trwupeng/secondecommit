<?php
namespace Lib\Logs;

/**
 * 设备使用情况记录和追踪
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Device extends \Sooh\DB\Base\KVObj {
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'devices'.($isCache?'':'');
	}
	public static function guidMaker($type,$sn)
	{
		if(empty($sn)){
			$sn = md5(microtime(true).\Sooh\Base\Tools::remoteIP());
		}else{
			$pre = substr($sn,0,5);
			if(in_array($pre, array('idfa:','imei:','mac :','md5 :'))){
				$sn=substr($sn,5);
			}
		}
		switch($type){
			case 'idfa':return 'idfa:'.str_replace(['-',':'], '', $sn);
			case 'imei':return 'imei:'.str_replace(['-',':'], '', $sn);
			case 'mac':	return 'mac_:'.str_replace(['-',':'], '', $sn);
			case 'md5': return 'md5_:'.$sn;
			case 'www': return 'www_:'.$sn;
			case 'wap': return 'wap_:'.$sn;
			case 'taid':return 'taid:'.$sn;
			default:
				error_log('error: unknown device type found:'.$type);
				$len = strlen($type);
				switch ($len){
					case 0: throw new \ErrorException('type of device not given');
					case 1: return $type.'   :'.$sn;
					case 2: return $type.'  :'.$sn;
					case 3: return $type.' :'.$sn;
					default:return substr($type,0,4).' :'.$sn;
				}
		}
	}
	/**
	 * 
	 * @param string $deviceId idfa:xxxxx
	 * @return \Lib\Logs\Device
	 */
	public static function getCopy($deviceId) {
		return parent::getCopy(array('deviceId'=>$deviceId));
	}
	
	public function getDeviceId()
	{
		return $this->pkey['deviceId'];
	}
	public $flgNewCreate=false;
	/**
	 * 
	 * @param string $type [idfa|imei|md5|mac]
	 * @param string $sn
	 * @param string $phone
	 * @param string $userId
	 * @param string $contractId
	 * @param array $extraData
	 * @param string $typeAlias [idfa|imei|md5|mac]
	 * @param string $snAlias
	 * @return \Lib\Logs\Device
	 */
	public static function ensureOne($type,$sn,$userId=null,$contractId=null,$extraData=null,$typeAlias=null,$snAlias=null)
	{
		$rNew = new \Lib\Logs\DeviceBinding;
		$rNew->contractId=$contractId;
		$rNew->userId=$userId;
		$rNew->contractData = $extraData;
		$rNew->ip = \Sooh\Base\Tools::remoteIP();
		$rOld = new \Lib\Logs\DeviceBinding;
		$rAlias = new \Lib\Logs\DeviceBinding;
		$rNew->deviceId=$rOld->deviceId = self::guidMaker($type, $sn);
		if(!empty($snAlias)){
			$rNew->alias = $rAlias->deviceId= self::guidMaker($typeAlias, $snAlias);
		}
		
		$sys=self::ensureReal($rOld, $rNew, $rAlias);
		if(!empty($snAlias)){
			self::ensureReal($rAlias, $rNew, $rOld);
		}
		
		return $sys;
	}
	/**
	 * 
	 * @param array $fields
	 * @param \Lib\Logs\DeviceBinding $rOld
	 * @param \Lib\Logs\DeviceBinding $rNew
	 * @param \Lib\Logs\DeviceBinding $rAlias
	 */
	protected static function ensureReal($rOld,$rNew,$rAlias)
	{
		$sys = static::getCopy($rOld->deviceId);
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::tableNotExists);
			$sys->load();
		}  catch (\ErrorException $e){
			if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::tableNotExists)){
				$sys->createTable ();
			}
		}

		try{
			if($sys->exists()===false){//新的设备
				if(false===$rOld->saveNew($sys, $rNew,$rAlias->deviceId)){
					return $sys;
				}
			}else{//已经存在的设备
				self::logChange($rNew, $rOld,$sys->getField('extraData',true),$sys->getField('extraRet',true));//所有者发生变化了
				$rOld->update($sys, $rNew,$rAlias->deviceId);
			}
		} catch ( \ErrorException $e) {
			error_log("error: on ensure-device:".$e->getMessage()."\n".$e->getTraceAsString());
		}
		return $sys;
	}
	/**
	 * 检查是否需要额外记录变更（使用者变更,id关联关系变更）
	 * @param \Lib\Logs\DeviceBinding $rNew
	 * @param \Lib\Logs\DeviceBinding $rOld
	 * @return bool
	 */
	protected static function logChange($rNew,$rOld,$oldExtraData,$extraRetOld)
	{
		if(
				(!empty($rOld->userId) && !empty($rNew->userId) && $rOld->userId!=$rNew->userId) 
				|| (!empty($rOld->contractId) && !empty($rNew->contractId) && $rNew->contractId!=$rOld->contractId) 
		){
			
			$chg = array(
					'deviceId'=>$rOld->deviceId,
					'deviceIdAlias'=>$rOld->alias,
					'dtChange'=>\Sooh\Base\Time::getInstance()->ymdhis(),
					'userIdOld'=>$rOld->userId,
					'extraDataOld'=>(is_scalar($oldExtraData)===false?json_encode($oldExtraData):$oldExtraData),
					'extraRetOld'=>$extraRetOld,
					'contractIdOld'=>$rOld->contractId,
					'userIdNew'=>$rNew->userId,
					'extraDataNew'=>empty($rNew->contractData)?'':json_encode($rNew->contractData),
					'extraRetNew'=>$extraRetOld,
					'contractIdNew'=>empty($rNew->contractId)?'0':$rNew->contractId,
					'ipOld'=>$rOld->ip,
					'ipNew'=>$rNew->ip,
				);
			$extraRetOld = (is_scalar($extraRetOld)===false?json_encode($extraRetOld):$extraRetOld);
			\Sooh\DB\Broker::getInstance(\PrjLib\Tbname::db_rpt)
						->addRecord(\PrjLib\Tbname::tb_device_log, $chg);
		}
	}
	/**
	 * 创建每天的日志表
	 */
	protected function createTable()
	{
		$this->db()->ensureObj($this->tbname(), array(
			'deviceId'=>'varchar(64) not null',
			'deviceIdAlias'=>'varchar(64) not null',
			'userId'=>'varchar(64) not null',
			'contractId'=>'bigint not null default 0',
			'extraData'=>'varchar(1000) not null',
			'extraRet'=>'varchar(500) not null',
			'ip'=>'varchar(32) not null',
			'ymd'=>'int not null default 0',
			'hhiiss'=>'int not null default 0',
			'iRecordVerID'=>'bigint not null default 0'
		),array('deviceId'));
	}
	protected static function splitedTbName($n,$isCache)
	{
		return 'tb_device_'.($n % static::numToSplit());
	}
}
