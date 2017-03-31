<?php
//php.ini 设置 yaf.use_spl_autoload=on
define("APP_PATH",  dirname(__DIR__)); /* 指向public的上一级 */
if(!defined('SOOH_INDEX_FILE')){
	define ('SOOH_INDEX_FILE', 'index.php');
}
define('SOOH_ROUTE_VAR','__');

ob_start();
include dirname(__DIR__) .'/conf/globals.php';
$ini = \Sooh\Base\Ini::getInstance();

if(40!==\Sooh\Base\Ini::getInstance()->get('deploymentCode')){///////////////////////////////////////////////////////--debug
	error_log("-------------------------------------------------------tart:route=".$_GET['__']." cmd=".(empty($_GET['cmd'])?"":$_GET['cmd'])." pid=".  getmypid());
	error_log('----cookie:' . json_encode($_COOKIE));
	\Sooh\Base\Tests\Bomb::$flg=empty($_REQUEST['__'])?@array_shift(explode('?',$_SERVER['REQUEST_URI'])):$_REQUEST['__'];
}///////////////////////////////////////////////////////--debug

$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");

$dispatcher = $app->getDispatcher();
if(!empty($reqeustReal)){
	$dispatcher->setRequest( $reqeustReal );
}

$view = \SoohYaf\SoohPlugin::initYafBySooh($dispatcher);
$dispatcher->returnResponse(TRUE);
try{
	
	$response = $app->run();
}catch(\ErrorException $e){
	$code = $e->getCode();
	$viewType=$ini->viewRenderType();
	if ($viewType === 'html' && $code === 401) {
		header('Location:' . \Sooh\Base\Ini::getInstance()->get('uriBase')['www'] . '/error.html');
	}elseif($code===300 || $code===301) {
        if ($viewType === 'wap') {
            header('Location:' . \Sooh\Base\Ini::getInstance()->get('uriBase')['www'] . '/manage/manager/login?__VIEW__=wap&errTrans=' . urlencode($e->getMessage()));
        } else {
            $view->assign('statusCode', $code);
            $view->assign('msg', $e->getMessage());
            if ($viewType === 'html') {
                $ini->viewRenderType('json');
            }
            $response = new Yaf_Response_Http();
            $req = $dispatcher->getRequest();
            $response->setBody($view->render($req->getControllerName() . '/' . $req->getActionName() . '.' . $viewType . '.phtml'));
        }
    } elseif($code === 408 && ($viewType === 'html' || $viewType === 'wap')){
        header('Location:' . \Sooh\Base\Ini::getInstance()->get('uriBase')['www'] . '/forbid.html');
	} else {
        $code = $code == 408 ? 300 : $code;
		$view->assign('statusCode',$code);
		$view->assign('message',$e->getMessage());
		error_log("Error Caught at index.php:".$e->getMessage()."\n".\Sooh\DB\Broker::lastCmd()."\n".$e->getTraceAsString()."\n");
		$response=new Yaf_Response_Http();
		$req = $dispatcher->getRequest();
		$response->setBody($view->render($req->getControllerName().'/'.$req->getActionName().'.'.$viewType.'.phtml'));
	}
}

if($ini->viewRenderType()==='json') {
	header('Content-type: application/json');
}
if($response){
	$response->response();
	$end = ob_get_contents();
	ob_flush();
}


\Sooh\Base\Ini::registerShutdown(null, null);
\Sooh\Base\Tests\Bomb::onShutdown();
if(40!==\Sooh\Base\Ini::getInstance()->get('deploymentCode')){///////////////////////////////////////////////////////--debug
	error_log("====================================================================end:route=".$_GET['__']." cmd=".$_GET['cmd']." pid=".  getmypid());
}///////////////////////////////////////////////////////--debug

