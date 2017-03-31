<?php
$GLOBALS['CONF']['path_php'] = 'php';
$GLOBALS['CONF']['path_console'] = APP_PATH.'/run/crond.php';
//$GLOBALS['CONF']['released'] = false;
$GLOBALS['CONF']['ServerId']=3;
$GLOBALS['CONF']['version'] = '1.0';
$GLOBALS['CONF']['deploymentCode'] = '10';//10:dev | 20:test | 30:pre | 40:online
$GLOBALS['CONF']['CookieDomainBase']='.miaojilicai.com';
//$GLOBALS['CONF']['SignKeyForService'] = 'asgdfw4872hfhjksdhr8732trsj';
//$GLOBALS['CONF']['hostsOfMssqlAPI'] = array(
//	'default'=>array('http://192.168.56.130/API001/index.php?__=service/call',)
//);
$GLOBALS['CONF']['RpcConfig'] = array(
	'force'=>0,
	'key'=>'asgdfw4872hfhjksdhr8732trsj','protocol'=>'HttpGet',
	'urls'=>array('http://admapp.miaojilicai.com//index.php?__=service/call',)
);
//By Hand
$GLOBALS['CONF']['payGW'] = array(
    'http://139.129.29.52:8080/backgrount_payment/service/service',
);
$GLOBALS['CONF']['logerror'] = array(
	1=>['/var/www/logs/prj_error.txt'],
	2=>['/var/www/logs/prj_trace.txt']
);
$GLOBALS['CONF']['maintainTime'] = array(mktime(23,59,59,1970,12,30)-60,mktime(23,59,59,1971,12,30),);

$GLOBALS['CONF']['dbConf'] = array(
	'default' => array('host' => 'localhost', 'user' => 'p2p', 'pass' => '123456', 'type' => 'mysql', 'port' => '3306',
		'dbEnums' => array('default' => 'db_p2p', 'devices' => 'db_devices', 'dbgrpForLog' => 'db_logs')),//根据模块选择的具体的数据库名
	'oauth' => array('host' => 'localhost', 'user' => 'p2p', 'pass' => '123456', 'type' => 'mysql', 'port' => '3306',
		'dbEnums' => array('default' => 'db_oauth',)),
);

$GLOBALS['CONF']['dbByObj']=array(
	'default'=>array(1,'default'),
	'dbgrpForLog'=>array(2,'default',),
	'session'=>array(1,'default'),
	'account'=>array(1,'default'),
	'monitor'=>array(1,'default'),
	'smscode'=>array(1,'default'),
	'devices'=>array(2,'default'),
	'manage'=>array(1,'default'),
	'oauth'=>array(2,'oauth'),
);

$GLOBALS['CONF']['sync'] = [
    'baseUrl' => 'http://oa.kuaikuaidai.com/seeyon',
    'userName' => 'restuser',
    'password' => 'rest123456',
];

$GLOBALS['CONF']['uriBase']=array(
	'www'=>'http://wwwapp.miaojilicai.com',
	'oauth'=>'http://wwwapp.miaojilicai.com',
);

$GLOBALS['CONF']['SMSConf'] = 'ChuangLan';

$GLOBALS['CONF']['TestKey'] = 'O2eWC5ExmwL47Ku8MRBcq35kvhAGRVDiZQvak8z';

$GLOBALS['CONF']['debug'] = 1;

$GLOBALS['CONF']['localLibs']=array('Lib','Prj');

function var_log($var,$prefix=''){
	if(is_a($var, "\Exception")){
		$s = $var->__toString();
		if(strpos($s,'[Sooh_Base_Error]')){
			if(class_exists('\Sooh\DB\Broker',false)){
				$sql = "\n".\Sooh\DB\Broker::lastCmd()."\n";
			}else{
				$sql = "\n";
			}
			error_log(str_replace('[Sooh_Base_Error]',$sql,$s));
		}else{
			error_log($prefix.$var->getMessage()."\n".$s);
		}
	}else{
		error_log($prefix."\n".var_export($var,true));
	}
}

include "/var/www/vendor/autoload.php";