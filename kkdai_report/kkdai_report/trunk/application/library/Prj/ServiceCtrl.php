<?php
namespace Prj;
class ServiceCtrl  extends \Prj\BaseCtrl {


	public function init()
	{
		parent::init();
		$render = $this->ini->viewRenderType();
		if($render==='html'){
			$this->ini->viewRenderType('json');
		}
	}
	/**
	 * 记录日志的时候的 deviceid对应远程服务器 和 userid对应本地服务器
	 * @return array array(deviceid,userid)
	 */
	protected function initSession()
	{
		if('SessionStorage' == $this->_request->get('service')){
			\Lib\Services\SessionStorage::setStorageIni();
			error_log('session setted');
			return array('','');
		}else{
			return array(
				$_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'], 
				$_SERVER['SERVER_NAME'].':'. getmypid()
			);
		}
	}	
	protected function error($msg,$code=400)
	{
		\Sooh\Base\Log\Data::getInstance()->ret = $msg;
		$this->_view->assign('code',$code);
		$this->_view->assign('msg',$msg);
	}
	protected function checkSign($service,$appKey=null)
	{
		$dt = $this->_request->get('dt');
		$sign = $this->_request->get('sign');
		
		if($appKey===null){
			$arr = \Sooh\Base\Rpc\Broker::getRpcIni($service);
			$appKey= $arr['key'];
		}
		if(\Sooh\Base\Ini::getInstance()->get('released')){
			$dur = abs($dt-time());
			if($dur>60){
				$this->error('sign error');
				return false;
			}
			if($sign != md5($dt.$appKey)){
				$this->error('sign error');
				return false;
			}
		}else{
			if(empty($sign)){
				error_log('trace:sign of serviceCtrl skipped');
			}elseif($sign != md5($dt.$appKey)){
				$this->error('sign error');
				return false;
			}
		}
		return true;
	}
	protected function sort_params($class,$method,$params)
	{
		if(empty($params) || sizeof($params)==1){
			return $params;
		}
		$class = new \ReflectionClass($class);
		$f=$class->getmethod($method);
		$orders = $f->getParameters();
		$ret=[];
		foreach($orders as $o){
			$ret[] = $params[$o->name];
		}
		return $ret;
	}
	protected function exec_real($args=null,$method=null,$service=null)
	{
		if($service===null){
			$service = $this->serviceNameByCtrl();
		}
		if($method===null){
			$method = $this->_request->getActionName();
		}
		if($args===null){
			$args = $this->_request->get('args');
			if(!empty($args)){
				$args = json_decode($args,true);
			}
		}
		if (file_exists(__DIR__.'/../Lib/Services/'.ucfirst($service).'.php')){
			$classname = '\\Lib\\Services\\'.ucfirst($service);
			if(method_exists($classname, $method)){
				try{
					$args = $this->sort_params($classname, $method, $args);
					$obj = $classname::getInstance();
					$ret = call_user_func_array(array($obj,$method), $args);
					$this->returnOK();
					$this->_view->assign('data',$ret);
					//$this->_view->assign('lastsql', \Sooh\DB\Broker::lastCmd());
					//$this->_view->assign('lastsql', base64_encode(\Sooh\DB\Broker::lastCmd()));
					\Sooh\Base\Log\Data::getInstance()->ret = "done";
				}  catch (\Exception $e){
					if(!empty($e->customData)){
						$this->_view->assign('data',$e->customData);
					}
					if(is_a($e, '\Sooh\DB\Error')){
						$code=500;
					}else{
						$code = $e->getCode()-0;
						if($code==0){
							$code=500;
						}
					}
					$this->error($e->getMessage(),$code);
				}
			}else{
				$this->error('service-cmd error:'.$method.' of '.$classname);
			}
		}else{
			$this->error('service-name error:'.$service);
		}
	}
	
	protected function serviceNameByCtrl()
	{
		$class = get_called_class();
		return substr($class,0,-10);
	}
}
