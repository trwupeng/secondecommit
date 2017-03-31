<?php
namespace Lib\Services;
/**
 * rpc 服务器管理
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Sample {
	protected static $_instance=null;
	/**
	 * 
	 * @param \Sooh\Base\Rpc\Broker $rpcOnNew
	 * @return Sample
	 */
	public static function getInstance($rpcOnNew=null)
	{
		if(self::$_instance===null){
			$c = get_called_class();
			self::$_instance = new $c;
			self::$_instance->rpc = $rpcOnNew;
		}
		return self::$_instance;
	}
	/**
	 * 
	 * @var \Sooh\Base\Rpc\Broker
	 */
	protected $rpc;
	public function somfunc($args1)
	{
		if($this->rpc!==null){
			return $this->rpc->initArgs(array('args1'=>$args1,))->send(__FUNCTION__);
		}else{
			return ["chk1"=>$args1];
		}
	}
}
