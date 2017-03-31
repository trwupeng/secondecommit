<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: member_logging.php 25246 2011-11-02 03:34:53Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

define('NOROBOT', TRUE);

if(!in_array($_GET['action'], array('login', 'logout'))) {
	showmessage('undefined_action');
}

//登录重定向
if($_GET['action'] == 'login'){
    if(!defined('IN_MOBILE')){
        if ($_GET['inajax'])
            include template('common/header_ajax');
        $redirectUrl = '';
        echo '<script type="text/javascript" reload="1">window.location.href=\'' . $_G['config']['kkd']['url'] . $_G['config']['kkd']['user']['login']['web'] . '?url=\'+encodeURIComponent(window.location.href)+\'\';</script>';
        if ($_GET['inajax'])
            include template('common/footer_ajax');
        dexit();
    }else{
        //屏蔽app端的登录页面
        if(checkmobile() && $_GET['channel']){
            var_log('app 拦截登录页...');
            if(strtolower($_GET['channel']) == 'android'){
                $appcode = <<<html
                window.KKD_Control.gotoLogin();
html;
            }else if(strtolower($_GET['channel']) == 'ios'){
                $appcode = <<<html
document.addEventListener('WebViewJavascriptBridgeReady', function(event){
        var bridge = event.bridge;
        bridge.init(function(message, responseCallback) {
            var data = { 'Javascript Responds':'Wee!' }
            responseCallback(data)
        });
        //js 发送消息给ios
        //调用登录方法
        var loginData = {
            data : 'gologin'
        }
        bridge.send(loginData, function(responseData) {
            log('JS got response', responseData)
        });
    }, false);
html;
            }
            if($_GET['inajax']){
                $js = <<<html

html;
                echo $js;
                dexit();
            }else{
                $js = <<<html
            <!--app 互动代码 start-->
<script>
history.go(-1);
console.log('app event start ...');
$appcode
</script>
<!--app 互动代码 end-->
html;
                echo $js;
            }
        }
        //echo '<script type="text/javascript" reload="1">window.location.href=\''.$_G['config']['kkd']['url'].$_G['config']['kkd']['user']['login']['h5'].'?url=\'+window.location.href+\'\';</script>';
    }
}



$ctl_obj = new logging_ctl();
$ctl_obj->setting = $_G['setting'];
$method = 'on_'.$_GET['action'];
$ctl_obj->template = 'member/login';
$ctl_obj->$method();

?>