<?php
// evt_reg.php 用于记录用户注册框的输入情况
$_page = $_GET['source'];//页面标识，spider 蜘蛛网
switch ($_GET['lt']){//最后激活的输入框
	case 'a':$_lastForm='user';break;
	case 'b':$_lastForm='pwd';break;
	case 'c':$_lastForm='code';break;
default :$_lastForm='none';break;
}
$_reged=$_GET['reged']-0;//是否成功注册
$_sendcode=$_GET['sendcode']-0;//是否点击过发送验证码

$clientType = $_GET['channel'];//客户端类型

$_usr=$_GET['a']-0;//关闭时用户名
$_pwd=$_GET['b']-0;//关闭时密码
$_validcode=$_GET['c']-0;//关闭时验证码

error_log("[Trace_evt_reg]$_page\t$clientType\t$_reged\t$_lastForm\t$_sendcode\t$_usr\t$_pwd\t$_validcode");
