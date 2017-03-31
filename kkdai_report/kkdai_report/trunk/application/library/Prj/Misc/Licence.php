<?php
namespace Prj\Misc;

/**
 * 各类许可协议相关接口
 *
 * @author simon.wang
 */
class Licence {
	public static  function version($type=null)
	{
		$map = ['register'=>1,'invest'=>1,'recharges'=>1,'binding'=>1];
		if($type){
			return $map[$type];
		}else{
			return $map;
		}
	}
	
	public static function register($ver=null)
	{
		if(empty($ver)){
			$ver = self::version(__FUNCTION__);
		}
		return file_get_contents(__DIR__.'/txt/'.__FUNCTION__.'/'.$ver.'.phtml');
	}
	public static function binding($ver=null)
	{
		if(empty($ver)){
			$ver = self::version(__FUNCTION__);
		}
		return file_get_contents(__DIR__.'/txt/'.__FUNCTION__.'/'.$ver.'.phtml');
	}
	public static function invest($arr,$ver=null)
	{
		if(empty($ver)){
			$ver = self::version(__FUNCTION__);
		}
		$find=[];
		$rep=[];
		foreach($arr as $k=>$v){
			$find[]='{$'.$k.'}';
			$rep[]=$v;
		}
		$tmp = file_get_contents(__DIR__.'/txt/'.__FUNCTION__.'/'.$ver.'.phtml');
		return str_replace($find, $rep, $tmp);
	}
	
	public static function recharges($arr,$ver=null)
	{
		if(empty($ver)){
			$ver = self::version(__FUNCTION__);
		}
		$find=[];
		$rep=[];
		foreach($arr as $k=>$v){
			$find[]='{$'.$k.'}';
			$rep[]=$v;
		}
		$tmp = file_get_contents(__DIR__.'/txt/'.__FUNCTION__.'/'.$ver.'.phtml');
		return str_replace($find, $rep, $tmp);
	}
}
