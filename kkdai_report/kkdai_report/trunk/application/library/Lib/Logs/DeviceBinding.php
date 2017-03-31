<?php
namespace Lib\Logs;
/**
 * 设备绑定信息
 *
 * @author simon.wang
 */
class DeviceBinding {
	public $deviceId='';
	public $userId='';
	public $contractId='0';
	public $contractData='';
	public $alias='';
	public $ip='';
	/**
	 * 从数据库中读出数据
	 * @param \Sooh\DB\Base\KVObj $obj
	 */
	public function loadFromRecord($obj)
	{
		$this->userId = $obj->getField('userId');
		$this->contractId =  $obj->getField('contractId');
		$this->deviceId =  $obj->getField('deviceId');
		$this->alias =  $obj->getField('deviceIdAlias');
		$this->ip = $obj->getField('ip');
	}
	/**
	 * 
	 * @param \Sooh\DB\Base\KVObj $sys
	 * @param DeviceBinding $rNew
	 * @param string $deviceIdAlias
	 * @return boolean
	 */
	public function saveNew($sys,$rNew,$deviceIdAlias)
	{
		$sys->setField('userId', empty($rNew->userId)?'':$rNew->userId);
		$sys->setField('extraData', (empty($rNew->contractData)?'':json_encode($rNew->contractData)));
		$sys->setField('contractId', empty($rNew->contractId)?'0':$rNew->contractId);
		$sys->setField('deviceIdAlias', empty($deviceIdAlias)?'':$deviceIdAlias);
		try{
			\Sooh\DB\Broker::errorMarkSkip(\Sooh\DB\Error::duplicateKey);
			$dt = \Sooh\Base\Time::getInstance();
			$sys->setField('ymd',$dt->YmdFull);
			$sys->setField('hhiiss',$dt->his);
			$sys->setField('ip',$rNew->ip);
			$sys->setField('extraRet', '');
			$sys->update();
			$sys->flgNewCreate=true;

		}catch(\ErrorException $e){
			$sys->reload();
			$this->loadFromRecord($sys);
			if($sys->exists()===false){
				error_log('error create new device log:'.$e->getMessage()."\n".$e->getTraceAsString());
				return false;
			}
		}
		return true;
	}
	/**
	 * 
	 * @param \Sooh\DB\Base\KVObj $sys
	 * @param DeviceBinding $rNew
	 * @param type $deviceIdAlias
	 */
	public function update($sys,$rNew,$deviceIdAlias)
	{
		if(!empty($rNew->contractData)){
			$sys->setField('extraData', json_encode($rNew->contractData));
		}
		if(!empty($rNew->userId)){
			$sys->setField('userId', $rNew->userId);
		}
		if(!empty($rNew->contractId)){
			$sys->setField('contractId', $rNew->contractId);
		}
		if(!empty($deviceIdAlias)){
			$sys->setField('deviceIdAlias', $deviceIdAlias);
		}
		$dt = \Sooh\Base\Time::getInstance();
		$sys->setField('ymd',$dt->YmdFull);
		$sys->setField('hhiiss',$dt->his);
		$sys->setField('ip',$rNew->ip);
		$sys->update();
	}
}
