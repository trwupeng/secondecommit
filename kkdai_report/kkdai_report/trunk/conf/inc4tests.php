<?php
require_once '/var/www/vendor/autoload.php';
if(!function_exists('myloader')){
	function myloader($class)  
	{  
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
				include __DIR__.'/../application/library/'.implode('/', $r).'.php';
				return true;
			}else{
				return false;
			}
		}
	}
	spl_autoload_register('myloader'); 
	include __DIR__.'/globals.php';
}
