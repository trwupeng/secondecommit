<?php

class GitlabController extends \Prj\BaseCtrl {

    public  function  init(){
    
      parent::init();
      $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }
    
    protected $db_rpt;
    protected $db_p2p;
    
     public function indexAction() {
         
         $ob=$_GET['userId'];
         
         $url="http://git.kuaikuaidai.com/users/sign_in";
         
         $result=\Api\Catchgitlab\Catchgitlab::curlGet($url);
         
         if($result==false){
          $result=\Api\Catchgitlab\Catchgitlab::curlGet($url);
         }
         
         $cookie=$result[0];
        // var_log("GetLoginCookie: " . $cookie);
         $result=$result[1];
        // var_log("GetLoginHtml: " . $result);
         
         $value = "value=\"";
         //获取authenticity_token
         $tokenLength = "name=\"authenticity_token\"";
         //获取要查找信息的下标
         $authenticityTokenIndex = strpos($result, $tokenLength);
         //获取从查找信息下标开始的字符串
         $lastStr = substr($result, ($authenticityTokenIndex + strlen($tokenLength)));
         $valueLastStr = substr($lastStr, (strpos($lastStr, $value) + strlen($value)));
       
         $aa = strpos($valueLastStr, "\"");
         $authenticityToken = substr($valueLastStr, 0, $aa);
        // var_log("AuthenticityToken: " . $authenticityToken);
       
         if($authenticityToken){
           $postArr=[
               'authenticity_token'=>$authenticityToken,
               'user[login]'=>"dong.wang",
               'user[password]'=>"kkdai123"
           ];
          // var_log($postArr, "PostLoginParams");
           
           $rem = \Api\Catchgitlab\Catchgitlab::curlPost($url, $postArr, $cookie);
           
           //var_log($rem, "PostLoginHtml");
           $cookies = $rem[0];
          // var_log($cookies, "PostLoginCookie");
           $rem = $rem[1];
          // var_log($rem, "PostLoginHtml");
           
           foreach ($cookies as $value) {
               $c = explode( ': ', $value);
               $kv = explode( '=', $c[1]);
               $v = explode(';',$kv[1])[0];
               setcookie($kv[0],$v,null,'/','kuaikuaidai.com');
           }
           
           $location=$this->checkuser($ob);
           
         //  header("Location: http://git.kuaikuaidai.com/kkd_sh/kkdai_report/graphs/master/commits");
          
       }
     }
     
     public function checkuser($userId){
       
        if(in_array($userId,$this->kkdai_sh)){
            
          header("Location: http://git.kuaikuaidai.com/kkd_sh/kkdai_report/graphs/master/commits");
          return 0;
         
        }elseif(in_array($userId, $this->android_kkd_app)){
            
          header("Location: http://git.kuaikuaidai.com/android/kkd_app/graphs/master/commits");
          return 0;
         
        }elseif(in_array($userId, $this->ios_app_ios)){
            
         header("Location: http://git.kuaikuaidai.com/ios/app_ios/graphs/master/commits");
         return 0;
         
        }elseif(in_array($userId, $this->ios_loan_app)){
            
         header("Location: http://git.kuaikuaidai.com/ios/loan_app/graphs/master/commits");
         return 0;
         
        }elseif(in_array($userId, $this->server_phoenix)){
            
         header("Location: http://git.kuaikuaidai.com/server/phoenix/graphs/master/commits");
         return 0;
         
        }elseif(in_array($userId, $this->server_nest)){
            
        header("Location: http://git.kuaikuaidai.com/server/nest/graphs/master/commits");
        return 0;
         
        }elseif(in_array($userId, $this->server_sparrow)){
            
        header("Location: http://git.kuaikuaidai.com/server/server_sparrow/graphs/master/commits");
        return 0;
         
        }elseif(in_array($userId, $this->server_common)){
            
       header("Location: http://git.kuaikuaidai.com/server/server_common/graphs/master/commits");
       return 0;
         
        }
        
     }
     
     
     protected  $kkdai_sh=[
         "卢秋鹤","吴鹏",
         "戴杰","李梦竹","李连奇",
         "梁言庆","汤高航","沈海燮",
         "王伟","王阳","童益丰","钟继业",
         "陶满","马龙龙"
     ];
     
     protected  $android_kkd_app=[
         "张有传","赵闯",
         "马天一"
     ];
     
     protected  $ios_app_ios=[
         "孔子龙"
     ];
     
     protected $ios_loan_app=[
         "徐利营","赵宇光"
     ];
     
     protected  $server_phoenix=[
         "周豪"
     ];
     
     protected $server_nest=[
         "向兰英"
     ];
     
     protected $server_sparrow=[
         "孙瑞明"
     ];
     
     protected  $server_common=[
         "宋威","岳化梦","李向南","李增芳","李晓苑",
         "杨丽云","汪杰","熊彩阳","金超","牛鹏凯",
     ];
     
     
    }
    