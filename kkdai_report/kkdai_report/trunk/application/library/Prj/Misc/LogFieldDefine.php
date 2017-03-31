<?php
namespace Prj\Misc;
/**
 * 日志的字段定义
 *
 * @author simon.wang
 */
class LogFieldDefine {
	public static $define=[
		'Index/Dev/test'=>[	'desc'=>'开发测试',
			'maintype'=>'','subtype'=>'',
			'target'=>'','num'=>'','ext'=>'',
			'narg1'=>'','narg2'=>'','narg3'=>'',
			'sarg1'=>'','sarg2'=>'','sarg3'=>'','ret'=>''
		],
		//----------------------------------------------app启动相关
		'start_up'=>[	'desc'=>'app启动',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>'','ret'=>''
		],
		'wake_up'=>[	'desc'=>'app唤醒',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>'','ret'=>''
		],
		'ver_chk_error'=>[	'desc'=>'版本检查失败',
			'maintype'=>'','subtype'=>'联网方式',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'ver_dl_error'=>[	'desc'=>'版本下载失败',
			'maintype'=>'','subtype'=>'联网方式',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'start_page'=>[	'desc'=>'启动页加载成功',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'start_skip'=>[	'desc'=>'启动页被跳过',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'start_ok'=>[	'desc'=>'app启动成功，进入首页',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		//------------------------------------------------注册流程
		'register_page'=>[	'desc'=>'用户点击进入注册页',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'register_smssend'=>[	'desc'=>'注册获取验证码',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'register_btn'=>[	'desc'=>'用户点击注册按钮（注册前）',
			'maintype'=>'','subtype'=>'短信验证码',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'register_done'=>[	'desc'=>'注册成功',
			'maintype'=>'','subtype'=>'短信验证码',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		//------------------------------------------------登入流程，验证码？
		'login_page'=>[	'desc'=>'用户点击登入注册页',
			'maintype'=>'','subtype'=>'密码头两位',
			'target'=>'品牌','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'login_btn'=>[	'desc'=>'用户点击登入按钮（登入前）',
			'maintype'=>'','subtype'=>'密码头两位',
			'target'=>'品牌','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'login_done'=>[	'desc'=>'登入成功',
			'maintype'=>'','subtype'=>'密码头两位',
			'target'=>'品牌','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		//------------------------------------------------绑卡流程
		'bind_page'=>[	'desc'=>'用户进入绑卡页面',
			'maintype'=>'','subtype'=>'密码头两位',
			'target'=>'品牌','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'bind_card'=>[	'desc'=>'用户进入输入银行卡页面',
			'maintype'=>'','subtype'=>'之前输入的姓名',
			'target'=>'品牌','num'=>'身份证后8位','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'bind_card_sms'=>[	'desc'=>'准备提交验证码请求',
			'maintype'=>'','subtype'=>'银行卡头6位',
			'target'=>'品牌','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'bind_card_ret'=>[	'desc'=>'绑卡结果',
			'maintype'=>'','subtype'=>'验证码',
			'target'=>'品牌','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>'','ret'=>'失败提示'
		],
		'bind_card_pwd'=>[	'desc'=>'设置支付密码成功',
			'maintype'=>'','subtype'=>'',
			'target'=>'品牌','num'=>'','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		//------------------------------------------------购买流程
		'wares_page'=>[	'desc'=>'商品明细页面',
			'maintype'=>'货架id','subtype'=>'',
			'target'=>'商品id','num'=>'手机号','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'wares_need_recharges'=>[	'desc'=>'用户点击充值',
			'maintype'=>'货架id','subtype'=>'钱包余额',
			'target'=>'商品id','num'=>'用户想购买的金额','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		'wares_buybtn'=>[	'desc'=>'用户点击购买',
			'maintype'=>'货架id','subtype'=>'',
			'target'=>'商品id','num'=>'用户想购买的金额','ext'=>'系统版本号',
			'narg1'=>'横屏幕宽','narg2'=>'横屏幕高','narg3'=>'',
			'sarg1'=>'设备标示1','sarg2'=>'设备标示2','sarg3'=>''
		],
		//----------------------------------------------------html 打开页面
		'html_enter'=>[	'desc'=>'进入HTML页面',
			'maintype'=>'','subtype'=>'',
			'target'=>'页面标示','num'=>'','ext'=>'browser-agent',
			'narg1'=>'页面宽度','narg2'=>'页面高度','narg3'=>'',
			'sarg1'=>'','sarg2'=>'','sarg3'=>'','ret'=>'http_referer'
		],
		//homepage,记录http-refer
		//spreadshort,记录http-refer
		//spreadlong,记录http-refer
	];
}
