<?php
if(sizeof($argv)!=2){
	die('usage: php loopall.php task-withpath'."\n");
}else{
	$tmp = explode('.',$argv[1]);
	if(sizeof($tmp)!=2){
		die('usage: php loopall.php task-path/task-classname'."\n");
	}
}
$start = mktime(0,0,0,7,1,2014);
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
