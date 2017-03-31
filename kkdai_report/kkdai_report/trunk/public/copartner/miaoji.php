<?php
//

if(empty($_SERVER['REMOTE_ADDR'])){
	die('error');
}
define ('SOOH_INDEX_FILE', 'copartner.php');

$_REQUEST['copartnerAbs']='Miaoji';
//$_REQUEST['contractId']='999920151105123456';
$_REQUEST['deviceType']=!isset($_REQUEST['deviceType'])?'imei':$_REQUEST['deviceType'];
$_REQUEST['deviceId']=!isset($_REQUEST['deviceId'])?'236423566':$_REQUEST['deviceId'];
$_REQUEST['extraData']=!isset($_REQUEST['extraData'])?'渠道提供的需要额外处理的数据':$_REQUEST['extraData'];
//$_REQUEST['deviceTypeAlias']='taid';
//$_REQUEST['deviceIdAlias']='12345688';
$_REQUEST['__VIEW__']='json';
$action = 'hold';
$reqeustReal = new Yaf_Request_Simple("CLI", 'Index', 'loger', $action, $_REQUEST);

include __DIR__.'/../index.php';