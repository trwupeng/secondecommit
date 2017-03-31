<?php
/**
 * User: token.tong
 * Date: 2017/03/15
 */
class GitStatController extends \Prj\ManagerCtrl{
    public function init(){
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }
    public function indexAction () 
    {
    	$userId = $this->session->get('managerId');
    	list($loginName,$cameFrom) = explode('@', $userId);
    	if ( !\Prj\Data\Manager::isSpecialLoginUser($loginName) )
    	{
    		throw new \ErrorException(\Prj\ErrCode::errNoRights,301);
    	}
    	
    	$output = self::post( "http://www.kuaikuaidai.com/phoenix-manager/j_spring_security_check",
					array( 'j_username' => 'richanggongzuo',
    						'j_password' => 'richanggongzuo' ) );
    	$lines = explode( "\r\n", $output );
    	var_log( count($lines), 'lines' );
    	list($proto, $code ) = explode( ' ', $lines[0] );
    	if ( 302 != $code )
    	{
    		throw new \ErrorException( \Prj\ErrCode::errCommonError, 301 );
    	}
    	$cookieHeader = 'Set-Cookie: ';
    	$locationHeader = 'Location: ';
    	$cookieKey = '';
    	$cookieValue = '';
    	$location = '';
    	foreach( $lines as $k=>$line )
    	{
    		$pos = strpos( $line, $cookieHeader );
    		if ( 0 == $pos )
    		{
    			$cookie = substr( $line, strlen($cookieHeader), strlen($line)-strlen($cookieHeader) );
    			$arrFields = explode( '; ', $cookie );
    			foreach( $arrFields as $k=>$field )
    			{
    				list($key, $value ) = explode( '=', $field );
    				if ( 'aliyungf_tc' == $key || 'JSESSIONID' == $key )
    				{
    					setcookie($key,$value,null,'/','kuaikuaidai.com');
    				}
    			/*
    				if ( 'JSESSIONID' == $key )
    				{
    					$cookieKey = $key;
    					$cookieValue = $value;
    					break;
    				}
    			*/
    			}
    		}
    		else
    		{
    			$pos = strpos( $line, $locationHeader );
    			if ( 0 == $pos )
    			{ 
    				$location = substr( $line, strlen($locationHeader), strlen($line)-strlen($locationHeader) );
    			}
    		}
    	}
    	
//    	\Sooh\Base\Ini::getInstance()->viewRenderType('echo');

    	$this->_view->assign( 'jump_url', 'http://www.kuaikuaidai.com/phoenix-manager/index' );
   /*
    	setcookie($cookieKey,$cookieValue,time()+3600*24*365,'/','.kuaikuaidai.com');
    	setcookie($cookieKey,$cookieValue,time()+3600*24*365,'/','.www.kuaikuaidai.com');
    	setcookie($cookieKey,$cookieValue,time()+3600*24*365,'/','www.kuaikuaidai.com');
    */
//    	$redir = 'Location: ' . 'http://www.kuaikuaidai.com/phoenix-manager/index'; 
//    	header( $redir );
//    	var_log( $redir, 'redir' );
    
    }
    
    function post( $url, $args, $arrHeaders = null, $arrCookies = null )
    {
    	var_log( 'start post', 'post' );
    	$ch = curl_init();
    	if($ch){
    		curl_setopt($ch, CURLOPT_URL, $url);
    			
    		if(is_array($arrHeaders) && !empty($arrHeaders)){
    			curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders);
    		}
    		if(is_array($arrCookies) && !empty($arrCookies)){
    			$arrCookies = str_replace('&', '; ', http_build_query($arrCookies));
    			curl_setopt($ch, CURLOPT_COOKIE,$arrCookies);
    		}
    		$tmp= http_build_query($args);
    		curl_setopt($ch, CURLOPT_POST, 1);
    		if(strlen($tmp)<1000){
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $tmp);
    		}else{
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    		}
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_HEADER, 1);
    		
    		$output = curl_exec($ch);
    		$err=curl_error($ch);
    		curl_close($ch);
    		
    		var_log( $output, 'output' );
    		var_log( $err, 'err' );
    			
    		return $output;
    	}else{
    		return "curl init failed";
    	}
    }
    
    

}