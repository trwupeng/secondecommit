<?php
    /**
     * 
     *@author wu.peng
     * Date: 2016/9/23
     * Time: 1:42
     * demo只是提供一个接口对接编写的思路，具体接口对接商户技术以自身项目的实际情况来进行接口代码的编写。
     */
   
    /**
     * 接口版本，新浪支付接口文档参数
     */
    define("sinapay_version", "1.0");//接口版本
    /**
     * 商户号，由新浪支付提供
     */
    define("sinapay_partner_id", "200004227922");//收银台模式商户号
    /**
     * 商户接口字符集，新浪支付接口文档参数
     */
    define("sinapay_input_charset", "utf-8");//接口字符集编码
    /**
     * 商户签名类型，新浪支付接口文档参数
     */
    define("sinapay_sign_type", "RSA");//签名类型
    /**
     * sftp上传文件，文件存放地址
     */
   // define("sinapay_file_path", "../view/server/php/files/");//file_path
    /**
     * 商户签名私钥，由商户自己生成
     */
    define("sinapay_rsa_sign_private_key", dirname(__File__) . "/../key/rsa_sign_private.pem");//签名私钥
   // define("sinapay_rsa_sign_private_key",$sinapay_rsa_sign_private_key);
    /**
     * 商户验证签名公钥，由新浪提供
     */
   define("sinapay_rsa_sign_public_key", dirname(__File__) . "/../key/rsa_sign_public.pem");//验证签名公钥
  // define("sinapay_rsa_sign_public_key",$sinapay_rsa_sign_public_key);
    /**
     * 新浪支付特殊参数加密，公钥，由新浪支付提供
     */
    define("sinapay_rsa_public__key", dirname(__File__) . "/../key/rsa_public.php");//加密公钥
    //define("sinapay_rsa_public__key",$sinapay_rsa_public__key);
    /**
     *异步回调地址，商户自定义自己系统的回调地址
     */
   // define("sinapay_notify_url", "http://139.196.46.136/notify-api/controller/notifyurl.php");
    /**
     *同步返回地址，商户自己定义自己系统的同步返回地址
     */
   // define("sinapay_return_url", "http://139.196.46.136");
    /**
     * 网关地址，接口文档参数
     */
    define("sinapay_mgs_url", "https://testgate.pay.sina.com.cn/mgs/gateway.do");//会员类网关地址
    define("sinapay_mas_url", "https://testgate.pay.sina.com.cn/mas/gateway.do");//订单类网关地址
    /**
     *
     **/
    define("sinapay_debug_status", true);//true 开启日志记录  false 关闭日志记录
    /**
     * sftp参数配置
     */
    //sftp地址
   // define("sinapay_sftp_address", "222.73.39.37");
   //sftp端口号
   // define("sinapay_sftp_port", "50022");
    //sftp登录名
    //define("sinapay_sftp_Username", "200004595271");
    //sftp登录私钥
   // define("sinapay_sftp_privatekey", dirname(__File__) . "/../key/id_rsa");
    //sftp登录公钥
   // define("sinapay_sftp_publickey", dirname(__File__) . "/../key/id_rsa.pub");
    //sftp上传目录
    //define("sinapay_sftp_upload_directory", dirname(__File__) . "/upload");

?>
