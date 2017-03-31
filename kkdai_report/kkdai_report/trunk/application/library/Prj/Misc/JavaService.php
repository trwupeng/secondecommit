<?php
namespace Prj\Misc;
/**
 * Description of JavaService
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class JavaService {
	/**
	 * 实际发送请求到server,默认http-get
	 * @return mixed or null 
	 */	
	public function _send($host,$service,$cmd,$args,$dt,$sign)
	{
		$dt = \Sooh\Base\Time::getInstance()->timestamp();
		//$service = $service;
		if(is_string($args)){
			$url = $host.'?service='.$service.'&cmd='.$cmd.'&';
			$posts = array('args'=>$args,'dt'=>$dt-0,'sign'=>$sign);
            $url .= http_build_query($posts);
		}else{
            $url = $host.'?service='.$service.'&cmd='.$cmd.'&';
			$args['dt']=$dt-0;
            $args['sign']=$sign;
            $url .= http_build_query($args);
		}
		$ret = \Sooh\Base\Tools::httpGet($url);
if('rpcservices'!=$service)error_log("[RPC@".getmypid()."]".$ret." by ".$url);
		if(200==\Sooh\Base\Tools::httpCodeLast()){
			$tmp = json_decode($ret,true);
			if(is_string($tmp['data'])){
				$tmp2 = json_decode($tmp['data'],true);
				if(is_array($tmp2)){
					$tmp['data']=$tmp2;
					$ret = json_encode($tmp);
				}
			}
			return $ret;
		}else{
			return null;
		}
	}
	public function onShutdown(){}
}
