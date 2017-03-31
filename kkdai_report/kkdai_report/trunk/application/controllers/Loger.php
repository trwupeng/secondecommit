<?php
/**
 * 提供给外部系统记录日志
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class LogerController extends \Prj\ServiceCtrl{
	/**
	 * 不校验登入情况
	 */
	protected function onInit_chkLogin()
	{
		//不校验登入情况
	}
	/**
	 * session初始化
	 */
	protected function initSession()
	{
		$act = strtolower($this->_request->getActionName());
		if($act==='hold'){
			return array('deviceId-ignore','userId');
		}
		$rpc = \Prj\BaseCtrl::getRpcDefault('SessionStorage');
		\Lib\Services\SessionStorage::setStorageIni();
		\Sooh\Base\Session\Data::getInstance( \Lib\Services\SessionStorage::getInstance($rpc));
		return array(\Sooh\Base\Session\Data::getSessId(),'userId');
	}
	/**
	 * （网页版）记录打开页面的情况
	 * 
	 * @input string page 页面名（标示）
	 * @input string httpReferer httpReferer
	 * @sample {api_uri}&page=intro.html&httpReferer=www.baidu.com
	 * @return {"code":200,"msg":""} 正常情况
	 */
	public function openpageAction()
	{
		$this->loger = \Sooh\Base\Log\Data::getInstance();
		$this->loger->clientType=$this->_request->get('clientType')-0;
		$this->loger->evt='openpage';
		$this->loger->target = $this->_request->get('page');
		$this->loger->userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$this->loger->ext = $this->_request->get('httpReferer');
		
		$tmpDevice = $this->onStartupLogReceived($this->loger->userId, null);
		if($this->loger->evt=='startup'){
			if($tmpDevice){
				$tmpContractData = $tmpDevice->getField('extraData');
				$tmpContractId=$tmpDevice->getField('contractId',true).'';
			}else{
				$tmpContractData='';
				$tmpContractId='0';
			}
			$this->_view->assign('contractId',  $tmpContractId);
			$this->_view->assign('contractData',  $tmpContractData);
		}
		
		$this->_view->assign('code','200');
		$this->_view->assign('msg','');
	}
	protected function getArgBigint($k)
	{
		return $this->_request->get($k,0);
	}
	/**
	 * 记录设备信息，启动的事件中，输出contractId
	 * @param type $UserId
	 * @param type $extraData
	 * @return \Lib\Logs\Device
	 */
	protected function onStartupLogReceived($UserId,$extraData,$contractId=0)
	{
		$tmpDevice=null;
		if($this->loger->evt=='start_up' || $this->loger->evt=='wake_up'){
			$this->_view->assign('contractId',  '0');
			
			$finds = [$this->loger->sarg1,$this->loger->sarg2];
			foreach($finds as $tmp){
				if(!empty($tmp)){
					list($type,$sn) = explode(':', $tmp);
					$tmpDevice = \Lib\Logs\Device::ensureOne($type, $sn,$UserId,$contractId,$extraData);
					if($tmpDevice){
						$tmpContractId=$tmpDevice->getField('contractId',true).'';
						if(strlen($tmpContractId)>3){
							$this->_view->assign('contractId',  $tmpContractId);
							break;
						}
					}
				}
			}

			$this->_view->assign('sysMsg',\Prj\Misc\Evt::msgOnAppStart());
			$this->_view->assign('clientPatch', \Prj\Misc\ClientPatch::forStartup($this->loger->clientType,$this->loger->clientVer));
		}

		return $tmpDevice;
	}
	
	public function apprefreshAction()
	{
		$this->loger->clientType=$this->_request->get('clientType')-0;
		$this->loger->clientVer=$this->_request->get('clientVer');		
		$this->_view->assign('clientPatch', \Prj\Misc\ClientPatch::forStartup($this->loger->clientType,$this->loger->clientVer));
		$this->_view->assign('sysMsg',\Prj\Misc\Evt::msgOnAppStart());
	}
	/**
	 * 记录app的日志的接口，支持输出的字段参看相关日志说明文档
	 * @output {"code":200,"msg":""} 一般记录请求
	 * @output {"code":200,"msg":"","contractId":0}  启动日志记录请求
	 */
	public function applogAction()
	{
		\Sooh\Base\Ini::getInstance()->viewRenderType('json');
		$this->loger = \Sooh\Base\Log\Data::getInstance();
		$accountId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$tmp = $this->_request->get('accountId');
		if(!empty($tmp)){
			if($tmp==$accountId){
				$this->loger->userId=$accountId;
				$this->loger->isLogined=1;
			}else{
				$this->loger->userId=$accountId;
				$this->loger->isLogined=2;
			}
		}else{
			$this->loger->userId=$tmp;
			$this->loger->isLogined=0;
		}
		
		
		$this->loger->opcount=$this->_request->get('opcount')-0;
		$this->loger->clientType=$this->_request->get('clientType')-0;
		$this->loger->clientVer=$this->_request->get('clientVer');
		$this->loger->contractId=$this->getArgBigint('contractId');
		$this->loger->evt=$this->_request->get('evt');
		$this->loger->mainType=$this->_request->get('mainType');
		$this->loger->subType=$this->_request->get('subType');
		$this->loger->target=$this->_request->get('target');
		$this->loger->num=$this->_request->get('num',0);
		$this->loger->ext=$this->_request->get('ext');
		$this->loger->ret=$this->_request->get('ret');
		$this->loger->narg1=$this->getArgBigint('narg1')-0;
		$this->loger->narg2=$this->getArgBigint('narg2')-0;
		$this->loger->narg3=$this->getArgBigint('narg3');
		$this->loger->sarg1=$this->_request->get('sarg1');
		$this->loger->sarg2=$this->_request->get('sarg2');
		$this->loger->sarg3=$this->_request->get('sarg3');
		$this->loger->userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		$this->loger->ext = $this->_request->get('httpReferer');

		$this->onStartupLogReceived($this->loger->userId, null);

		
		$this->_view->assign('code','200');
		$this->_view->assign('msg','');
	}
	/**
	 * 用于记录推广渠道的设备，后面注册算该渠道的
	 * 
	 * @param string $copartnerAbs 
	 * @param string $contractId
	 * @param string $deviceType idfa,imei,ect.
	 * @param string $deviceId
	 * @param string $extraData 推广渠道自定义数据
	 */
	public function holdAction()
	{
		$deviceType = $this->_request->get('deviceType');
		$deviceId=$this->_request->get('deviceId');
		$deviceTypeAlias = $this->_request->get('deviceTypeAlias');
		$deviceIdAlias=$this->_request->get('deviceIdAlias');
		if(empty($deviceType)){
			return $this->returnError('deviceType_missing', 400);
		}
		$sys = \Prj\Copartner\Copartner0::getByAbsOrId($this->_request->get('copartnerAbs'), $this->_request->get('contractId'));
		$sys->hold($deviceType, $deviceId, $this->_request->get('extraData'),$deviceTypeAlias,$deviceIdAlias);
		$ret = $sys->onReturnForHold();
		if($ret){
			foreach($ret as $k=>$v){
				$this->_view->assign($k,$v);
			}
		}
	}
}
