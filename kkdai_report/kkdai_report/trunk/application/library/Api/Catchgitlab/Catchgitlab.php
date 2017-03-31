<?php
namespace Api\Catchgitlab;

/**
 * @package Api\Umeng
 * @author  wu.peng
 */
class Catchgitlab
{

    
    /**
     * 通过CURL发送HTTP请求  (get请求)
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    public function curlGet($url,$timeout = 2000, $connection_timeout = 2000)
    { 

        $ch         = curl_init();
      
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
      

        if (defined("CURLOPT_TIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($timeout / 1000));
        }
        if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connection_timeout);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, ceil($connection_timeout / 1000));
        }

        $result = curl_exec($ch);
        
        preg_match('/Set-Cookie:(.*);/iU',$result,$str); //正则匹配
        $cookie = $str[1]; //获得COOKIE（SESSIONID）
        
        curl_close($ch);
        //var_log($result,'ch<<<<<<<<<<<<<');
        $arr=[0=>$cookie,1=>$result];
        return  $arr;
       // return $this->execResult($result);
    }

    /**
     * 通过CURL发送HTTP请求  (post请求)
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    public function curlPost($url, $postFields, $cookie,$timeout = 2000, $connection_timeout = 2000)
    { 
        $postFields = http_build_query($postFields);
        
        $postFields="utf8=%E2%9C%93&".$postFields;
       // var_log($postFields,'post###############');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch,CURLOPT_COOKIE,$cookie);
     
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Location: http://git.kuaikuaidai.com/'
        ));
        if (defined("CURLOPT_TIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($timeout / 1000));
        }
        if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connection_timeout);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, ceil($connection_timeout / 1000));
        }
         
        $result = curl_exec($ch);
      
        preg_match_all('/Set-Cookie:(.*);/iU',$result,$str); //正则匹配
        $cookie = $str[0]; //获得COOKIE（SESSIONID）
        
        curl_close($ch);
        $arr=[0=>$cookie,1=>$result];
        return  $arr;
    }
    
    
    
}