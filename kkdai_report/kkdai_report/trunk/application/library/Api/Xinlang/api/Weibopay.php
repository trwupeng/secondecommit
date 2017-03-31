<?php
namespace Api\Xinlang\api;
/**
 *@author wu.peng
 * 
 * @param Date: 2016/9/23
 * @param Time: 4:00
 * demo只是提供一个接口对接编写的思路，具体接口对接商户技术以自身项目的实际情况来进行接口代码的编写。
 */


@include_once (dirname ( __File__ ) . "/../config/conf.php");
class Weibopay {
	/**
	 * getSignMsg 计算签名
	 *
	 * @param array $pay_params
	 *        	计算签名数据
	 * @param string $sign_type
	 *        	签名类型
	 * @return string $signMsg 返回密文
	 */
	function getSignMsg($pay_params = array(), $sign_type,$_input_charset) {
		$params_str = "";
		$signMsg = "";

		foreach ( $pay_params as $key => $val ) {
			if ($key != "sign" && $key != "sign_type" && $key != "sign_version" && isset ( $val ) && @$val != "") {
				$params_str .= $key . "=" . $val . "&";
			}
		}
		$params_str = substr ( $params_str, 0, - 1 );
		//var_log($params_str,'parms############');
		
		//$params_str=mb_convert_encoding($params_str,$_input_charset);
		switch (@$sign_type) {
			case 'RSA' :
				//self::write_log("RSA参与签名运算数据".$params_str);
				$priv_key = file_get_contents ( sinapay_rsa_sign_private_key );
				$pkeyid = openssl_pkey_get_private ( $priv_key );
			    openssl_sign ( $params_str, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1 );
				openssl_free_key ( $pkeyid );
				$signMsg = base64_encode ( $signMsg );
				//var_log($signMsg,'signMSG##############');
				//self::write_log("RSA计算得出签名值：".$signMsg);
				break;
			case 'MD5' :
			default :
//				$params_str = $params_str . @sinapay_md5_key;
//				self::write_log("MD5参与签名运算数据".$params_str);
//				$signMsg = strtolower ( md5 ( $params_str ) );
//				self::write_log("MD5计算得出签名值：".$signMsg);
				break;
		}
		return $signMsg;
	}
	/**
	 * 通过公钥进行rsa加密
	 *
	 * @param type $name
	 *        	Descriptiondata
	 *        	$data 进行rsa公钥加密的数必传
	 *        	$pu_key 加密用的公钥 必传
	 *          $_input_charset 字符集编码
	 * @return 加密好的密文
	 */
	function Rsa_encrypt($data, $public_key,$_input_charset) {
		$encrypted = "";
		//$data=mb_convert_encoding($data,$_input_charset);
		$cert = file_get_contents ($public_key );
		//$cert=sinapay_rsa_public__key;
		$pu_key = openssl_pkey_get_public ( $cert ); // 这个函数可用来判断公钥是否是可用�?
		openssl_public_encrypt ( $data, $encrypted, $pu_key ); // 公钥加密
		$encrypted = base64_encode ( $encrypted ); // 进行编码
		return $encrypted;
	}
	/**
	 * [createcurl_data 拼接模拟提交数据]
	 *
	 * @param array $pay_params
	 * @return string url格式字符
	 */
	function createcurl_data($pay_params = array()) {
		$params_str = "";
		foreach ( $pay_params as $key => $val ) {
			if (isset ( $val ) && ! is_null ( $val ) && @$val != "") {
				$params_str .= "&" . $key . "=" . urlencode ( urlencode ( trim ( $val ) ) );
			}
		}
		if ($params_str) {
			$params_str = substr ( $params_str, 1 );
		}
		return $params_str;
	}
	/**
	 * checkSignMsg 回调签名验证
	 *
	 * @param array $pay_params 参与签名验证的数据
	 * @param string $sign_type  签名类型
	 * @param $_input_charset   签名字符集编码
	 * @return boolean  签名结果
	 */
	function checkSignMsg($pay_params = array(), $sign_type,$_input_charset) {
		$params_str = "";
		$signMsg = "";
		$return = false;
		foreach ( $pay_params as $key => $val ) {
			if ($key != "sign" && $key != "sign_type" && $key != "sign_version" && ! is_null ( $val ) && @$val != "") {
				$params_str .= "&" . $key . "=" . $val;
			}
		}
		if ($params_str) {
			$params_str = substr ( $params_str, 1 );
		}
		//验证签名demo需要支持多字符集所以此处对字符编码进行转码处理,正常商户不存在多字符集问题
		//$params_str=mb_convert_encoding($params_str,$_input_charset,"UTF-8");
		//$this->write_log("本地验证签名数据".$params_str);
		//$this->write_log("本地获取签名".$pay_params ['sign']);
		switch (@$sign_type) {
			case 'RSA' :
				$cert = file_get_contents ( sinapay_rsa_sign_public_key );
				//$cert=sinapay_rsa_sign_public_key;
				$pubkeyid = openssl_pkey_get_public ( $cert );
				$ok = openssl_verify ( $params_str, base64_decode ($pay_params ['sign']), $cert, OPENSSL_ALGO_SHA1 );
				$return = $ok == 1 ? true : false;
				openssl_free_key ( $pubkeyid );
				break;
			default :
				break;
		}
		return $return;
	}
	/**
	 * 文件摘要算法
	 */
	function md5_file($filename) {
		return md5_file ( $filename );
	}
	/**
	 * sftp上传企业资质
	 * sftp upload
	 * @param $file 上传文件路径
	 * @return false 失败   true 成功
	 */
// 	function sftp_upload($file,$filename) {
// 		$strServer = sinapay_sftp_address;
// 		$strServerPort = sinapay_sftp_port;
// 		$strServerUsername = sinapay_sftp_Username;
// 		$strServerprivatekey = sinapay_sftp_privatekey;
// 		$strServerpublickey = sinapay_sftp_publickey;
// 		$resConnection = ssh2_connect ( $strServer, $strServerPort );
// 		if (ssh2_auth_pubkey_file ( $resConnection, $strServerUsername, $strServerpublickey, $strServerprivatekey )) 
// 		{
// 			$resSFTP = ssh2_sftp ( $resConnection );
// 			file_put_contents ( "ssh2.sftp://{$resSFTP}/upload/".$filename, $file);
// 			if (! copy ( $file, "ssh2.sftp://{$resSFTP}/upload/$filename" )) {
// 				return false;
// 			}
// 			return true;
// 		}
// 		return false;
// 	}
	/**
	 * [curlPost 模拟表单提交]
	 *
	 * @param string $url  请求网关地址
	 * @param string $data  请求数据key=value格式
	 * @param $_input_charset 字符集编码
	 * @return string $data
	 */
	function curlPost($url, $data,$_input_charset) {
		//self::write_log("请求sina网关地址".$url);
		//self::write_log("请求sina网关数据".$data);
		//$data = http_build_query($data);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		//self::write_log("请求新浪网关返回内容:".mb_convert_encoding(urldecode($data),"UTF-8"));
		//由于json转数组使用了json_decode所以需要将非UTF-8的内容强转为UTF-8字符集
		//return mb_convert_encoding(urldecode($data),"UTF-8");
		return urldecode($data);
	}
	/**
	 * 日志记录
	 *
	 * @param unknown $msg
	 * @return boolean
	 */
// 	function write_log($msg) {
// 		if(sinapay_debug_status){
// 		$result=error_log( date ( "[YmdHis]" ) ."\t" . $msg . "\r\n", 3, '../'. date ( "Ymd" ) . '.log' );
// 			return $result;
// 		}else
// 		{
// 			return false;
// 		}

//	}

	/**
	 * 获取IP范例，具体以实现代码已自身网络架构来进行编写
	 * @return string
	 */
// 	function get_ip(){
// 		if (isset($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], "unknown"))
// 			$ip = $_SERVER['HTTP_CLIENT_IP'];
// 		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], "unknown"))
// 			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
// 		else if (isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
// 			$ip = $_SERVER['REMOTE_ADDR'];
// 		else if (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
// 			$ip = $_SERVER['REMOTE_ADDR'];
// 		else $ip = "";
// 		return ($ip);
// 	}

	
}
?>