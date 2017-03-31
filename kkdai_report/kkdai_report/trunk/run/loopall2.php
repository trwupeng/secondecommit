<?php
$tmp = explode('.',$argv[1]);
if(sizeof($tmp)!=2){
	die('usage: php loopall.php task-path/task-classname'."\n");
}


if (!empty($argv[2])) {
	$start = $argv[2];
	$start = explode('-', $start);
	if (sizeof($start) != 3){
		error_log('日期格式：2016-2-3');
		exit;
	}
	if (!checkdate($start[1]-0, $start[2]-0, $start[0]-0)){
		error_log('日期格式：2016-2-3');
		exit;
	}
	$start = mktime(0, 0, 0, $start[1]-0, $start[2]-0, $start[0]-0);
}else {
	$start = mktime(0,0,0,7,1,2014);
}

$end = time();
//$end = $start+86400*3;
$cmdid=$argv[1];
while($start<$end){
	$ymd = date('Ymd',$start);
	$cmd = "php /var/www/licai_php/public/crond.php \"__=crond/run&task=$cmdid&ymdh=$ymd\"";
//	echo "$cmd\n";
	system($cmd);
	$start+=86400;
}
echo "\n";
