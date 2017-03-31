<?php
if(!empty($_SERVER['REMOTE_ADDR'])){
	die('error');
}
define ('SOOH_INDEX_FILE', 'crond.php');
parse_str($argv[1],$argv);
if(empty($argv['__'])){
	$argv['__']='Index/crond/run';
}
$route = explode('/',$argv['__']);
if(sizeof($route)==2){
	array_unshift($route, 'Index');
}
$argv['__VIEW__']='json';
$reqeustReal = new Yaf_Request_Simple("CLI", $route[0], $route[1], $route[2], $argv);

include __DIR__.'/../public/index.php';
