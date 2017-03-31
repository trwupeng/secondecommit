<?php
namespace Prj;
use Sooh\Base\ErrException;

/**
 * 基础Ctrl
 *     session初始化，并设置日志
 *     提供returnError() && returnOk() && errorLogException()
 *     提供验证码图片接口：validimgAction() && isValidCodeOK()
 *     提供：static::getRpcDefault()
 *     检查是否维护时间 onInit_chkMaintainTime()
 *     提供 initForUriDefault() 设置当前环境，用于生成站内url
 */
class BaseCtrl  extends \Yaf_Controller_Abstract {

//	protected function getFromRaw()
//	{
//		$s = file_get_contents('php://input');
//		if(!empty($s)){
//			parse_str($s,$inputs);
//			return $inputs;
//		}else{
//			return $inputs=array();
//		}
//	}
	public function init()
	{
		$this->ini = \Sooh\Base\Ini::getInstance();
		if($this->_request->isPost()){
			$this->onInit_chkMaintainTime();
		}

		list($deviceId,$uid)=$this->initSession();
		
		\Sooh\Base\Log\Data::addWriter(new \Prj\Misc\Logerror(\Prj\Misc\Logerror::usedForTrace),'trace');
		\Sooh\Base\Log\Data::addWriter(new \Prj\Misc\Logerror(\Prj\Misc\Logerror::usedForError),'error');
		\Sooh\Base\Log\Data::addWriter(new \Sooh\Base\Log\Writers\Database('dbgrpForLog', 2),'evt');
		$this->loger = \Sooh\Base\Log\Data::getInstance('c');
		$this->loger->clientType=$this->_request->get('clientType')-0+$this->_request->get('clienttype');
		$this->loger->clientVer=$this->_request->get('clientVer').'';
		$this->loger->deviceId = $deviceId;
		$this->loger->contractId='0';
		$this->loger->evt =$this->_request->getModuleName().'/'.$this->_request->getControllerName().'/'.$this->_request->getActionName();
		$this->loger->isLogined=0;
		$this->loger->userId=$uid;
		$this->initForUriDefault();
	}
	/**
	 * loger
	 * @var \Sooh\Base\Log\Data 
	 */
	protected $loger=null;
	/**
	 * 发现错误，设置返回的错误信息
	 * @param string $msg
	 * @param int $code 默认400
	 */
	protected function returnError($msg='',$code=400)
	{
		\Sooh\Base\Log\Data::getInstance()->ret = $msg;
        var_log($msg,'warning:['.get_called_class().']');
		//TODO: 50x错误的报警
		$this->_view->assign('code',$code);
		if(!empty($msg)){
			$this->_view->assign('msg',$msg);
		}
	}
	/**
	 * 正常结束，设置返回的信息
	 * @param string $msg
	 * @param int $code
	 */
	protected function returnOK($msg='',$code=200)
	{
		$this->loger->ret=$msg?$msg:'ok';
		$this->_view->assign('code',$code);
		if(!empty($msg)){
			$this->_view->assign('msg',$msg);
		}
	}
	/**
	 * 异常的默认处理流程：写trace,更新loger->ret, 另外，如果需要，写error
	 * @param type $e
	 * @param type $needsNotify
	 */
	protected function errorLogException($e, $needsNotify='')
	{
		$this->loger->trace($needsNotify.'#'.$e->getMessage()."\n".$e->getTraceAsString());
		if($needsNotify){
			$this->loger->error($needsNotify.' #'.$e->getMessage());
		}
		$this->loger->ret='error:'.$e->getMessage();
	}
	/**
	 * 验证码的action 通用化
	 */
	public function validimgAction()
	{
		//php composer.phar require "gregwar/captcha"
		$builder = new \Gregwar\Captcha\CaptchaBuilder;
		$builder->build(110,40);
		header('Content-type: image/jpeg');
		$this->ini->viewRenderType('echo');
		$builder->output();
		\Sooh\Base\Session\Data::getInstance()->set('validImg', $builder->getPhrase());
	}
	
	protected function isValidCodeOK($code)
	{
		$sessionData=\Sooh\Base\Session\Data::getInstance();
		return $code === $sessionData->get('validImg');
	}
	/**
	 * @return array array(deviceid,userid)
	 */
	protected function initSession()
	{
//		error_log('initSession called');
		$rpc = \Prj\BaseCtrl::getRpcDefault('SessionStorage');
		if($rpc==null){
//			error_log('sessionStorage direct');
			\Lib\Services\SessionStorage::setStorageIni();
		}
//		else{
//			error_log('sessionStorage by rpc');
//		}
		\Sooh\Base\Session\Data::getInstance( \Lib\Services\SessionStorage::getInstance($rpc));
		$deviceId = \Sooh\Base\Session\Data::getSessId();
		$uid = \Sooh\Base\Session\Data::getInstance()->get('accountId');
		return array($deviceId,$uid);
	}
	
	public static function getRpcDefault($serviceName)
	{
		if($serviceName==='Rpcservices' ||$serviceName==='SessionStorage'){
			return null;
		}
		$flg = \Sooh\Base\Ini::getInstance()->get('RpcConfig.force');
		if($flg!==null){
			if($flg){
				error_log('force rpc for '.$serviceName);
				return \Sooh\Base\Rpc\Broker::factory($serviceName);
			}else{
				error_log('no rpc for '.$serviceName);
				return null;
			}
		}else{
			error_log('try rpc for '.$serviceName);
			return \Sooh\Base\Rpc\Broker::factory($serviceName);
		}
	}
	
	/**
	 * 检查是否维护时间，如果是，抛出异常（不支持任何写动作）
	 * @throw \ErrorException
	 */
	protected function onInit_chkMaintainTime()
	{
		$now = \Sooh\Base\Time::getInstance()->timestamp();
		$chk = $this->ini->get('maintainTime');
		if ( $chk[1]> $now && $chk[0]<= $now){
			throw new \ErrorException(\Prj\ErrCode::errMaintainTime);
		}
	}
	public function ttAction()
	{
		\Sooh\Base\Ini::getInstance()->viewRenderType('json');
		error_log('sdfsdfsdf');
	}
	protected function getUriBase()
	{
		$tmp = $this->_request->getBaseUri();
		return $tmp=='/'?'':$tmp;
	}
	protected function initForUriDefault()
	{
		$request = $this->_request;
		$this->ini->initGobal(array('request'=>array('action'=>$request->getActionName(),
												'controller'=>lcfirst($request->getControllerName()),
												'module'=>lcfirst($request->getModuleName()),
												'baseUri'=>$this->getUriBase()
												)
				));
	}

    protected function debug()
    {
        if(\Sooh\Base\Ini::getInstance()->get('debug')!==1)
        {
            $this->returnError('请开启debug模式');
            return false;
        }
        return true;
    }


    /**
	 *
	 * @var \Sooh\Base\Ini 
	 */
	protected $ini;


    /**
     * 检查用户在快快贷的登录状态
     */
    public function checkkkdLogin($jskey , $customerId = null){
        $api = \Sooh\Base\Ini::getInstance()->get('uriBase')['kkd'];
        if(empty($api))return $this->returnError('api missing');
        if($customerId){
            $url = $api.'/app/isLogin.do?token='.$jskey.'&customerId='.$customerId;
        }else{
            $url = $api.'/isLogin.do?jskey='.$jskey;
        }
        $str = \Prj\Misc\Funcs::curl_post($url);
        if(empty($str))$str = file_get_contents($url);
        $ret = json_decode($str , true);
        if(empty($ret)){
            error_log('###url:'.$url);
            throw new \ErrorException('connenct_failed');
        }
        if($ret['code'] !== 0)throw new \ErrorException('unlogin');
        return [
            'code' => 200,
            'data' => $ret,
        ];
    }
}
