<?php
/**
 * php专用，内部service，客户端请跳过
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class ServiceController extends \Prj\ServiceCtrl {

//	public function init()
//	{
//		//parent::init();
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'trace');
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\TextAll(),'error');
//		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 2),'evt');
//		$log = \Sooh\Base\Log\Data::getInstance('c');
//		$log->clientType=0;
//		$log->deviceId = $_SERVER['REMOTE_ADDR'];
//		$log->contractId='0';
//		$log->evt =$this->_request->getModuleName().'/'.$this->_request->getControllerName().'/'.$this->_request->getActionName();
//		$log->isLogined=0;
//		$log->userId= $_SERVER['SERVER_NAME'].':'. getmypid();
//
//		\Lib\Services\SessionStorage::setStorageIni();
//	}
	
	public function callAction()
	{
		$this->indexAction();
	}
	public function indexAction()
	{
		\Sooh\Base\Ini::getInstance()->viewRenderType('json');
		$class = $this->_request->get('service');
		$method = $this->_request->get('cmd');
		$class = explode('\\', $class);
		$class = ucfirst(array_pop($class));
		$logData = \Sooh\Base\Log\Data::getInstance();
		$logData->mainType = $class;
		$logData->subType = $method;
		$logData->target= "$class/$method";
		$logData->ret = "service:$class/$method";
if('rpcservices'!=$class)error_log("RPC_called:$class/$method from {$_SERVER['REMOTE_ADDR']}");
		$appKey=null;
		if($class==ucfirst(\Sooh\Base\Rpc\Broker::serviceNameForRpcManager)){
			$appKey = \Sooh\Base\Ini::getInstance()->get('RpcConfig.key');
			if(empty($appKey)){
				return $this->error('service-key-missing ',503);
			}
		}
		if($this->checkSign($class,$appKey)==false){
			return;
		}
		$args =  json_decode($this->_request->get('args'),true);
		$this->exec_real($args,$method,$class);
	}
}
