<?php
function myloader($class)  
{  
	error_log('myload:'.$class);
	$r = explode('\\', $class);
	if($r[0]=='\\')array_shift($r);
	if(sizeof($r)==1){
		if(file_exists($r[0].'.php')) {
			//error_log('try auto-load : '.$r[0].'.php');
			include $r[0].'.php';
			return true;
		} else {
			return false;
		}
	}else{
		if(in_array($r[0], $GLOBALS['CONF']['localLibs'])){
			include APP_PATH.'/application/library/'.implode('/', $r).'.php';
			return true;
		}else{
			return false;
		}
	}
}
spl_autoload_register('myloader'); 
