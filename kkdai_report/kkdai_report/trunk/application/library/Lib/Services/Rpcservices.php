<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Rpcservices {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return CheckinBook
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			self::$_instance = new Rpcservices;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 *
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;
	public function fetchini($service)
	{
		if($this->rpc===null){
			$javaService = ['paygw'];
			$cmp = strtolower($service);
			$ini = \Sooh\Base\Ini::getInstance()->get('RpcConfig');
			if(in_array($cmp, $javaService)){
				unset($ini['force']);
				$ini['protocol'] = '\Prj\Misc\JavaService';
				$ini['urls']=\Sooh\Base\Ini::getInstance()->get('payGW');
			}else{
				unset($ini['force']);
			}
			return $ini;
		}else{
			\Sooh\Base\Log\Data::error('Rpcservices should never be called by rpc');
			throw new \Sooh\Base\ErrException('Rpcservices should never be called by rpc');
			//return $this->rpc->initArgs(array('arg1'=>$arg1))->send();//call('CheckinBook/'.__FUNCTION__, array($withBonus,$userOrAccountId));
		}
	}
}
