<?php
namespace Api\Umeng;

/**
 * @package Api\Umeng
 * @author  wu.peng
 */
class Umeng
{
    //umeng 获取用户密钥的接口
    const API_SEND_URL = 'http://api.umeng.com/authorize';
   
    const API_ACCOUNT = 'zhaoyuguang@kkdai.com.cn';
   
    const API_PASSWORD = 'zyg315';
    
    
    
   
    
    /**
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function get_token()
    {
      
        $postArr = [
            'email'    => self::API_ACCOUNT,
            'password'       => self::API_PASSWORD,
  
        ];

        $result = $this->curlPost(self::API_SEND_URL, $postArr);
        if(!empty($result)){
            return  $result;
        }
        if(($result['code']==200)){
            return  $result;
        }else{
            sleep(30);
            $result = $this->curlPost(self::API_SEND_URL, $postArr);
            if($result['code']==200){
                return  $result;
            }else{
                return  false;
            }
        }
    }
    
    
    /**
     * @return string
     * @throws \Sooh\Base\ErrException
     */
    public function auth_token($auth_token)
    {
        $url='http://api.umeng.com/apps';
        //$auth_token='6yZ9WBSsChOJiirf4ahx';
        $per_page=10;
        $page=1;
        
        $postArr = [
            'per_page'    => $per_page,
            'page'       => $page,
             'auth_token'=>$auth_token
    
        ];
    
        $result = $this->curlGet($url, $postArr);

        if($result['code']!=403 || !empty($result)){
            return  $result;
        }else{
            sleep(30);
            $result = $this->curlGet($url, $postArr);
            if($result['code']!=403 || !empty($result)){
                return  $result;
            }else{
                return false;
            }
        }
    }
    
    
    /**
     * 获取所以渠道用户
     *
     * @string appkey app标识(android,ios)
     *
     * */
    public  function  channels($appkey,$auth_token,$date){
        
        $url="http://api.umeng.com/channels";
        
       // var_log($date,'3333333>>>>>>');
        
        if(empty($date)){
            $dt_instance = \Sooh\Base\Time::getInstance();
            $date = $dt_instance->yesterday('Y-m-d');
        }
         
       // var_log($date,'44444>>>>>>');
        
        $per_page=500;
        $page=1;
        
        $getArr=[
            'appkey'=>$appkey,
            'date'=>$date,
            'per_page'=>$per_page,
            'page'=>$page,
            'auth_token'=>$auth_token
        ];
        
        $result = $this->curlGet($url, $getArr);

        //var_log($result,'result<<<<<<<<<<<<<');
        
        if(!empty($result)){
            return  $result;
        }else{
            sleep(3);
            $result = $this->curlGet($url, $getArr);
            if(!empty($result)){
                return  $result;
            }else{
                return false;
            }
        }
    }
   
  
    
    /**
     * 通过CURL发送HTTP请求  (get请求)
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    private function curlGet($url, $postFields, $timeout = 2000, $connection_timeout = 2000)
    {
        $postFields = http_build_query($postFields);
        $url=$url.'?'.$postFields;
        //var_log($url,'url>>>>>>>>>>');
        
        $ch         = curl_init();
       // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

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
        curl_close($ch);
        //var_log($result,'ch<<<<<<<<<<<<<');
        
        return $this->execResult($result);
    }

    /**
     * 通过CURL发送HTTP请求  (post请求)
     * @param string $url        //请求URL
     * @param array  $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url, $postFields, $timeout = 2000, $connection_timeout = 2000)
    {    
       
        $postFields = http_build_query($postFields);

        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
           
     
        
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
     
        curl_close($ch);
        
       // var_log($result,'ch<<<<<<<<<<<<<');
        return $this->execResult($result);
    }
    
    /**
     * 处理返回值
     * @param string $result curl result
     * @return mixed
     */
    private function execResult($result)
    {
        $result = json_decode($result,true);
        return $result;
    }
    
}