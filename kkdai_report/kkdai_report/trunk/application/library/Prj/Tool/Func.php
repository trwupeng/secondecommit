<?php
namespace Prj\Tool;

class Func
{
    /**
     * 发post请求
     */
    public static function curl_post($url,$post=[],$time=5)
    {
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $time );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     * @param string $url 请求URL地址
     * @param array  $params 请求参数
     * @param string $method 请求类型
     * @param bool   $multi 图片信息
     * @param array  $extheaders 扩展的包头信息
     * @return mixed
     */
    public static function request($url, $params = [], $method = 'GET', $multi = false, $extheaders = []) {
        if (!function_exists('curl_init'))
            E('Need to open the curl extension');
        $method  = strtoupper($method);
        $ci      = curl_init();
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ci, CURLOPT_TIMEOUT, 3);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = (array) $extheaders;
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params)) {
                    if ($multi) {
                        foreach ($multi as $key => $file) {
                            $params[$key] = '@' . $file;
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                        $headers[] = 'Expect: ';
                    } else {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?')
                        . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }
        var_log( $url, 'url' );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);
        if ($headers) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ci);
        curl_close($ci);
        error_log('#func::request#url:'.$url);
        error_log('#func::request#response:'.substr($response,0,500).'...');
        return $response;
    }
    
    public static function changeTimeType($seconds){
    	if ($seconds > 3600){
    		$hours = intval($seconds/3600);
    		$minutes = $seconds % 3600;
    		$time = $hours.":".gmstrftime('%M:%S', $minutes);
    	}else{
    		$time = gmstrftime('%H:%M:%S', $seconds);
    	}
    	return $time;
    }
}